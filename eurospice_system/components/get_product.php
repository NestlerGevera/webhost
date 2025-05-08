<?php
require_once '../config/database.php';

$db = new Database();
$conn = $db->connect();

// Only select products approved by finance
$query = "SELECT name, brand, stock, price, batchCode, weight, packtype, packsize, shelftype, expirationDate, country, delivered, image 
          FROM products 
          WHERE approved_by_finance = 1";

$stmt = $conn->prepare($query);
$stmt->execute();

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($products);

echo json_encode([
    'success' => true,
    'products' => [['name' => 'Test Product', 'price' => 123.45]] // dummy data
]);
