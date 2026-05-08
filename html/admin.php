<?php
session_start();
require_once '../config.php'; // Sử dụng config.php chung để đồng bộ database

// 1. Kiểm tra quyền Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// 2. Xử lý đa ngôn ngữ
$lang = isset($_COOKIE['lang']) ? $_COOKIE['lang'] : 'EN';
$t_admin = [
    'EN' => [
        'dashboard' => 'Admin Dashboard',
        'edit_prod' => 'Edit Product',
        'add_prod' => 'Add New Product',
        'prod_name' => 'Product Name',
        'price' => 'Price',
        'stock' => 'Stock Quantity',
        'category' => 'Category',
        'gender' => 'Gender',
        'select_file' => 'Select File (Image/Video)',
        'keep_old' => 'Leave empty to keep old image',
        'is_video' => 'This is a Video file',
        'update_btn' => 'Update Product',
        'save_btn' => 'Save Product',
        'cancel' => 'Cancel Editing',
        'history_title' => 'Buyer History & Orders',
        'list_title' => 'Product List',
        'all' => 'All',
        'fashion' => 'Fashion',
        'makeup' => 'Makeup',
        'watch' => 'Watch',
        'orders' => 'Orders',
        'order_id' => 'ID',
        'buyer' => 'Buyer',
        'product' => 'Product',
        'qty' => 'Qty',
        'date' => 'Date',
        'status' => 'Status',
        'image' => 'Image',
        'actions' => 'Actions',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'confirm_delete' => 'Are you sure you want to delete?',
        'msg_added' => 'New product added successfully!',
        'msg_updated' => 'Product updated successfully!',
        'msg_deleted' => 'Product deleted successfully!',
        'women' => 'Women',
        'men' => 'Men'
    ],
    'VN' => [
        'dashboard' => 'Trang Quản Trị',
        'edit_prod' => 'Chỉnh Sửa Sản Phẩm',
        'add_prod' => 'Thêm Sản Phẩm Mới',
        'prod_name' => 'Tên sản phẩm',
        'price' => 'Giá tiền',
        'stock' => 'Số lượng kho',
        'category' => 'Danh mục',
        'gender' => 'Giới tính',
        'select_file' => 'Chọn tệp (Ảnh/Video)',
        'keep_old' => 'Để trống nếu giữ nguyên ảnh cũ',
        'is_video' => 'Đây là tệp Video',
        'update_btn' => 'Cập nhật sản phẩm',
        'save_btn' => 'Lưu sản phẩm',
        'cancel' => 'Hủy chỉnh sửa',
        'history_title' => 'Lịch Sử Người Mua & Đơn Hàng',
        'list_title' => 'Danh Sách Sản Phẩm',
        'all' => 'Tất cả',
        'fashion' => 'Thời trang',
        'makeup' => 'Trang điểm',
        'watch' => 'Đồng hồ',
        'orders' => 'Đơn hàng',
        'order_id' => 'Mã ĐH',
        'buyer' => 'Người mua',
        'product' => 'Sản phẩm',
        'qty' => 'SL',
        'date' => 'Ngày mua',
        'status' => 'Trạng thái',
        'image' => 'Hình ảnh',
        'actions' => 'Thao tác',
        'edit' => 'Sửa',
        'delete' => 'Xóa',
        'confirm_delete' => 'Bạn có chắc chắn muốn xóa?',
        'msg_added' => 'Thêm sản phẩm mới thành công!',
        'msg_updated' => 'Cập nhật sản phẩm thành công!',
        'msg_deleted' => 'Xóa sản phẩm thành công!',
        'women' => 'Nữ',
        'men' => 'Nam'
    ]
];
$ta = $t_admin[$lang];

$message = "";

// 2. Xử lý Lọc danh mục (Filter)
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$filter_query = "";

// 2. Xử lý Xóa sản phẩm
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Xóa sản phẩm thành công!";
    }
}

