<?php
// process_approval.php

require_once '../config/database.php'; // Make sure this file sets up your $pdo connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $orderId = $_POST['order_id'] ?? '';
    $notes = $_POST['notes'] ?? '';

    if ($action === 'approve' && !empty($orderId)) {
        try {
            // Update the order to approved
            $stmt = $pdo->prepare("UPDATE orders SET status = 'Approved', notes = :notes WHERE id = :order_id");
            $stmt->bindParam(':notes', $notes);
            $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                http_response_code(200);
                echo "Order approved successfully.";
            } else {
                http_response_code(500);
                echo "Failed to approve order.";
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo "Database error: " . $e->getMessage();
        }
    } else {
        http_response_code(400);
        echo "Invalid request.";
    }
} else {
    http_response_code(405);
    echo "Method not allowed.";
}
