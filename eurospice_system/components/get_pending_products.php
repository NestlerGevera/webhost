<?php
// Include database connection
require_once '../config/database.php'; // Adjust path as needed

/**
 * Function to get all pending products
 * @return array An array of pending products
 */
function getPendingProducts()
{
    global $conn;

    try {
        $sql = "SELECT * FROM pending_orders WHERE status = 'Pending' ORDER BY created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching pending products: " . $e->getMessage());
        return [];
    }
}

/**
 * Function to get all approved products
 * @return array An array of approved products
 */
function getApprovedProducts()
{
    global $conn;

    try {
        $sql = "SELECT * FROM pending_orders WHERE status = 'Approved' ORDER BY created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching approved products: " . $e->getMessage());
        return [];
    }
}

/**
 * Function to get all rejected products
 * @return array An array of rejected products
 */
function getRejectedProducts()
{
    global $conn;

    try {
        $sql = "SELECT * FROM pending_orders WHERE status = 'Rejected' ORDER BY created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching rejected products: " . $e->getMessage());
        return [];
    }
}

/**
 * Function to get a single product by ID
 * @param int $id The product ID
 * @return array|null The product data or null if not found
 */
function getProductById($id)
{
    global $conn;

    try {
        $sql = "SELECT * FROM pending_orders WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result : null;
    } catch (PDOException $e) {
        error_log("Error fetching product by ID: " . $e->getMessage());
        return null;
    }
}

/**
 * Function to update product status
 * @param int $id The product ID
 * @param string $status The new status ('Pending', 'Approved', 'Rejected')
 * @param string $admin_notes Optional admin notes
 * @return bool Whether the update was successful
 */
function updateProductStatus($id, $status, $admin_notes = '')
{
    global $conn;

    try {
        $sql = "UPDATE pending_orders SET 
                status = :status, 
                admin_notes = :admin_notes,
                updated_at = NOW() 
                WHERE id = :id";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':admin_notes', $admin_notes, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Error updating product status: " . $e->getMessage());
        return false;
    }
}

/**
 * Function to add a new pending product
 * @param array $productData The product data
 * @return int|bool The new product ID or false on failure
 */
function addPendingProduct($productData)
{
    global $conn;

    try {
        $sql = "INSERT INTO pending_orders (
                user_id, product_name, description, price, 
                quantity, image_url, status, created_at, updated_at
                ) VALUES (
                :user_id, :product_name, :description, :price,
                :quantity, :image_url, 'Pending', NOW(), NOW()
                )";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $productData['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':product_name', $productData['product_name'], PDO::PARAM_STR);
        $stmt->bindParam(':description', $productData['description'], PDO::PARAM_STR);
        $stmt->bindParam(':price', $productData['price'], PDO::PARAM_STR);
        $stmt->bindParam(':quantity', $productData['quantity'], PDO::PARAM_INT);
        $stmt->bindParam(':image_url', $productData['image_url'], PDO::PARAM_STR);

        $stmt->execute();
        return $conn->lastInsertId();
    } catch (PDOException $e) {
        error_log("Error adding pending product: " . $e->getMessage());
        return false;
    }
}

/**
 * Function to delete a pending product
 * @param int $id The product ID
 * @return bool Whether the deletion was successful
 */
function deletePendingProduct($id)
{
    global $conn;

    try {
        $sql = "DELETE FROM pending_orders WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Error deleting pending product: " . $e->getMessage());
        return false;
    }
}

/**
 * Function to get count of products by status
 * @return array Counts indexed by status
 */
function getProductCountsByStatus()
{
    global $conn;

    try {
        $sql = "SELECT status, COUNT(*) as count FROM pending_orders GROUP BY status";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $counts = [
            'Pending' => 0,
            'Approved' => 0,
            'Rejected' => 0
        ];

        foreach ($results as $row) {
            $counts[$row['status']] = (int)$row['count'];
        }

        return $counts;
    } catch (PDOException $e) {
        error_log("Error getting product counts: " . $e->getMessage());
        return [
            'Pending' => 0,
            'Approved' => 0,
            'Rejected' => 0
        ];
    }
}

