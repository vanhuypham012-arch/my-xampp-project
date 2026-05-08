<?php
session_start();

// 1. Xóa tất cả các biến session
$_SESSION = array();

// 2. Xóa session cookie trên trình duyệt của người dùng
// Điều này đảm bảo session ID cũ không còn được gửi lên server nữa
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Hủy session hoàn toàn trên máy chủ
session_destroy();

// 4. Chuyển hướng người dùng về trang đăng nhập hoặc trang chủ
header("Location: login.php");
exit();