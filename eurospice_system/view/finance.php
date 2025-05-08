<?php
require_once '../config/database.php';

// Create database connection
$db = new Database();
$pdo = $db->connect();

// Function to get products by status - FIXED to use correct column name
function getProductsByStatus($pdo, $status)
{
    // Modified query to use the correct column name (likely "status" instead of "order_status")
    $stmt = $pdo->prepare("SELECT * FROM products WHERE status = ?");
    $stmt->execute([$status]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch products by status - using correct column name
$pendingProducts = getProductsByStatus($pdo, 'Pending');
$approvedProducts = getProductsByStatus($pdo, 'Approved');
$rejectedProducts = getProductsByStatus($pdo, 'Rejected');

// Process product approval if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_id'])) {
    $productId = $_POST['approve_id'];

    try {
        // Start transaction for data consistency
        $pdo->beginTransaction();

        // 1. Update product status to Approved - FIXED to use correct column name
        $updateStmt = $pdo->prepare("UPDATE products SET status = 'Approved', approved_by_finance = 1 WHERE id = ?");
        $updateStmt->execute([$productId]);

        // 2. Insert into approved_orders table
        $insertStmt = $pdo->prepare("INSERT INTO approved_orders (order_id, approval_date, approved_by) 
                                     VALUES (?, NOW(), ?)");
        $insertStmt->execute([$productId, $_SESSION['user_id'] ?? 1]); // Use logged-in user ID if available

        $pdo->commit();

        // Refresh the page to show updated data
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        $errorMessage = "Error approving product: " . $e->getMessage();
    }
}

// Process product rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reject_id'])) {
    $productId = $_POST['reject_id'];
    $rejectReason = $_POST['reject_reason'] ?? '';

    try {
        // Update product status to Rejected - FIXED to use correct column name
        $updateStmt = $pdo->prepare("UPDATE products SET status = 'Rejected', rejection_reason = ? WHERE id = ?");
        $updateStmt->execute([$rejectReason, $productId]);

        // Refresh the page to show updated data
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } catch (PDOException $e) {
        $errorMessage = "Error rejecting product: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="x-icon" href="../assets/images/eurospice-favicon.png">
    <title>Finance Department</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link
        href="https://cdn.prod.website-files.com/66a833f537135b05bc1eaecb/css/maria-bettinas-dynamite-site.webflow.05b59e178.css"
        rel="stylesheet" type="text/css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            background-color: #faf2e9;
            /* Light background for body */
            color: #333;
            /* Ensure text is visible */
        }

        .space {
            height: 54px;
            width: 100%;
        }

        #sidebar-nav {
            display: flex;
            flex-direction: column;
        }

        #sidebar-nav a {
            text-decoration: none;
            color: white;
            padding: 10px;
            background-color: #F15B31;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        #sidebar-nav a:hover {
            background-color: #D14118;
        }

        .offcanvas-body {
            background-color: #F15B31;
            padding: 0;
        }

        .offcanvas-header {
            background-color: #F15B31;
            color: white;
        }

        #navbar-container {
            position: fixed;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            padding: 5px;
            border-radius: 15px;
            width: 90%;
        }

        .body-container {
            padding: 10px;
            display: flex;
            height: 100vh;
            background-color: #faf2e9;
            flex-grow: 1;
            /* Allow this container to fill the screen */
            overflow-y: auto;
            /* Ensure scrolling when content overflows */
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            /* Sidebar size */
            background-color: #D14118;
            /* Sidebar background color */
            color: white;
            padding: 20px 10px;
            height: 100vh;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            /* Sidebar can scroll if needed */
            margin-top: 100px;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 12px 0;
            cursor: pointer;
        }

        .sidebar a:hover {
            background-color: #F15B31;
            /* Hover effect */
        }

        .sidebar .offcanvas-header {
            background-color: #D14118;
            color: white;
        }

        .sidebar .offcanvas-body {
            background-color: #D14118;
            padding: 0;
        }

        /* Main Content */
        .main-content {
            flex-grow: 1;
            padding: 20px;
            background-color: #faf2e9;
            overflow-y: auto;
            /* Make sure content is scrollable if needed */
            margin-top: 100px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: left;
        }

        th {
            background-color: #D14118;
            /* Table header color */
            color: white;
        }

        button {
            padding: 6px 12px;
            margin: 2px;
            font-weight: bold;
            cursor: pointer;
            background-color: #F15B31;
            /* Button color */
            color: white;
            border: none;
        }

        img {
            max-width: 60px;
            height: auto;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 20px;
            width: 300px;
            border-radius: 5px;
            position: relative;
        }

        .modal-content h3 {
            margin-top: 0;
        }

        .modal-content input,
        .modal-content select,
        .modal-content textarea {
            width: 100%;
            margin-bottom: 10px;
            padding: 5px;
        }

        .close-btn {
            background: red;
            color: white;
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 18px;
            cursor: pointer;
            border: none;
        }

        /* Status classes */
        .status-pending {
            color: #ff9800;
            font-weight: bold;
        }

        .status-approved {
            color: #4caf50;
            font-weight: bold;
        }

        .status-rejected {
            color: #f44336;
            font-weight: bold;
        }

        /* Tab container */
        .tab-container {
            margin-top: 20px;
        }

        .tab-buttons {
            display: flex;
            margin-bottom: 10px;
        }

        .tab-btn {
            padding: 10px 20px;
            cursor: pointer;
            background-color: #f2f2f2;
            border: none;
            margin-right: 5px;
            color: #f44336;
            border: #f44336 1px solid;
        }

        .tab-btn.active {
            background-color: #333;
            color: white;
        }

        .tab-content {
            display: none;
            margin-top: 100px;
            padding: 20px;
        }

        .tab-content.active {
            display: block;
            background-color: rgb(255, 204, 204);
        }

        .search-container {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }

        /* Navbar */
        .navbar {
            background-color: #D14118;
            /* Same color for navbar */
        }

        .navbar a {
            color: white;
            font-weight: bold;
            text-decoration: none;
        }

        .navbar .sidebar-emoji {
            font-size: 2rem;
        }

        .dropdown-menu {
            background-color: #F15B31;
            /* Dropdown menu background */
        }

        .dropdown-item {
            color: white;
        }

        .dropdown-item:hover {
            background-color: #D14118;
            /* Hover effect for dropdown */
        }

        .bg-primary {
            background-color: #D14118 !important;
        }

        #open-sidebar {
            text-decoration: none;
            color: white;
            font-weight: 800;
            font-size: 2rem;
            margin-left: 10px;
            margin-right: 10px;
        }

        .dropdown {
            margin-right: 10px;
        }

        #off-canvas-logo {
            max-width: 400px;
        }
    </style>

