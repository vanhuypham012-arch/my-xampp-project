<?php
session_start();

$lang = isset($_COOKIE['lang']) ? $_COOKIE['lang'] : 'EN';
$t_coll = [
    'EN' => ['my_bag' => 'My Bag', 'subtotal' => 'Subtotal:', 'checkout' => 'Checkout', 'gender' => 'GENDER', 'category' => 'CATEGORY'],
    'VN' => ['my_bag' => 'Giỏ Hàng', 'subtotal' => 'Tổng cộng:', 'checkout' => 'Thanh Toán', 'gender' => 'GIỚI TÍNH', 'category' => 'DANH MỤC']
];
$c = $t_coll[$lang];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collection - Fashion Club</title>
    <link rel="stylesheet" href="../css/collection.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>

    <?php include '../header.php'; ?>

    <div class="container">
        <!-- SIDEBAR: Bộ lọc sản phẩm -->
        <aside class="sidebar">
            <h4><?php echo $c['gender']; ?></h4>
            <label><input type="checkbox" class="gender" value="men"> Men</label>
            <label><input type="checkbox" class="gender" value="women"> Women</label>

            <div class="category-box" style="display: none; margin-top: 20px;">
                <h4><?php echo $c['category']; ?></h4>
                <label data-type="women"><input type="checkbox" class="category" value="dress"> Dress</label>
                <label data-type="women"><input type="checkbox" class="category" value="tops"> Tops</label>
                <label data-type="men"><input type="checkbox" class="category" value="pants"> Pants</label>
                <label data-type="men"><input type="checkbox" class="category" value="jacket"> Jacket</label>
            </div>
        </aside>

        <!-- MAIN: Danh sách sản phẩm Fashion -->
        <main class="products">
            <!-- Sản phẩm sẽ được collection.js tự động tải vào đây -->
            <div style="color: #666; grid-column: 1/-1; text-align: center; padding: 50px;">
                <i class="fa-solid fa-spinner fa-spin"></i> Loading fashion collection...
            </div>
        </main>
    </div>

    <!-- GIỎ HÀNG (CART SIDEBAR) -->
    <div class="cart-overlay" onclick="toggleCart()"></div>
    <section id="cart-sidebar" class="cart-sidebar">
        <div class="cart-header">
            <h2 style="color: black;"><?php echo $c['my_bag']; ?></h2>
            <button onclick="toggleCart()" class="close-cart">&times;</button>
        </div>
        <div id="cart-items" class="cart-items"></div>
        <div class="cart-footer">
            <div style="display: flex; justify-content: space-between; margin-bottom: 20px; color: white;">
                <span style="color: black;"><?php echo $c['subtotal']; ?></span>
                <span>$<span id="cart-total">0</span></span>
            </div>
            <button class="btn-buy" onclick="checkout()"><?php echo $c['checkout']; ?></button>
        </div>
    </section>

    <footer style="text-align: center; padding: 50px; background: #fff; color: #999; font-size: 11px; border-top: 1px solid #eee;">
        <p>&copy; 2026 FASHION CLUB. ALL RIGHTS RESERVED.</p>
    </footer>

    <!-- Liên kết file logic đã có của bạn -->
    <script src="../js/collection.js"></script>
    <script>
        // Đảm bảo khi load trang, danh mục Fashion được kích hoạt
        window.addEventListener("load", () => {
            const fashionBtn = document.querySelector('.menu a[href*="collection"]');
            if (fashionBtn) fashionBtn.classList.add('active');
            
            // Tự động hiện sidebar sau khi load để người dùng dễ thấy
            const sidebar = document.querySelector(".sidebar");
            if (sidebar) sidebar.style.opacity = "1";
            if (sidebar) sidebar.style.transform = "translateX(0)";
        });
    </script>
</body>
</html>