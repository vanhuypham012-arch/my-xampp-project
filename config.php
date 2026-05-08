<?php
// config.php - Cấu hình database chung cho thư mục gốc
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "fashion_club";

// Bật báo lỗi MySQLi để dễ dàng debug
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");
?>