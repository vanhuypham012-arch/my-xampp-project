<?php
session_start();
header('Content-Type: application/json');

// 2. Sử dụng file kết nối chung để đồng bộ với admin.php và config.php
require_once 'config.php';

// 3. Thêm chức năng lấy danh sách sản phẩm theo Category (GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $cat = $_GET['category'] ?? '';
    $sql = "SELECT * FROM products" . ($cat ? " WHERE category = ?" : "");
    $stmt = $conn->prepare($sql);
    if ($cat) $stmt->bind_param("s", $cat);
    $stmt->execute();
    echo json_encode(mysqli_fetch_all($stmt->get_result(), MYSQLI_ASSOC));
    exit();
}

// 4. Kiểm tra quyền Admin cho các hành động thay đổi dữ liệu (POST)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Bạn không có quyền thực hiện hành động này!']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

try {
    if ($action === 'add') {
        $stmt = $conn->prepare("INSERT INTO products (name, price, quantity, category, gender, image_url, is_video) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $is_video = $input['is_video'] ? 1 : 0;
        $stmt->bind_param("sdisssi", $input['name'], $input['price'], $input['quantity'], $input['category'], $input['gender'], $input['image_url'], $is_video);
        $stmt->execute();
        echo json_encode(['success' => true, 'message' => 'Thêm sản phẩm thành công!']);

    } elseif ($action === 'edit') {
        $stmt = $conn->prepare("UPDATE products SET name=?, price=?, quantity=?, category=?, gender=?, image_url=?, is_video=? WHERE id=?");
        $is_video = $input['is_video'] ? 1 : 0;
        $stmt->bind_param("sdisssii", $input['name'], $input['price'], $input['quantity'], $input['category'], $input['gender'], $input['image_url'], $is_video, $input['id']);
        $stmt->execute();
        echo json_encode(['success' => true, 'message' => 'Cập nhật thành công!']);

    } elseif ($action === 'delete') {
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $input['id']);
        $stmt->execute();
        echo json_encode(['success' => true, 'message' => 'Xóa sản phẩm thành công!']);

    } else {
        echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
?>