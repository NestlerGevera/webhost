<?php
require_once '../config/database.php';
$db = new Database();
$conn = $db->connect();

function getData($conn, $table)
{
    $stmt = $conn->prepare("SELECT * FROM $table");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode([
    "pending" => getData($conn, "pending_orders"),
    "approved" => getData($conn, "approved_orders"),
    "rejected" => getData($conn, "rejected_orders")
]);
