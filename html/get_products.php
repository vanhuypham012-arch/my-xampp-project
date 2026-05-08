<?php
include 'db.php';
header('Content-Type: application/json');

$result = mysqli_query($conn, "SELECT * FROM products");
$products = [];
while($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}
echo json_encode($products);
?>