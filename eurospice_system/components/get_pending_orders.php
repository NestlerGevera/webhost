<?php
require_once '../config/database.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->connect();

$query = "SELECT * FROM pending_orders WHERE delivered_status = 'Pending' ORDER BY created_at DESC";
$stmt = $db->prepare($query);

if ($stmt->execute()) {
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($orders);
} else {
    echo json_encode(['error' => 'Failed to fetch pending orders']);
}
