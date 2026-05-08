<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Makeup Collection - Fashion Club</title>
    <link rel="stylesheet" href="../css/collection.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: #fdfbfb; /* Nền sáng */
            padding-top: 80px;
            color: #1a1a1a;
        }
        
        .makeup-hero {
            height: 40vh;
            background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('../img/banner_makeup.jpg') center/cover;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            margin-bottom: 50px;
        }
        
        .makeup-hero h2 {
            font-family: 'Playfair Display', serif;
            font-size: 48px;
            letter-spacing: 5px;
        }
        
        .makeup-grid {
            padding: 0 50px 100px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 40px;
        }
        
        .card {
            background: white;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: 0.5s;
            border-radius: 0;
        }
        
        .card:hover {
            transform: translateY(-15px);
            box-shadow: 0 20px 40px rgba(201, 169, 110, 0.2);
        }
        
        .card .img-container {
            height: 350px;
            background: #f9f9f9;
        }
        
        .card h3 {
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 20px;
            color: #333;
        }
        
        .card p {
            color: #c9a96e;
            font-family: 'Poppins', sans-serif;
            font-weight: 400;
        }
        
        .add-to-cart {
            background: transparent;
            border: 1px solid #1a1a1a;
            color: #1a1a1a;
            width: 80%;
            margin: 10px auto 20px;
        }
        
        .add-to-cart:hover {
            background: #c9a96e;
            border-color: #c9a96e;
            color: white;
        }
    </style>
</head>

<body>
    <?php include '../header.php'; ?>

    <section class="makeup-hero">
        <div>
            <h2>The Art of Beauty</h2>
            <p>Elevate your glow with our curated makeup collection</p>
        </div>
    </section>

    <main class="makeup-grid" id="makeup-products"></main>

    <div class="cart-overlay" onclick="toggleCart()"></div>
    <section id="cart-sidebar" class="cart-sidebar">
        <div class="cart-header">
            <h2>My Beauty Bag</h2>
            <button onclick="toggleCart()" class="close-cart">&times;</button>
        </div>
        <div id="cart-items" class="cart-items"></div>
        <div class="cart-footer">
            <div style="display: flex; justify-content: space-between; margin-bottom: 20px; color: white;">
                <span>Subtotal:</span>
                <span>$<span id="cart-total">0</span></span>
            </div>
            <button class="btn-buy" onclick="checkout()">Checkout</button>
        </div>
    </section>

    <footer style="text-align: center; padding: 50px; background: #fff; color: #999; font-size: 11px; border-top: 1px solid #eee;">
        <p>&copy; 2026 FASHION CLUB BEAUTY. ALL RIGHTS RESERVED.</p>
    </footer>
    <script src="../js/makeup.js"></script>
</body>

</html>