</head>

<body>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvas" aria-labelledby="offcanvasLabel">
        <img src="../assets/images/eurospice-logo.png" alt="Euro Spice Logo" width="100%" id="off-canvas-logo">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasLabel">Welcome Admin</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <nav id="sidebar-nav">
                <a href="admin_dashboard.php">Dashboard</a>
                <a href="finance.php">Finance</a>
                <a href="inventory.php">Inventory</a>
                <a href="supplier_profile.php">Supplier Profile</a>
                <a href="supplier_products.php">Supplier Products</a>
                <a href="user_profile.php">User Profiles</a>
                <a href="profile.php">Add User Profiles</a>

                <!-- Add more links as needed -->
            </nav>
        </div>
    </div>

    <div class="navbar navbar-dark bg-primary" id="navbar-container">
        <a class="sidebar-emoji" id="open-sidebar" data-bs-toggle="offcanvas" href="#offcanvas" role="button" aria-controls="offcanvas">
            ☰
        </a>

        <h4 style="color: white">Euro Spice ERP</h4>

        <div class="dropdown" id="user-settings">
            <a class="dropdown-toggle text-white text-decoration-none" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                👤
            </a>
            <ul class="dropdown-menu" id="user-dropdown" aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="#">Profile</a></li>
                <li><a class="dropdown-item" href="#">Settings</a></li>
                <li><a class="dropdown-item" href="#" onclick="logout(); return false;">Log Out</a></li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <h1>Finance Department</h1>
        <div id="currentDateTime"></div>

        <div id="dashboard" style="display: flex; gap: 20px; margin-bottom: 20px;">
            <div style="background: #f2f2f2; padding: 20px; border-radius: 8px;">
                <h3>Pending Orders</h3>
                <p id="pendingOrdersCount" style="font-size: 24px; font-weight: bold;">0</p>
            </div>
            <div style="background: #f2f2f2; padding: 20px; border-radius: 8px;">
                <h3>Today's Approved Budget</h3>
                <p id="todaysBudget">₱245,780.00</p>
            </div>
            <div style="background: #f2f2f2; padding: 20px; border-radius: 8px;">
                <h3>Monthly Expenses</h3>
                <p id="monthlyExpenses">₱1,854,320.00</p>
            </div>
            <div style="background: #f2f2f2; padding: 20px; border-radius: 8px;">
                <h3>Available Budget</h3>
                <p id="availableBudget">₱3,254,600.00</p>
            </div>
        </div>

        <div class="search-container">
            <input type="text" id="searchOrders" placeholder="Search orders...">
            <button class="btn btn-primary" onclick="searchOrders()">Search</button>
            <button class="btn btn-warning" onclick="resetSearch()">Reset</button>
        </div>

        <div class="tab-container">
            <div class="tab-buttons">
                <button class="tab-btn active" onclick="showTab('pendingOrders')">Pending Approvals</button>
                <button class="tab-btn" onclick="showTab('approvedOrders')">Approved Orders</button>
                <button class="tab-btn" onclick="showTab('rejectedOrders')">Rejected Orders</button>
                <button class="tab-btn" onclick="showTab('budgetAllocation')">Budget Allocation</button>
                <button class="tab-btn" onclick="showTab('budgetReports')">Budget Reports</button>
                <button class="tab-btn" onclick="showTab('dailyReports')">Daily Reports</button>
                <button class="tab-btn" onclick="showTab('monthlyReports')">Monthly Reports</button>
                <button class="tab-btn" onclick="showTab('yearlyReports')">Yearly Reports</button>
            </div>

            <!-- Pending Orders -->
            <div class="tab">
                <h2>Pending Orders</h2>
                <table id="pendingOrdersTable">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Name</th>
                            <th>Brand</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>Batch Code</th>
                            <th>Weight</th>
                            <th>Pack Type</th>
                            <th>Pack Size</th>
                            <th>Shelf Type</th>
                            <th>Expiration</th>
                            <th>Country</th>
                            <th>Delivered</th>
                            <th>Image</th>
                            <th>Notes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingProducts as $product): ?>
                            <tr>
                                <td><?= $product['id'] ?></td>
                                <td><?= $product['name'] ?></td>
                                <td><?= $product['brand'] ?></td>
                                <td><?= $product['stock'] ?></td>
                                <td>₱<?= number_format($product['price'], 2) ?></td>
                                <td><?= $product['batch_code'] ?></td>
                                <td><?= $product['weight'] ?></td>
                                <td><?= $product['pack_type'] ?></td>
                                <td><?= $product['pack_size'] ?></td>
                                <td><?= $product['shelf_type'] ?></td>
                                <td><?= $product['expiration'] ?></td>
                                <td><?= $product['country'] ?></td>
                                <td><?= $product['delivered'] ?></td>
                                <td><img src="<?= $product['image'] ?>" alt="Image" style="max-width: 50px;"></td>
                                <td><?= $product['notes'] ?></td>
                                <td>
                                    <button onclick='openProductModal(<?= json_encode($product) ?>)'>View</button>
                                    <button onclick='approveProduct(<?= $product["id"] ?>)'>Approve</button>
                                    <button onclick='rejectProduct(<?= $product["id"] ?>)'>Reject</button>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Approved Orders -->
            <div class="tab">
                <h2>Approved Orders</h2>
                <table id="approvedOrdersTable">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Name</th>
                            <th>Brand</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>Batch Code</th>
                            <th>Weight</th>
                            <th>Pack Type</th>
                            <th>Pack Size</th>
                            <th>Shelf Type</th>
                            <th>Expiration</th>
                            <th>Country</th>
                            <th>Delivered</th>
                            <th>Image</th>
                            <th>Notes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($approvedProducts as $product): ?>
                            <tr>
                                <td><?= $product['id'] ?></td>
                                <td><?= $product['name'] ?></td>
                                <td><?= $product['brand'] ?></td>
                                <td><?= $product['stock'] ?></td>
                                <td>₱<?= number_format($product['price'], 2) ?></td>
                                <td><?= $product['batch_code'] ?></td>
                                <td><?= $product['weight'] ?></td>
                                <td><?= $product['pack_type'] ?></td>
                                <td><?= $product['pack_size'] ?></td>
                                <td><?= $product['shelf_type'] ?></td>
                                <td><?= $product['expiration'] ?></td>
                                <td><?= $product['country'] ?></td>
                                <td><?= $product['delivered'] ?></td>
                                <td><img src="<?= $product['image'] ?>" alt="Image" style="max-width: 50px;"></td>
                                <td><?= $product['notes'] ?></td>
                                <td>
                                    <button onclick='openProductModal(<?= json_encode($product) ?>)'>View</button>
                                    <button onclick='approveProduct(<?= $product["id"] ?>)'>Approve</button>
                                    <button onclick='rejectProduct(<?= $product["id"] ?>)'>Reject</button>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Rejected Orders -->
            <div class="tab">
                <h2>Rejected Orders</h2>
                <table id="rejectedOrdersTable">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Name</th>
                            <th>Brand</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>Batch Code</th>
                            <th>Weight</th>
                            <th>Pack Type</th>
                            <th>Pack Size</th>
                            <th>Shelf Type</th>
                            <th>Expiration</th>
                            <th>Country</th>
                            <th>Delivered</th>
                            <th>Image</th>
                            <th>Notes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rejectedProducts as $product): ?>
                            <tr>
                                <td><?= $product['id'] ?></td>
                                <td><?= $product['name'] ?></td>
                                <td><?= $product['brand'] ?></td>
                                <td><?= $product['stock'] ?></td>
                                <td>₱<?= number_format($product['price'], 2) ?></td>
                                <td><?= $product['batch_code'] ?></td>
                                <td><?= $product['weight'] ?></td>
                                <td><?= $product['pack_type'] ?></td>
                                <td><?= $product['pack_size'] ?></td>
                                <td><?= $product['shelf_type'] ?></td>
                                <td><?= $product['expiration'] ?></td>
                                <td><?= $product['country'] ?></td>
                                <td><?= $product['delivered'] ?></td>
                                <td><img src="<?= $product['image'] ?>" alt="Image" style="max-width: 50px;"></td>
                                <td><?= $product['notes'] ?></td>
                                <td>
                                    <button onclick='openProductModal(<?= json_encode($product) ?>)'>View</button>
                                    <button onclick='approveProduct(<?= $product["id"] ?>)'>Approve</button>
                                    <button onclick='rejectProduct(<?= $product["id"] ?>)'>Reject</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>


            <!-- Budget Allocation Tab -->
            <div id="budgetAllocation" class="tab-content">
                <h2>Budget Allocation</h2>
                <table id="budgetAllocationTable" border="1">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Department</th>
                            <th>Total Budget</th>
                            <th>Used Budget</th>
                            <th>Remaining Budget</th>
                            <th>Last Updated</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>BUD-001</td>
                            <td>Warehouse</td>
                            <td>₱1,500,000.00</td>
                            <td>₱875,450.00</td>
                            <td>₱624,550.00</td>
                            <td>May 01, 2025</td>
                            <td>
                                <button onclick="adjustBudget('Warehouse')">Adjust Budget</button>
                                <button onclick="viewBudgetHistory('Warehouse')">View History</button>
                            </td>
                        </tr>
                        <tr>
                            <td>BUD-002</td>
                            <td>Logistics</td>
                            <td>₱1,200,000.00</td>
                            <td>₱534,780.00</td>
                            <td>₱665,220.00</td>
                            <td>May 01, 2025</td>
                            <td>
                                <button onclick="adjustBudget('Logistics')">Adjust Budget</button>
                                <button onclick="viewBudgetHistory('Logistics')">View History</button>
                            </td>
                        </tr>
                        <tr>
                            <td>BUD-003</td>
                            <td>Sales</td>
                            <td>₱800,000.00</td>
                            <td>₱344,890.00</td>
                            <td>₱455,110.00</td>
                            <td>May 01, 2025</td>
                            <td>
                                <button onclick="adjustBudget('Sales')">Adjust Budget</button>
                                <button onclick="viewBudgetHistory('Sales')">View History</button>
                            </td>
                        </tr>
                        <tr>
                            <td>BUD-004</td>
                            <td>Admin</td>
                            <td>₱600,000.00</td>
                            <td>₱245,680.00</td>
                            <td>₱354,320.00</td>
                            <td>May 01, 2025</td>
                            <td>
                                <button onclick="adjustBudget('Admin')">Adjust Budget</button>
                                <button onclick="viewBudgetHistory('Admin')">View History</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Budget Reports Tab -->
            <div id="budgetReports" class="tab-content">
                <h2>Budget Reports</h2>
                <div class="search-container">
                    <select id="reportPeriod">
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                    <button onclick="generateBudgetReport()">Generate Report</button>
                </div>
                <div id="budgetReportContainer">
                    <p>Select a period and click "Generate Report" to view budget reports.</p>
                </div>
            </div>

            <!-- Daily Reports Tab -->
            <div id="dailyReports" class="tab-content">
                <h2>Daily Financial Reports</h2>
                <div class="search-container">
                    <input type="date" id="dailyReportDate" value="2025-05-02">
                    <button onclick="generateDailyReport()">View Report</button>
                </div>
                <div id="dailyReportContainer">
                    <p>Select a date and click "View Report" to see daily financial reports.</p>
                </div>
            </div>

            <!-- Monthly Reports Tab -->
            <div id="monthlyReports" class="tab-content">
                <h2>Monthly Financial Reports</h2>
                <div class="search-container">
                    <select id="monthSelect">
                        <option value="1">January 2025</option>
                        <option value="2">February 2025</option>
                        <option value="3">March 2025</option>
                        <option value="4">April 2025</option>
                        <option value="5" selected>May 2025</option>
                    </select>
                    <button onclick="generateMonthlyReport()">View Report</button>
                </div>
                <div id="monthlyReportContainer">
                    <p>Select a month and click "View Report" to see monthly financial reports.</p>
                </div>
            </div>

            <!-- Yearly Reports Tab -->
            <div id="yearlyReports" class="tab-content">
                <h2>Yearly Financial Reports</h2>
                <div class="search-container">
                    <select id="yearSelect">
                        <option value="2023">2023</option>
                        <option value="2024">2024</option>
                        <option value="2025" selected>2025 (YTD)</option>
                    </select>
                    <button onclick="generateYearlyReport()">View Report</button>
                </div>
                <div id="yearlyReportContainer">
                    <p>Select a year and click "View Report" to see yearly financial reports.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Details Modal -->
    <div id="productDetailsModal" class="modal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal('productDetailsModal')">×</button>
            <h3>Product Details</h3>
            <div id="productDetailsContent">
                <p><strong>Product ID:</strong> <span id="modalProductId"></span></p>
                <p><strong>Name:</strong> <span id="modalName"></span></p>
                <p><strong>Brand:</strong> <span id="modalBrand"></span></p>
                <p><strong>Stock:</strong> <span id="modalStock"></span></p>
                <p><strong>Price:</strong> <span id="modalPrice"></span></p>

                <!-- Hidden input for finalizeApproval -->
                <input type="hidden" id="approvalProductId" name="approve_id">

                <div class="detail-col">
                    <p><strong>Batch Code:</strong> <span id="modalBatchCode"></span></p>
                    <p><strong>Weight:</strong> <span id="modalWeight"></span></p>
                    <p><strong>Pack Type:</strong> <span id="modalPackType"></span></p>
                    <p><strong>Pack Size:</strong> <span id="modalPackSize"></span></p>
                    <p><strong>Shelf Type:</strong> <span id="modalShelfType"></span></p>
                </div>

                <div class="detail-col">
                    <p><strong>Expiration Date:</strong> <span id="modalExpiration"></span></p>
                    <p><strong>Country of Origin:</strong> <span id="modalCountry"></span></p>
                    <p><strong>Delivered:</strong> <span id="modalDelivered"></span></p>
                </div>

                <p><strong>Notes:</strong> <span id="modalNotes"></span></p>
                <p><strong>Image:</strong><br>
                    <img id="modalImage" src="" alt="Product Image" style="max-width: 100px; height: auto;">
                </p>

                <div id="approvalSection">
                    <h4>Approval Action</h4>
                    <div>
                        <label for="approvalNotes">Notes:</label>
                        <textarea id="approvalNotes" rows="3" placeholder="Enter notes for approval/rejection"></textarea>
                    </div>
                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                        <!-- Finalize Approval Button -->
                        <button onclick="finalizeApproval()">Finalize Approval</button>

                        <!-- Finalize Rejection Button -->
                        <button onclick="finalizeRejection(document.getElementById('modalProductId').textContent)">Finalize Rejection</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="tab-content" id="pendingProductsTab">
        <div class="section-header">
            <h2>Pending Product Approvals</h2>
            <p>Review and approve new products added by suppliers</p>
            <div class="badge-counter">
                <span id="pendingProductsCount" class="badge bg-danger">0</span> pending
            </div>
        </div>

        <div class="search-filter-container">
            <div class="input-group mb-3">
                <input type="text" id="searchProducts" class="form-control" placeholder="Search products...">
                <button class="btn btn-outline-secondary" type="button" onclick="searchProducts()">Search</button>
                <button class="btn btn-outline-secondary" type="button" onclick="resetProductSearch()">Reset</button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover" id="pendingProductsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Brand</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Batch</th>
                        <th>Supplier</th>
                        <th>Date Added</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Product rows will be loaded here dynamically -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Product Details Modal -->
    <div class="modal fade" id="productDetailsModal" tabindex="-1" aria-labelledby="productDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="productDetailsModalLabel">Product Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div id="modalProductImage" class="text-center mb-3"></div>
                        </div>
                        <div class="col-md-8">
                            <h4 id="modalProductName"></h4>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Product ID:</strong> <span id="modalProductId"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Brand:</strong> <span id="modalProductBrand"></span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Price:</strong> <span id="modalProductPrice"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Stock:</strong> <span id="modalProductStock"></span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Batch Code:</strong> <span id="modalProductBatchCode"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Weight:</strong> <span id="modalProductWeight"></span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Pack Type:</strong> <span id="modalProductPackType"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Pack Size:</strong> <span id="modalProductPackSize"></span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Shelf Type:</strong> <span id="modalProductShelfType"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Expiration:</strong> <span id="modalProductExpiration"></span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Country:</strong> <span id="modalProductCountry"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Delivered:</strong> <span id="modalProductDelivered"></span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <strong>Supplier Notes:</strong>
                                <p id="modalProductNotes" class="border p-2 rounded"></p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12">
                            <h5>Finance Review</h5>
                            <form id="productApprovalForm">
                                <input type="hidden" id="approvalProductId" name="product_id">
                                <div class="mb-3">
                                    <label for="approvalNotes" class="form-label">Finance Notes</label>
                                    <textarea class="form-control" id="approvalNotes" rows="3" placeholder="Enter comments about this product"></textarea>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" onclick="rejectProduct()">Reject</button>
                    <button type="button" class="btn btn-success" onclick="approveProduct()">Approve</button>
                </div>
            </div>
        </div>
    </div>



    <!-- Adjust Budget Modal -->
    <div id="adjustBudgetModal" class="modal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal('adjustBudgetModal')">×</button>
            <h3>Adjust Budget: <span id="budgetDepartment">Department</span></h3>

            <div>
                <label for="currentBudget">Current Budget:</label>
                <input type="text" id="currentBudget" readonly>
            </div>

            <div>
                <label for="budgetAdjustment">New Budget Amount:</label>
                <input type="number" id="budgetAdjustment" placeholder="Enter new budget amount">
            </div>

            <div>
                <label for="adjustmentReason">Reason for Adjustment:</label>
                <textarea id="adjustmentReason" rows="3" placeholder="Explain the reason for this budget adjustment"></textarea>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 10px;">
                <button onclick="saveBudgetAdjustment()">Save Changes</button>
                <button onclick="closeModal('adjustBudgetModal')">Cancel</button>
            </div>
        </div>
    </div>

    <div id="budgetHistoryModal" class="modal">
        <div class="modal-content" style="width: 600px;">
            <button class="close-btn" onclick="closeModal('budgetHistoryModal')">×</button>
            <h3>Budget History: <span id="historyDepartment">Department</span></h3>
            <table id="budgetHistoryTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Previous Budget</th>
                        <th>New Budget</th>
                        <th>Change</th>
                        <th>Reason</th>
                        <th>Adjusted By</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Sample data - would be populated dynamically -->
                </tbody>
            </table>
        </div>
    </div>
    <script src="finc.js"></script>

    <script>
        // Logout function with notification
        function logout() {
            // Show confirmation dialog
            if (confirm("Are you sure you want to log out?")) {
                // Create notification element
                const notification = document.createElement('div');
                notification.style.position = 'fixed';
                notification.style.top = '20px';
                notification.style.left = '50%';
                notification.style.transform = 'translateX(-50%)';
                notification.style.backgroundColor = '#D14118';
                notification.style.color = 'white';
                notification.style.padding = '15px 25px';
                notification.style.borderRadius = '5px';
                notification.style.zIndex = '9999';
                notification.style.boxShadow = '0 4px 8px rgba(0,0,0,0.2)';
                notification.textContent = 'Logging you out...';

                // Add notification to body
                document.body.appendChild(notification);

                // Set timeout to redirect after showing notification
                setTimeout(function() {
                    window.location.href = 'login.php';
                }, 1500); // Redirect after 1.5 seconds
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
        crossorigin="anonymous"></script>
</body>

</html>