<?php
session_start();
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <title>Fragrance Collection</title>
        <link rel="stylesheet" href="../css/collection.css">
        <link rel="stylesheet" href="../css/fragrance.css">
        <!-- Giữ lại để lấy style cho slider -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    </head>

    <body>
        <?php include '../header.php'; ?>

        <section class="fragrance-hero">
            <div>
                <h2>The Scent of Elegance</h2>
                <p>Discover your signature fragrance from our luxury collection</p>
            </div>
        </section>

        <section class="fragrance-grid" id="fragrance-products">
            <!-- Sản phẩm sẽ được nạp vào đây -->
        </section>

        <div class="cart-overlay" onclick="toggleCart()"></div>
        <section id="cart-sidebar" class="cart-sidebar">
            <div class="cart-header">
                <h2>Your Bag</h2>
                <button onclick="toggleCart()" class="close-cart">&times;</button>
            </div>
            <div id="cart-items" class="cart-items"></div>
            <div class="cart-footer">
                <div style="display: flex; justify-content: space-between; margin-bottom: 20px; color: white; font-weight: bold;">
                    <span>Total:</span>
                    <span>$<span id="cart-total">0</span></span>
                </div>
                <button class="btn-buy" onclick="checkout()">Checkout</button>
            </div>
        </section>
        <script src="../js/fragrance.js"></script>
    </body>

    </html>