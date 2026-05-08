<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['avatar'])) {
    $user_id = $_SESSION['user_id'];
    $file = $_FILES['avatar'];

    // 1. Kiểm tra lỗi file
    if ($file['error'] !== UPLOAD_ERR_OK) {
        header("Location: profile.php?error=Lỗi upload file.");
        exit();
    }

    // 2. Kiểm tra định dạng (chỉ cho phép ảnh)
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
    if (!in_array($file['type'], $allowed_types)) {
        header("Location: profile.php?error=Chỉ chấp nhận file ảnh (JPG, PNG, WEBP).");
        exit();
    }

    // 3. Kiểm tra kích thước (ví dụ tối đa 2MB)
    if ($file['size'] > 2 * 1024 * 1024) {
        header("Location: profile.php?error=Kích thước ảnh quá lớn (tối đa 2MB).");
        exit();
    }

    // 4. Xử lý lưu file
    $upload_dir = 'img/avatars/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = 'avatar_' . $user_id . '_' . time() . '.' . $extension;
    $target_path = $upload_dir . $new_filename;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        // 5. Cập nhật Database
        $stmt = $conn->prepare("UPDATE users SET avatar_url = ? WHERE id = ?");
        $stmt->bind_param("si", $new_filename, $user_id);
        
        if ($stmt->execute()) {
            // 6. Cập nhật Session để Header đổi ảnh ngay lập tức
            $_SESSION['avatar'] = $new_filename;
            header("Location: profile.php?success=1");
        } else {
            header("Location: profile.php?error=Không thể cập nhật cơ sở dữ liệu.");
        }
    } else {
        header("Location: profile.php?error=Lỗi khi lưu file vào thư mục.");
    }
    exit();
}