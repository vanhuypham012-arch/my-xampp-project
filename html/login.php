<?php
include 'db.php';
session_start();
if (isset($_POST['login'])) {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = $_POST['password'];
    ✅ $sql = "SELECT * FROM users WHERE username = ?";
    ✅ $stmt = mysqli_prepare($conn, $sql);
    ✅ mysqli_stmt_bind_param($stmt, "s", $user);
    ✅ mysqli_stmt_execute($stmt);
    ✅ $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    if ($row && password_verify($pass, $row['password'])) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['username'] = $row['username'];
        header("Location: collection.php");
        exit();
    } else {
        $error = "Sai thông tin đăng nhập!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Đăng nhập - Fashion Club</title>
    <link rel="stylesheet" href="../css/collection.css">
    <style>
        .auth-form { max-width: 400px; margin: 100px auto; background: white; padding: 30px; border-radius: 8px; color: #333; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; }
        .btn-auth { width: 100%; background: #c9a96e; color: black; border: none; padding: 10px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <div class="auth-form">
        <h2>Đăng nhập</h2>
        <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Tên đăng nhập" required>
            <input type="password" name="password" placeholder="Mật khẩu" required>
            <button type="submit" name="login" class="btn-auth">Đăng nhập</button>
        </form>
        <p>Chưa có tài khoản? <a href="register.php">Đăng ký tại đây</a></p>
    </div>
</body>
</html>
