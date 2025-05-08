<?php
// Log startup and errors for debugging
file_put_contents('php_error_log.txt', 'PHP started at ' . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error) {
        file_put_contents('php_error_log.txt', print_r($error, true), FILE_APPEND);
    }
});

// Send JSON header first
header('Content-Type: application/json');

// Configure error handling
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);
ini_set('error_log', 'php_error_log.txt');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'products' => []
];

try {
    // Include database configuration
    require_once '../config/database.php';

    // Check if connection exists 
    if (!isset($conn) || $conn === null) {
        // Fallback connection if database.php didn't provide one
        $conn = new mysqli('localhost', 'root', '', 'eurospice_database');

        if ($conn->connect_error) {
            throw new Exception("Database connection failed: " . $conn->connect_error);
        }
    }

    // GET request
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $action = isset($_GET['action']) ? $_GET['action'] : '';

        if ($action === 'get_pending_products') {
            // Query to get pending products
            $query = "SELECT p.*, s.name AS supplier_name
                      FROM products p
                      LEFT JOIN suppliers s ON p.supplier_id = s.id
                      WHERE p.approved_by_finance = 0 AND p.status = 'Pending'
                      ORDER BY p.id DESC";

            $result = $conn->query($query);

            if ($result === false) {
                throw new Exception("Query failed: " . $conn->error);
            }

            while ($row = $result->fetch_assoc()) {
                $response['products'][] = $row;
            }

            $response['success'] = true;
            $response['message'] = 'Products retrieved successfully';
        } else {
            $response['message'] = 'Invalid action specified';
        }
    }

    // POST request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = isset($_POST['action']) ? $_POST['action'] : '';

        if (in_array($action, ['approve_product', 'reject_product'])) {
            $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
            $notes = isset($_POST['notes']) ? $_POST['notes'] : '';

            if (!$productId) {
                $response['message'] = 'Product ID is required';
                echo json_encode($response);
                exit;
            }

            if ($action === 'reject_product' && !$notes) {
                $response['message'] = 'Rejection reason is required';
                echo json_encode($response);
                exit;
            }

            $query = "UPDATE products SET 
                        approved_by_finance = ?,
                        finance_notes = ?,
                        status = ?,
                        approved_date = NOW()
                      WHERE id = ?";

            $approved = ($action === 'approve_product') ? 1 : 0;
            $productStatus = ($action === 'approve_product') ? 'Active' : 'Rejected';

            $stmt = $conn->prepare($query);

            if ($stmt === false) {
                throw new Exception("Prepare failed: " . $conn->error);
            }

            $stmt->bind_param('issi', $approved, $notes, $productStatus, $productId);

            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            $response['success'] = true;
            $response['message'] = 'Product ' . ($approved ? 'approved' : 'rejected') . ' successfully';
            $stmt->close();
        } else {
            $response['message'] = 'Invalid action specified';
        }
    }
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    // Log the exception for debugging
    error_log("Exception: " . $e->getMessage());
} finally {
    // Close connection if it exists and is not PDO
    if (isset($conn) && !($conn instanceof PDO)) {
        $conn->close();
    }

    // Always return a JSON response
    echo json_encode($response);
}
