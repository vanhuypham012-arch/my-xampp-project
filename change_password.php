<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $current_pwd = $_POST['current_password'];
    $new_pwd = $_POST['new_password'];
    $confirm_pwd = $_POST['confirm_password'];

    // 1. Kiểm tra mật khẩu mới và xác nhận mật khẩu có khớp không
    if ($new_pwd !== $confirm_pwd) {
        header("Location: profile.php?pwd_error=Xác nhận mật khẩu mới không khớp.");
        exit();
    }

    // 2. Lấy mật khẩu hiện tại từ database để đối chiếu
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // 3. Xác minh mật khẩu hiện tại (dùng password_verify vì pass trong DB đã được hash)
    if (password_verify($current_pwd, $user['password'])) {
        // 4. Mã hóa mật khẩu mới và cập nhật
        $hashed_pwd = password_hash($new_pwd, PASSWORD_BCRYPT);
        $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update_stmt->bind_param("si", $hashed_pwd, $user_id);
        
        if ($update_stmt->execute()) {
            header("Location: profile.php?pwd_success=1");
        } else {
            header("Location: profile.php?pwd_error=Không thể cập nhật mật khẩu vào hệ thống.");
        }
    } else {
        header("Location: profile.php?pwd_error=Mật khẩu hiện tại không chính xác.");
    }
    $stmt->close();
    exit();
}