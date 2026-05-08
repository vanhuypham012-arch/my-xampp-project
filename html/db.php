<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "fashion_club"; // Đảm bảo viết thường đồng nhất với manage_products.php

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Lỗi kết nối Database: " . mysqli_connect_error());
}

// Thiết lập kết nối hỗ trợ tiếng Việt có dấu
mysqli_set_charset($conn, "utf8mb4");
?>