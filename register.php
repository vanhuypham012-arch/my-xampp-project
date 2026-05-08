<?php
require_once 'config.php';

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Mật khẩu xác nhận không khớp!";
    } else {
        // Kiểm tra username tồn tại chưa
        $check_sql = "SELECT id FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_get_result($stmt)->num_rows > 0) {
            $error = "Tên đăng nhập đã tồn tại!";
        } else {
            // Mã hóa mật khẩu và lưu
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $insert_sql = "INSERT INTO users (username, password, role) VALUES (?, ?, 'user')";
            $stmt = mysqli_prepare($conn, $insert_sql);
            mysqli_stmt_bind_param($stmt, "ss", $username, $hashed_password);
            
            if (mysqli_stmt_execute($stmt)) {
                $message = "Đăng ký thành công! Đang chuyển hướng...";
                echo "<script>setTimeout(() => { window.location.href = 'login.php'; }, 2000);</script>";
            } else {
                $error = "Có lỗi xảy ra, vui lòng thử lại!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Fashion Club</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=Poppins:wght@300;400&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #1a1a1a; color: white; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .register-card { background: #222; padding: 40px; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); width: 350px; text-align: center; border: 1px solid #333; }
        h2 { font-family: 'Playfair Display', serif; color: #c9a96e; letter-spacing: 2px; margin-bottom: 30px; }
        input { width: 100%; padding: 12px; margin: 10px 0; background: #333; border: 1px solid #444; color: white; border-radius: 4px; outline: none; }
        input:focus { border-color: #c9a96e; }
        .btn-reg { width: 100%; padding: 12px; background: #c9a96e; border: none; color: black; font-weight: bold; cursor: pointer; margin-top: 20px; transition: 0.3s; }
        .btn-reg:hover { background: white; }
        .error { color: #ff4444; font-size: 13px; }
        .success { color: #44ff44; font-size: 13px; }
        a { color: #c9a96e; text-decoration: none; font-size: 13px; }
    </style>
</head>
<body>
    <div class="register-card">
        <h2>REGISTER</h2>
        <?php 
            if($error) echo "<p class='error'>$error</p>"; 
            if($message) echo "<p class='success'>$message</p>";
        ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Choose Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit" class="btn-reg">CREATE ACCOUNT</button>
        </form>
        <p style="margin-top: 20px; font-size: 13px; color: #888;">
            Already have an account? <a href="login.php">Login here</a>
        </p>
    </div>
</body>
</html>