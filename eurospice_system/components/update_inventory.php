<?php
// Include database connection
require_once 'config/database.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the values from the request
    $productName = isset($_POST['productName']) ? $_POST['productName'] : null;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
    $orderId = isset($_POST['orderId']) ? $_POST['orderId'] : null;
    $action = isset($_POST['action']) ? $_POST['action'] : 'decrease';

    // Validate inputs
    if (empty($productName) || $quantity <= 0 || empty($orderId)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid input parameters'
        ]);
        exit;
    }

    try {
        // Get current product stock
        $stmt = $conn->prepare("SELECT id, stock FROM products WHERE name = ?");
        $stmt->bind_param("s", $productName);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Product not found'
            ]);
            exit;
        }

        $product = $result->fetch_assoc();
        $productId = $product['id'];
        $currentStock = intval($product['stock']);

        // Calculate new stock
        $newStock = ($action === 'decrease')
            ? max(0, $currentStock - $quantity)
            : ($currentStock + $quantity);

        // Update the stock in the database
        $updateStmt = $conn->prepare("UPDATE products SET stock = ? WHERE id = ?");
        $updateStmt->bind_param("ii", $newStock, $productId);
        $success = $updateStmt->execute();

        if ($success) {
            // Log the inventory change
            $logStmt = $conn->prepare("INSERT INTO inventory_log (product_id, order_id, quantity_change, action, previous_stock, new_stock, timestamp) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $logStmt->bind_param("isisii", $productId, $orderId, $quantity, $action, $currentStock, $newStock);
            $logStmt->execute();

            echo json_encode([
                'status' => 'success',
                'message' => 'Inventory updated successfully',
                'data' => [
                    'productId' => $productId,
                    'productName' => $productName,
                    'previousStock' => $currentStock,
                    'newStock' => $newStock
                ]
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to update inventory'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
}
