<?php
session_start();
require_once 'config.php';

// Nếu đã đăng nhập rồi thì vào thẳng trang chủ
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; // ✅ sửa ở đây

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Mật khẩu không chính xác!";
        }
    } else {
        $error = "Tài khoản không tồn tại!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Fashion Club</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=Poppins:wght@300;400&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #ffffff; color: black; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-card { background: #f9f9f9; padding: 40px; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 350px; text-align: center; border: 1px solid #eee; }
        h2 { font-family: 'Playfair Display', serif; color: #c9a96e; letter-spacing: 2px; margin-bottom: 30px; }
        input { width: 100%; padding: 12px; margin: 10px 0; background: #fff; border: 1px solid #ddd; color: black; border-radius: 4px; outline: none; }
        input:focus { border-color: #c9a96e; background: #fff; }
        .btn-login { width: 100%; padding: 12px; background: #c9a96e; border: none; color: black; font-weight: bold; cursor: pointer; margin-top: 20px; transition: 0.3s; }
        .btn-login:hover { background: white; }
        .error { color: #ff4444; font-size: 13px; margin-bottom: 10px; }
        a { color: #c9a96e; text-decoration: none; font-size: 13px; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>FASHION CLUB</h2>
        <?php if($error) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn-login">LOGIN</button>
        </form>
        <p style="margin-top: 20px; font-size: 13px; color: #888;">
            Don't have an account? <a href="register.php">Register here</a>
        </p>
    </div>
</body>
</html>