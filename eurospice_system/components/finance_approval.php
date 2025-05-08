<?php
// Include database connection
require_once '../config/database.php'; // Adjust path as needed

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    switch ($action) {
        case 'approve_product':
            approveProduct($id);
            break;
        case 'reject_product':
            rejectProduct($id);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

// Function to approve a pending product
function approveProduct($id)
{
    global $conn;

    try {
        // Start transaction
        $conn->beginTransaction();

        // Get the pending order details
        $sql = "SELECT * FROM pending_orders WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            throw new Exception('Pending order not found');
        }

        // Update the product's finance approval status
        $sql = "UPDATE products SET 
                approved_by_finance = 1, 
                finance_notes = CONCAT(finance_notes, ' | Approved on ', NOW())
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$order['product_id']]);

        // Update the pending order status
        $sql = "UPDATE pending_orders SET 
                status = 'Approved',
                notes = CONCAT(notes, ' | Approved on ', NOW())
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);

        // Commit transaction
        $conn->commit();

        echo json_encode(['success' => true, 'message' => 'Product has been approved']);
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}

// Function to reject a pending product
function rejectProduct($id)
{
    global $conn;

    try {
        // Start transaction
        $conn->beginTransaction();

        // Get the pending order details
        $sql = "SELECT * FROM pending_orders WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            throw new Exception('Pending order not found');
        }

        // Update the product's finance approval status
        $sql = "UPDATE products SET 
                approved_by_finance = 0, 
                finance_notes = CONCAT(finance_notes, ' | Rejected on ', NOW())
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$order['product_id']]);

        // Update the pending order status
        $sql = "UPDATE pending_orders SET 
                status = 'Rejected',
                notes = CONCAT(notes, ' | Rejected on ', NOW())
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);

        // Commit transaction
        $conn->commit();

        echo json_encode(['success' => true, 'message' => 'Product has been rejected']);
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
