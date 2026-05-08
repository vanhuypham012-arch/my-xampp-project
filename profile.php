<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang cá nhân - Fashion Club</title>
    <link rel="stylesheet" href="css/collection.css">
    <style>
        .profile-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 40px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        .profile-avatar-large {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
            border: 3px solid #c9a96e;
        }
        .upload-form {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }
        .btn-upload {
            background: #1a1a1a;
            color: #fff;
            padding: 10px 25px;
            border: none;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.3s;
        }
        .btn-upload:hover { background: #c9a96e; }
        .msg { font-size: 14px; margin-bottom: 15px; }
        .msg-success { color: green; }
        .msg-error { color: red; }
        .profile-container input[type="password"] {
            width: 100%;
            max-width: 300px;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            outline: none;
        }
        .profile-container input:focus { border-color: #c9a96e; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="profile-container">
        <h2 style="font-family: 'Playfair Display', serif; margin-bottom: 20px;">Quản lý tài khoản</h2>
        
        <?php if(isset($_GET['success'])) echo "<p class='msg msg-success'>Cập nhật ảnh đại diện thành công!</p>"; ?>
        <?php if(isset($_GET['error'])) echo "<p class='msg msg-error'>Lỗi: " . htmlspecialchars($_GET['error']) . "</p>"; ?>

        <?php if(isset($_SESSION['avatar']) && !empty($_SESSION['avatar'])): ?>
            <img src="img/avatars/<?php echo $_SESSION['avatar']; ?>" class="profile-avatar-large">
        <?php else: ?>
            <div class="profile-avatar-large" style="background: #eee; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                <i class="fa-solid fa-user" style="font-size: 60px; color: #ccc;"></i>
            </div>
        <?php endif; ?>

        <form action="upload_avatar.php" method="POST" enctype="multipart/form-data" class="upload-form">
            <label for="avatar">Chọn ảnh đại diện mới:</label>
            <input type="file" name="avatar" id="avatar" accept="image/*" required>
            <button type="submit" class="btn-upload">Tải ảnh lên</button>
        </form>

        <!-- Form đổi mật khẩu -->
        <form action="change_password.php" method="POST" class="upload-form" style="margin-top: 40px; border-top: 1px solid #eee; padding-top: 30px;">
            <h3 style="font-family: 'Playfair Display', serif; margin-bottom: 15px; font-size: 1.2rem;">Đổi mật khẩu</h3>
            
            <?php if(isset($_GET['pwd_success'])) echo "<p class='msg msg-success'>Đổi mật khẩu thành công!</p>"; ?>
            <?php if(isset($_GET['pwd_error'])) echo "<p class='msg msg-error'>Lỗi: " . htmlspecialchars($_GET['pwd_error']) . "</p>"; ?>

            <input type="password" name="current_password" placeholder="Mật khẩu hiện tại" required>
            <input type="password" name="new_password" placeholder="Mật khẩu mới" required>
            <input type="password" name="confirm_password" placeholder="Xác nhận mật khẩu mới" required>
            <button type="submit" class="btn-upload">Cập nhật mật khẩu</button>
        </form>
    </div>
</body>
</html>