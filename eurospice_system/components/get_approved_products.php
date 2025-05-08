<?php
require_once '../config/database.php';

$db = new Database();
$conn = $db->connect();

try {
    // Join products with approved_orders table to get only approved products
    $query = "SELECT p.name, p.brand, p.stock, p.price, p.batch_code AS batchCode, 
                     p.weight, p.pack_type AS packtype, p.pack_size AS packsize, 
                     p.shelf_type AS shelftype, p.expiration_date AS expirationDate, 
                     p.country, p.delivered, p.image
              FROM products p
              INNER JOIN approved_orders ao ON p.order_id = ao.order_id";

    $stmt = $conn->prepare($query);
    $stmt->execute();

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Set header to return JSON
    header('Content-Type: application/json');
    echo json_encode($products);
} catch (PDOException $e) {
    // Set proper error status code
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