// 3. Xử lý Thêm hoặc Cập nhật sản phẩm
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity'] ?? 0); // Lấy số lượng từ form
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $is_video = isset($_POST['is_video']) ? 1 : 0;

    // 1. Mặc định dùng ảnh cũ
    $image_url = $_POST['existing_image'] ?? ''; 

    // 2. Nếu có upload file mới
    if (isset($_FILES['product_file']) && $_FILES['product_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = $is_video ? '../video/' : '../img/';
        
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $file_name = time() . '_' . basename($_FILES['product_file']['name']);
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['product_file']['tmp_name'], $target_file)) {
            $image_url = $file_name;
        }
    }

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Cập nhật sản phẩm
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("UPDATE products SET name=?, price=?, quantity=?, category=?, gender=?, image_url=?, is_video=? WHERE id=?");
        $stmt->bind_param("sdisssii", $name, $price, $quantity, $category, $gender, $image_url, $is_video, $id);
        $stmt->execute();
        header("Location: admin.php?filter=$filter&msg=updated");
        exit();
    } else {
        // Thêm mới sản phẩm
        $stmt = $conn->prepare("INSERT INTO products (name, price, quantity, category, gender, image_url, is_video) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdisssi", $name, $price, $quantity, $category, $gender, $image_url, $is_video);
        $stmt->execute();
        header("Location: admin.php?filter=$filter&msg=added");
        exit();
    }
}
// Xử lý hiển thị thông báo từ URL
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'added') $message = $ta['msg_added'];
    if ($_GET['msg'] == 'updated') $message = $ta['msg_updated'];
    if ($_GET['msg'] == 'deleted') $message = $ta['msg_deleted'];
}

// 4. Lấy dữ liệu sản phẩm để hiển thị hoặc để sửa
$edit_product = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_product = $stmt->get_result()->fetch_assoc();
}

// Tự động chọn danh mục dựa trên Filter hiện tại nếu đang thêm mới
$current_cat = $edit_product['category'] ?? ($filter !== 'all' && $filter !== 'fashion' ? $filter : 'tops');

// 5. Xây dựng câu lệnh SQL dựa trên bộ lọc
if ($filter === 'fashion') {
    $filter_query = "WHERE category IN ('tops', 'pants', 'dress', 'jacket')";
} elseif ($filter === 'makeup') {
    $filter_query = "WHERE category = 'makeup'";
} elseif ($filter === 'watch') {
    $filter_query = "WHERE category = 'watch'";
}

