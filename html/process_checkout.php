<?php
// 1. Đưa session_start lên đầu tiên
session_start();

// 2. Bật báo lỗi MySQLi để try-catch có thể hoạt động
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

include 'db.php';

header('Content-Type: application/json');

// Lấy dữ liệu giỏ hàng từ yêu cầu POST
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['cart']) || empty($data['cart'])) {
    echo json_encode(['success' => false, 'message' => 'Giỏ hàng không hợp lệ!']);
    exit();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để thanh toán!']);
    exit();
}

$cart = $data['cart'];
$userId = $_SESSION['user_id'];
$success = true;
$message = "Thanh toán thành công! Số lượng kho đã được cập nhật.";

mysqli_begin_transaction($conn);

try {
    // Tính tổng tiền
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    // 1. Tạo đơn hàng (Orders)
    $stmtOrder = $conn->prepare("INSERT INTO orders (user_id, total, status) VALUES (?, ?, 'Paid')");
    $stmtOrder->bind_param("id", $userId, $total);
    $stmtOrder->execute();
    $orderId = mysqli_insert_id($conn);

    foreach ($cart as $item) {
        $name = $item['name'];
        $qtyToSubtract = intval($item['quantity']);

        // Lấy số lượng hiện tại từ DB và khóa hàng (FOR UPDATE)
        $stmtProd = $conn->prepare("SELECT id, quantity FROM products WHERE name = ? FOR UPDATE");
        $stmtProd->bind_param("s", $name);
        $stmtProd->execute();
        $res = $stmtProd->get_result();
        $product = mysqli_fetch_assoc($res);

        if (!$product) {
            throw new Exception("Sản phẩm '" . $name . "' không tồn tại trong hệ thống!");
        }

        if ($product) {
            $currentStock = intval($product['quantity']);
            $newStock = $currentStock - $qtyToSubtract;
            $stmtUpdate = $conn->prepare("UPDATE products SET quantity = ? WHERE id = ?");
            $stmtUpdate->bind_param("ii", $newStock, $product['id']);
            $stmtUpdate->execute();
            
            // 2. Lưu chi tiết đơn hàng (Order Details)
            $prodId = $product['id'];
            $price = $item['price'];
            $stmtDetail = $conn->prepare("INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmtDetail->bind_param("iiid", $orderId, $prodId, $qtyToSubtract, $price);
            $stmtDetail->execute();
        }
    }
    mysqli_commit($conn);
} catch (Exception $e) {
    // Hoàn tác nếu có lỗi (ví dụ: một trong các sản phẩm bị hết hàng đột ngột)
    mysqli_rollback($conn);
    $success = false;
    $message = $e->getMessage();
}

echo json_encode(['success' => $success, 'message' => $message]);
?>