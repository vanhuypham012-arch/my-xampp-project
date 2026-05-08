<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$lang = isset($_COOKIE['lang']) ? $_COOKIE['lang'] : 'EN';
$t_hero = [
    'EN' => ['arrivals' => 'NEW ARRIVALS 2026', 'define' => 'DEFINE YOUR STYLE', 'explore' => 'Explore Collection'],
    'VN' => ['arrivals' => 'BỘ SƯU TẬP MỚI 2026', 'define' => 'ĐỊNH HÌNH PHONG CÁCH', 'explore' => 'Khám Phá Ngay']
];
$h = $t_hero[$lang];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fashion Club - Home</title>
    <link rel="stylesheet" href="css/index.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&family=Playfair+Display&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>

    <section class="hero">
        <img src="img/banner.jpg" class="hero-img">
        <div class="hero-text">
            <h2><?php echo $h['arrivals']; ?></h2>
            <p><?php echo $h['define']; ?></p>
            <a href="html/collection.php" class="btn"><?php echo $h['explore']; ?></a>
        </div>
    </section>

    <script>
        // Thay vì mở sidebar, khi nhấp vào icon giỏ hàng ở trang chủ sẽ chuyển hướng đến trang mua sắm
        window.toggleCart = function() {
            window.location.href = 'html/collection.php';
        };

        // Chỉ cập nhật số lượng badge hiển thị trên icon giỏ hàng ở Header
        function updateCartBadge() {
            const cart = JSON.parse(localStorage.getItem("fashion_cart")) || [];
            const count = cart.reduce((sum, item) => sum + item.quantity, 0);
            if(document.getElementById("cart-count")) document.getElementById("cart-count").innerText = count;
        }
        window.addEventListener('load', updateCartBadge);
    </script>
</body>
</html>