$products = null;
$orders = null;
if ($filter === 'orders') {
    $orders = mysqli_query($conn, "SELECT o.id, u.username, p.name as prod_name, od.quantity, od.price, o.created_at, o.status 
                                  FROM orders o 
                                  JOIN users u ON o.user_id = u.id 
                                  JOIN order_details od ON o.id = od.order_id 
                                  JOIN products p ON od.product_id = p.id 
                                  ORDER BY o.created_at DESC");
} else {
    $products = mysqli_query($conn, "SELECT * FROM products $filter_query ORDER BY id DESC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Trang Quản Trị - Fashion Club</title>
    <link rel="stylesheet" href="../css/collection.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { background: #111; color: #fff; padding-top: 100px; }
        .admin-container { max-width: 1100px; margin: 0 auto; padding: 20px; }
        .form-box { background: #222; padding: 30px; border-radius: 8px; border: 1px solid #333; margin-bottom: 50px; }
        .form-box h2 { font-family: 'Playfair Display', serif; color: #c9a96e; margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #c9a96e; font-weight: 600; }
        input, select, textarea { width: 100%; padding: 12px; margin-bottom: 15px; background: #333; border: 1px solid #444; color: #fff; border-radius: 4px; outline: none; transition: 0.3s; }
        input:focus, select:focus { border-color: #c9a96e; background: #3a3a3a; }
        .btn-submit { background: #c9a96e; color: #000; border: none; padding: 12px 30px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-submit:hover { background: #fff; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #1a1a1a; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #333; }
        th { color: #c9a96e; text-transform: uppercase; font-size: 13px; }
        .product-img { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; }
        .action-links a { color: #fff; text-decoration: none; margin-right: 15px; font-size: 13px; padding: 5px 10px; border-radius: 4px; }
        .edit-btn { background: #3498db; }
        .delete-btn { background: #e74c3c; }
        .alert { padding: 15px; background: #27ae60; color: white; margin-bottom: 20px; border-radius: 4px; }
        
        /* Style cho các Tab lọc */
        .admin-tabs { display: flex; gap: 10px; margin-bottom: 30px; border-bottom: 1px solid #333; padding-bottom: 15px; }
        .tab-link { color: #888; text-decoration: none; padding: 10px 20px; border-radius: 4px; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; }
        .tab-link:hover { background: #222; color: #fff; }
        .tab-link.active { background: #c9a96e; color: #000; font-weight: bold; }
    </style>
</head>
<body>
    <?php include '../header.php'; ?>

    <div class="admin-container">
        <h1 style="font-family: 'Playfair Display', serif; color: #c9a96e; margin-bottom: 30px;"><?php echo $ta['dashboard']; ?></h1>

        <?php if($message) echo "<div class='alert'>$message</div>"; ?>

        <!-- FORM THÊM / SỬA -->
        <div class="form-box">
            <h2><?php echo $edit_product ? $ta['edit_prod'] : $ta['add_prod']; ?></h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $edit_product['id'] ?? ''; ?>">
                <input type="hidden" name="existing_image" value="<?php echo $edit_product['image_url'] ?? ''; ?>">

                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 30px;">
                    <div>
                        <label><?php echo $ta['prod_name']; ?></label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($edit_product['name'] ?? ''); ?>" required>
                    </div>
                    <div>
                        <label><?php echo $ta['price']; ?></label>
                        <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($edit_product['price'] ?? ''); ?>" required>
                    </div>
                    <div>
                        <label><?php echo $ta['stock']; ?></label>
                        <input type="number" name="quantity" value="<?php echo htmlspecialchars($edit_product['quantity'] ?? '0'); ?>" required>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 0.7fr 1.5fr 1fr; gap: 30px; align-items: center;">
                    <select name="category" style="margin-bottom: 0;">
                        <option value="tops" <?php if($current_cat == 'tops') echo 'selected'; ?>>Áo (Tops)</option>
                        <option value="pants" <?php if($current_cat == 'pants') echo 'selected'; ?>>Quần (Pants)</option>
                        <option value="dress" <?php if($current_cat == 'dress') echo 'selected'; ?>>Váy (Dress)</option>
                        <option value="jacket" <?php if($current_cat == 'jacket') echo 'selected'; ?>>Áo khoác (Jacket)</option>
                        <option value="watch" <?php if($current_cat == 'watch') echo 'selected'; ?>>Đồng hồ (Watch)</option>
                        <option value="makeup" <?php if($current_cat == 'makeup') echo 'selected'; ?>>Trang điểm (Makeup)</option>
                    </select>
                    <select name="gender" style="margin-bottom: 0;">
                        <option value="women" <?php if(isset($edit_product) && $edit_product['gender'] == 'women') echo 'selected'; ?>><?php echo $ta['women']; ?></option>
                        <option value="men" <?php if(isset($edit_product) && $edit_product['gender'] == 'men') echo 'selected'; ?>><?php echo $ta['men']; ?></option>
                    </select>
                    <div>
                        <label><?php echo $ta['select_file']; ?></label>
                        <input type="file" name="product_file" accept="image/*,video/*" style="margin-bottom: 0; padding: 8px;">
                        <?php if($edit_product): ?><small style="color: #888;"><?php echo $ta['keep_old']; ?></small><?php endif; ?>
                    </div>
                    <label style="font-size: 13px; display: flex; align-items: center; gap: 10px; background: #333; padding: 0 15px; border-radius: 4px; border: 1px solid #444; height: 48px; box-sizing: border-box; cursor: pointer; white-space: nowrap; color: #fff; margin-bottom: 0;">
                        <input type="checkbox" name="is_video" <?php if(isset($edit_product) && $edit_product['is_video']) echo 'checked'; ?> style="margin: 0; width: 18px; height: 18px; cursor: pointer;"> <?php echo $ta['is_video']; ?>
                    </label>
                </div>
                <br><br>
                <button type="submit" class="btn-submit"><?php echo $edit_product ? $ta['update_btn'] : $ta['save_btn']; ?></button>
                <?php if($edit_product): ?>
                    <a href="admin.php" style="color: #888; margin-left: 20px; text-decoration: none;"><?php echo $ta['cancel']; ?></a>
                <?php endif; ?>
            </form>
        </div>

        <!-- DANH SÁCH SẢN PHẨM -->
        <h2 style="font-family: 'Playfair Display', serif; color: #c9a96e; margin-bottom: 20px;">
            <?php echo $filter === 'orders' ? $ta['history_title'] : $ta['list_title']; ?>
        </h2>

        <div class="admin-tabs">
            <a href="admin.php?filter=all" class="tab-link <?php echo $filter == 'all' ? 'active' : ''; ?>"><?php echo $ta['all']; ?></a>
            <a href="admin.php?filter=fashion" class="tab-link <?php echo $filter == 'fashion' ? 'active' : ''; ?>"><?php echo $ta['fashion']; ?></a>
            <a href="admin.php?filter=makeup" class="tab-link <?php echo $filter == 'makeup' ? 'active' : ''; ?>"><?php echo $ta['makeup']; ?></a>
            <a href="admin.php?filter=watch" class="tab-link <?php echo $filter == 'watch' ? 'active' : ''; ?>"><?php echo $ta['watch']; ?></a>
            <a href="admin.php?filter=orders" class="tab-link <?php echo $filter == 'orders' ? 'active' : ''; ?>"><?php echo $ta['orders']; ?></a>
        </div>

        <table>
            <?php if ($filter === 'orders'): ?>
            <thead>
                <tr>
                    <th><?php echo $ta['order_id']; ?></th>
                    <th><?php echo $ta['buyer']; ?></th>
                    <th><?php echo $ta['product']; ?></th>
                    <th><?php echo $ta['qty']; ?></th>
                    <th><?php echo $ta['price']; ?></th>
                    <th><?php echo $ta['date']; ?></th>
                    <th><?php echo $ta['status']; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php while($o = mysqli_fetch_assoc($orders)): ?>
                <tr>
                    <td>#<?php echo $o['id']; ?></td>
                    <td><b style="color: #c9a96e;"><?php echo htmlspecialchars($o['username']); ?></b></td>
                    <td><?php echo htmlspecialchars($o['prod_name']); ?></td>
                    <td><?php echo $o['quantity']; ?></td>
                    <td>$<?php echo $o['price']; ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($o['created_at'])); ?></td>
                    <td><span style="background: #27ae60; padding: 3px 8px; border-radius: 3px; font-size: 11px;"><?php echo $o['status']; ?></span></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
            <?php else: ?>
            <thead>
                <tr>
                    <th><?php echo $ta['image']; ?></th>
                    <th><?php echo $ta['prod_name']; ?></th>
                    <th><?php echo $ta['price']; ?></th>
                    <th><?php echo $ta['stock']; ?></th>
                    <th><?php echo $ta['category']; ?></th>
                    <th><?php echo $ta['actions']; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php while($p = mysqli_fetch_assoc($products)): ?>
                <?php 
                    $filename = $p['image_url'];
                    // Logic hiển thị đường dẫn: Ưu tiên link tuyệt đối, nếu không thì ghép thư mục nội bộ
                    if (empty($filename)) {
                        $imgPath = '../img/v4.jpg';
                    } elseif (filter_var($filename, FILTER_VALIDATE_URL)) {
                        $imgPath = $filename;
                    } else {
                        $imgPath = ($p['is_video'] ? '../video/' : '../img/') . $filename;
                    }
                ?>
                <tr>
                    <td>
                        <div style="width: 50px; height: 50px; overflow: hidden; border-radius: 4px; background: #000; border: 1px solid #333;">
                            <?php if ($p['is_video']): ?>
                                <video src="<?php echo $imgPath; ?>" style="width: 100%; height: 100%; object-fit: cover;" muted loop playsinline onmouseover="this.play()" onmouseout="this.pause()"></video>
                            <?php else: ?>
                                <img src="<?php echo $imgPath; ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='../img/v4.jpg'">
                            <?php endif; ?>
                        </div>
                    </td>
                    <td><?php echo htmlspecialchars($p['name']); ?></td>
                    <td>$<?php echo $p['price']; ?></td>
                    <td><span style="color: <?php echo $p['quantity'] > 0 ? '#27ae60' : '#e74c3c'; ?>"><?php echo $p['quantity']; ?></span></td>
                    <td style="text-transform: capitalize;"><?php echo $p['category']; ?> (<?php echo $p['gender']; ?>) <?php echo $p['is_video'] ? '<b style="color:#c9a96e">[Video]</b>' : ''; ?></td>
                    <td class="action-links">
                        <a href="admin.php?filter=<?php echo $filter; ?>&edit=<?php echo $p['id']; ?>" class="edit-btn"><?php echo $ta['edit']; ?></a>
                        <a href="admin.php?filter=<?php echo $filter; ?>&delete=<?php echo $p['id']; ?>" class="delete-btn" onclick="return confirm('<?php echo $ta['confirm_delete']; ?>')"><?php echo $ta['delete']; ?></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>