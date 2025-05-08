<?php
// Force the response to be JSON
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);

// Include database connection
require_once '../config/database.php';

// Initialize database connection
$database = new Database();
$db = $database->connect();

$response = [];

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Add product
    if ($action === 'add_product') {
        $image_name = '';

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = '../uploads/';
            $image_name = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $upload_dir . $image_name;

            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
                exit;
            }
        }

        $data = [
            'name' => $_POST['name'],
            'brand' => $_POST['brand'],
            'stock' => $_POST['stock'],
            'price' => $_POST['price'],
            'batchCode' => $_POST['batchCode'],
            'weight' => $_POST['weight'],
            'packtype' => $_POST['packtype'],
            'packsize' => $_POST['packsize'],
            'shelftype' => $_POST['shelftype'],
            'expirationDate' => $_POST['expirationDate'],
            'country' => $_POST['country'],
            'delivered' => $_POST['delivered'],
            'image' => $image_name,
            'approved_by_finance' => $_POST['approved_by_finance'],
            'finance_notes' => $_POST['finance_notes'],
            'status' => $_POST['status']
        ];

        $response['success'] = $database->createProduct($data);
        $response['message'] = $response['success'] ? 'Product added successfully' : 'Failed to add product';
        echo json_encode($response);
        exit;
    }

    // Get, Update, Delete logic continues (unchanged except using $response consistently)...
    // NOTE: Use same pattern of building $response and json_encode($response); exit;

    if ($action === 'get_product') {
        $id = $_POST['id'];
        $product = $database->getSingleProduct($id);
        $response['success'] = !!$product;
        $response['product'] = $product;
        $response['message'] = $product ? 'Product fetched' : 'Product not found';
        echo json_encode($response);
        exit;
    }

    if ($action === 'update_product') {
        $product_id = $_POST['id'];
        $product = $database->getSingleProduct($product_id);
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            exit;
        }

        $image_name = $product['image'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = '../uploads/';
            $image_name = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $upload_dir . $image_name;
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                if (!empty($product['image']) && file_exists($upload_dir . $product['image'])) {
                    unlink($upload_dir . $product['image']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
                exit;
            }
        }

        // Handle finance approval fields for updates
        $approved_by_finance = isset($_POST['approved_by_finance']) ? $_POST['approved_by_finance'] : $product['approved_by_finance'] ?? '0';
        $finance_status = isset($_POST['finance_status']) ? $_POST['finance_status'] : $product['finance_status'] ?? 'pending';
        $supplier_notes = isset($_POST['supplier_notes']) ? $_POST['supplier_notes'] : $product['supplier_notes'] ?? '';

        // Check if finance status is changing to approved
        $finance_status_changed = isset($_POST['finance_status']) &&
            isset($product['finance_status']) &&
            $product['finance_status'] != $_POST['finance_status'] &&
            $_POST['finance_status'] == 'approved';

        $data = [
            'id' => $product_id,
            'name' => $_POST['name'],
            'brand' => $_POST['brand'],
            'stock' => $_POST['stock'],
            'price' => $_POST['price'],
            'batchCode' => $_POST['batchCode'],
            'weight' => $_POST['weight'],
            'packtype' => $_POST['packtype'],
            'packsize' => $_POST['packsize'],
            'shelftype' => $_POST['shelftype'],
            'expirationDate' => $_POST['expirationDate'],
            'country' => $_POST['country'],
            'delivered' => $_POST['delivered'],
            'image' => $image_name,
            'approved_by_finance' => $_POST['approved_by_finance'],
            'finance_notes' => $_POST['finance_notes'],
            'status' => $_POST['status']
        ];

        $success = $database->updateProduct($data);
        $response['success'] = $success;
        $response['message'] = $success ? 'Product updated successfully' : 'Failed to update product';
        echo json_encode($response);
        exit;
    }

    if ($action === 'delete_product') {
        $id = $_POST['id'];
        $product = $database->getSingleProduct($id);

        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            exit;
        }

        if (!empty($product['image'])) {
            $image_path = '../uploads/' . $product['image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        $response['success'] = $database->deleteProduct($id);
        $response['message'] = $response['success'] ? 'Product deleted successfully' : 'Failed to delete product';
        echo json_encode($response);
        exit;
    }

    // New action to handle finance approval
    if ($action === 'approve_product') {
        $product_id = $_POST['id'];
        $finance_notes = $_POST['finance_notes'] ?? '';
        $approved_by = $_SESSION['user_id'] ?? 0; // Assuming user_id is stored in session

        $product = $database->getSingleProduct($product_id);
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            exit;
        }

        $data = [
            'id' => $product_id,
            'approved_by_finance' => $approved_by,
            'finance_status' => 'approved',
            'finance_notes' => $finance_notes,
            'status' => 'Active' // Change status from Pending Approval to Active
        ];

        // Ensure this method exists in your Database class
        $success = $database->updateProduct($data);

        $response['success'] = $success;
        $response['message'] = $success ? 'Product approved successfully' : 'Failed to approve product';
        echo json_encode($response);
        exit;
    }

    // New action to reject product
    if ($action === 'reject_product') {
        $product_id = $_POST['id'];
        $finance_notes = $_POST['finance_notes'] ?? '';
        $rejected_by = $_SESSION['user_id'] ?? 0;

        $product = $database->getSingleProduct($product_id);
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            exit;
        }

        $data = [
            'id' => $product_id,
            'approved_by_finance' => $rejected_by,
            'finance_status' => 'rejected',
            'finance_notes' => $finance_notes,
            'status' => 'Rejected' // Change status to Rejected
        ];

        // Ensure this method exists in your Database class
        $success = $database->updateProduct($data);

        $response['success'] = $success;
        $response['message'] = $success ? 'Product rejected successfully' : 'Failed to reject product';
        echo json_encode($response);
        exit;
    }
}

// Handle GET request to fetch all products
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $result = $database->getProducts();
        $products = $result->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'products' => $products]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error fetching products']);
    }
    exit;
}