/**
 * Function to search products
 * @param string $keyword The search keyword
 * @param string $status Optional status filter
 * @return array Matching products
 */
function searchProducts($keyword, $status = null)
{
    global $conn;

    try {
        $sql = "SELECT * FROM pending_orders WHERE 
                (product_name LIKE :keyword OR description LIKE :keyword)";

        if ($status) {
            $sql .= " AND status = :status";
        }

        $sql .= " ORDER BY created_at DESC";
        $stmt = $conn->prepare($sql);

        $searchTerm = "%" . $keyword . "%";
        $stmt->bindParam(':keyword', $searchTerm, PDO::PARAM_STR);

        if ($status) {
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error searching products: " . $e->getMessage());
        return [];
    }
}

/**
 * Function to get products by user ID
 * @param int $userId The user ID
 * @param string $status Optional status filter
 * @return array User's products
 */
function getProductsByUser($userId, $status = null)
{
    global $conn;

    try {
        $sql = "SELECT * FROM pending_orders WHERE user_id = :user_id";

        if ($status) {
            $sql .= " AND status = :status";
        }

        $sql .= " ORDER BY created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

        if ($status) {
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching user products: " . $e->getMessage());
        return [];
    }
}

/**
 * Function to move approved product to active products
 * @param int $id The pending product ID
 * @return bool Whether the operation was successful
 */
function moveToActiveProducts($id)
{
    global $conn;

    try {
        // Start transaction
        $conn->beginTransaction();

        // Get the approved product
        $sql = "SELECT * FROM pending_orders WHERE id = :id AND status = 'Approved'";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            $conn->rollBack();
            return false;
        }

        // Insert into active products table
        $sql = "INSERT INTO active_products (
                user_id, product_name, description, price, 
                quantity, image_url, created_at, updated_at
                ) VALUES (
                :user_id, :product_name, :description, :price,
                :quantity, :image_url, NOW(), NOW()
                )";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $product['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':product_name', $product['product_name'], PDO::PARAM_STR);
        $stmt->bindParam(':description', $product['description'], PDO::PARAM_STR);
        $stmt->bindParam(':price', $product['price'], PDO::PARAM_STR);
        $stmt->bindParam(':quantity', $product['quantity'], PDO::PARAM_INT);
        $stmt->bindParam(':image_url', $product['image_url'], PDO::PARAM_STR);

        $stmt->execute();

        // Delete from pending orders
        $sql = "DELETE FROM pending_orders WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Commit transaction
        $conn->commit();
        return true;
    } catch (PDOException $e) {
        // Rollback on error
        $conn->rollBack();
        error_log("Error moving to active products: " . $e->getMessage());
        return false;
    }
}

/**
 * Function to get paginated products by status
 * @param string $status The status to filter by
 * @param int $page Current page number
 * @param int $perPage Items per page
 * @return array Products and pagination info
 */
function getPaginatedProducts($status, $page = 1, $perPage = 10)
{
    global $conn;

    try {
        // Calculate offset
        $offset = ($page - 1) * $perPage;

        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM pending_orders WHERE status = :status";
        $countStmt = $conn->prepare($countSql);
        $countStmt->bindParam(':status', $status, PDO::PARAM_STR);
        $countStmt->execute();
        $totalCount = (int)$countStmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Get paginated results
        $sql = "SELECT * FROM pending_orders WHERE status = :status 
                ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate total pages
        $totalPages = ceil($totalCount / $perPage);

        return [
            'products' => $products,
            'pagination' => [
                'total' => $totalCount,
                'per_page' => $perPage,
                'current_page' => $page,
                'total_pages' => $totalPages,
                'has_more' => ($page < $totalPages)
            ]
        ];
    } catch (PDOException $e) {
        error_log("Error fetching paginated products: " . $e->getMessage());
        return [
            'products' => [],
            'pagination' => [
                'total' => 0,
                'per_page' => $perPage,
                'current_page' => $page,
                'total_pages' => 0,
                'has_more' => false
            ]
        ];
    }
}
