<?php
// components/supplier_operations.php

// Send JSON header
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
    'data' => []
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

    // GET requests
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $action = isset($_GET['action']) ? $_GET['action'] : '';

        // Get all suppliers
        if ($action === 'get_all_suppliers') {
            $status = isset($_GET['status']) ? $_GET['status'] : '';

            $query = "SELECT * FROM suppliers";

            if ($status) {
                $query .= " WHERE status = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('s', $status);
            } else {
                $stmt = $conn->prepare($query);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            if ($result === false) {
                throw new Exception("Query failed: " . $conn->error);
            }

            while ($row = $result->fetch_assoc()) {
                $response['data'][] = $row;
            }

            $response['success'] = true;
            $response['message'] = 'Suppliers retrieved successfully';
            $stmt->close();
        }
        // Get single supplier
        else if ($action === 'get_supplier') {
            if (!isset($_GET['id'])) {
                throw new Exception("Supplier ID is required");
            }

            $id = intval($_GET['id']);

            $query = "SELECT * FROM suppliers WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result === false) {
                throw new Exception("Query failed: " . $conn->error);
            }

            $supplier = $result->fetch_assoc();

            if ($supplier) {
                $response['data'] = $supplier;
                $response['success'] = true;
                $response['message'] = 'Supplier retrieved successfully';
            } else {
                $response['message'] = 'Supplier not found';
            }

            $stmt->close();
        }
        // Search suppliers
        else if ($action === 'search_suppliers') {
            if (!isset($_GET['term'])) {
                throw new Exception("Search term is required");
            }

            $term = '%' . $_GET['term'] . '%';

            $query = "SELECT * FROM suppliers WHERE 
                     name LIKE ? OR 
                     company_name LIKE ? OR 
                     contact_person LIKE ? OR 
                     email LIKE ? OR
                     city LIKE ? OR
                     country LIKE ?";

            $stmt = $conn->prepare($query);
            $stmt->bind_param('ssssss', $term, $term, $term, $term, $term, $term);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result === false) {
                throw new Exception("Query failed: " . $conn->error);
            }

            while ($row = $result->fetch_assoc()) {
                $response['data'][] = $row;
            }

            $response['success'] = true;
            $response['message'] = 'Search results retrieved successfully';
            $stmt->close();
        } else {
            $response['message'] = 'Invalid action specified';
        }
    }

    // POST requests
    else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = isset($_POST['action']) ? $_POST['action'] : '';

        // Add new supplier
        if ($action === 'add_supplier') {
            // Required fields
            $required = ['name', 'company_name', 'contact_person', 'email', 'phone', 'address', 'city', 'country', 'postal_code'];

            foreach ($required as $field) {
                if (!isset($_POST[$field]) || empty($_POST[$field])) {
                    throw new Exception("Field '$field' is required");
                }
            }

            // Check if email already exists
            $checkQuery = "SELECT id FROM suppliers WHERE email = ?";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bind_param('s', $_POST['email']);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows > 0) {
                throw new Exception("A supplier with this email already exists");
            }

            $checkStmt->close();

            // Insert new supplier
            $query = "INSERT INTO suppliers (
                        name, company_name, contact_person, email, phone, 
                        address, city, state, country, postal_code, 
                        tax_id, payment_terms, account_number, website, notes, status
                      ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($query);

            if ($stmt === false) {
                throw new Exception("Prepare failed: " . $conn->error);
            }

            $name = $_POST['name'];
            $company = $_POST['company_name'];
            $contact = $_POST['contact_person'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $address = $_POST['address'];
            $city = $_POST['city'];
            $state = isset($_POST['state']) ? $_POST['state'] : null;
            $country = $_POST['country'];
            $postal = $_POST['postal_code'];
            $tax_id = isset($_POST['tax_id']) ? $_POST['tax_id'] : null;
            $terms = isset($_POST['payment_terms']) ? $_POST['payment_terms'] : 'Net 30';
            $account = isset($_POST['account_number']) ? $_POST['account_number'] : null;
            $website = isset($_POST['website']) ? $_POST['website'] : null;
            $notes = isset($_POST['notes']) ? $_POST['notes'] : null;
            $status = isset($_POST['status']) ? $_POST['status'] : 'Active';

            $stmt->bind_param(
                'ssssssssssssssss',
                $name,
                $company,
                $contact,
                $email,
                $phone,
                $address,
                $city,
                $state,
                $country,
                $postal,
                $tax_id,
                $terms,
                $account,
                $website,
                $notes,
                $status
            );

            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            $response['success'] = true;
            $response['message'] = 'Supplier added successfully';
            $response['data'] = ['id' => $conn->insert_id];
            $stmt->close();
        }
        // Update supplier
        else if ($action === 'update_supplier') {
            if (!isset($_POST['id'])) {
                throw new Exception("Supplier ID is required");
            }

            $id = intval($_POST['id']);

            // Check if supplier exists
            $checkQuery = "SELECT id FROM suppliers WHERE id = ?";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bind_param('i', $id);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows === 0) {
                throw new Exception("Supplier not found");
            }

            $checkStmt->close();

            // Check if email is unique (if changing email)
            if (isset($_POST['email'])) {
                $checkEmailQuery = "SELECT id FROM suppliers WHERE email = ? AND id != ?";
                $checkEmailStmt = $conn->prepare($checkEmailQuery);
                $checkEmailStmt->bind_param('si', $_POST['email'], $id);
                $checkEmailStmt->execute();
                $checkEmailResult = $checkEmailStmt->get_result();

                if ($checkEmailResult->num_rows > 0) {
                    throw new Exception("A supplier with this email already exists");
                }

                $checkEmailStmt->close();
            }

            // Build update query dynamically based on provided fields
            $setClause = [];
            $bindTypes = "";
            $bindValues = [];

            $fields = [
                'name' => 's',
                'company_name' => 's',
                'contact_person' => 's',
                'email' => 's',
                'phone' => 's',
                'address' => 's',
                'city' => 's',
                'state' => 's',
                'country' => 's',
                'postal_code' => 's',
                'tax_id' => 's',
                'payment_terms' => 's',
                'account_number' => 's',
                'website' => 's',
                'notes' => 's',
                'status' => 's',
                'rating' => 'i',
                'credit_limit' => 'd',
                'currency' => 's'
            ];

            foreach ($fields as $field => $type) {
                if (isset($_POST[$field])) {
                    $setClause[] = "$field = ?";
                    $bindTypes .= $type;
                    $bindValues[] = $_POST[$field];
                }
            }

            if (empty($setClause)) {
                throw new Exception("No fields to update");
            }

            $query = "UPDATE suppliers SET " . implode(", ", $setClause) . " WHERE id = ?";
            $bindTypes .= "i";
            $bindValues[] = $id;

            $stmt = $conn->prepare($query);

            if ($stmt === false) {
                throw new Exception("Prepare failed: " . $conn->error);
            }

            // Dynamically bind parameters
            $bindParams = array_merge([$bindTypes], $bindValues);
            $tmp = [];
            foreach ($bindParams as $key => $value) $tmp[$key] = &$bindParams[$key];
            call_user_func_array([$stmt, 'bind_param'], $tmp);

            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            $response['success'] = true;
            $response['message'] = 'Supplier updated successfully';
            $stmt->close();
        }
        // Delete supplier
        else if ($action === 'delete_supplier') {
            if (!isset($_POST['id'])) {
                throw new Exception("Supplier ID is required");
            }

            $id = intval($_POST['id']);

            // Check if supplier exists
            $checkQuery = "SELECT id FROM suppliers WHERE id = ?";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bind_param('i', $id);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows === 0) {
                throw new Exception("Supplier not found");
            }

            $checkStmt->close();

            // Check if supplier is referenced by products
            $checkProductsQuery = "SELECT COUNT(*) as product_count FROM products WHERE supplier_id = ?";
            $checkProductsStmt = $conn->prepare($checkProductsQuery);
            $checkProductsStmt->bind_param('i', $id);
            $checkProductsStmt->execute();
            $checkProductsResult = $checkProductsStmt->get_result();
            $productCount = $checkProductsResult->fetch_assoc()['product_count'];

            if ($productCount > 0) {
                throw new Exception("Cannot delete supplier: $productCount products are linked to this supplier");
            }

            $checkProductsStmt->close();

            // Delete supplier
            $query = "DELETE FROM suppliers WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $id);

            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            $response['success'] = true;
            $response['message'] = 'Supplier deleted successfully';
            $stmt->close();
        }
        // Change supplier status
        else if ($action === 'change_status') {
            if (!isset($_POST['id']) || !isset($_POST['status'])) {
                throw new Exception("Supplier ID and status are required");
            }

            $id = intval($_POST['id']);
            $status = $_POST['status'];

            // Validate status
            $validStatuses = ['Active', 'Inactive', 'Pending', 'Blacklisted'];
            if (!in_array($status, $validStatuses)) {
                throw new Exception("Invalid status. Must be one of: " . implode(', ', $validStatuses));
            }

            $query = "UPDATE suppliers SET status = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('si', $status, $id);

            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            $response['success'] = true;
            $response['message'] = 'Supplier status updated successfully';
            $stmt->close();
        } else {
            $response['message'] = 'Invalid action specified';
        }
    }
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    // Log the exception for debugging
    error_log("Exception in supplier_operations.php: " . $e->getMessage());
} finally {
    // Close connection if it exists and is not PDO
    if (isset($conn) && !($conn instanceof PDO)) {
        $conn->close();
    }

    // Always return a JSON response
    echo json_encode($response);
}
