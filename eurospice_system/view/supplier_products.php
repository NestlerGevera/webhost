<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Euro Spice | Supplier </title>
    <link rel="icon" type="x-icon" href="../assets/images/eurospice-favicon.png">
    <link rel="stylesheet" href="../css/responsive.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link
        href="https://cdn.prod.website-files.com/66a833f537135b05bc1eaecb/css/maria-bettinas-dynamite-site.webflow.05b59e178.css"
        rel="stylesheet" type="text/css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

        @font-face {
            font-family: 'Maragsa Display';
            src: url('../assets/fonts/Maragsa-Display.otf') format('opentype');
            font-weight: normal;
            font-style: normal;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-image: url('../assets/images/eurospice-grid.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            height: 700px;
        }

        body,
        html {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .navbar-container h3 {
            font-size: 1rem;
            text-align: center;
            font-family: 'Poppins', sans-serif;
            padding: 10px;
            color: white;
        }

        .navbar-container h3 a {
            text-decoration: underline;
        }

        .navbar {
            background-color: #b82720;
        }

        .body-container {
            width: 100%;
            height: 100%;
            display: grid;
            place-items: center;
        }

        .section-container {
            display: grid;
            place-items: center;
            min-height: 600px;
            background-color: #faf2e9;
            overflow: hidden;
            border-radius: 20px;
            width: 1200px;
        }

        .container-me {
            font-size: 10px;
            width: 1200px;
            max-width: 100%;
            height: 100%;
            margin: 0 auto;
            display: grid;
            place-items: center;
        }

        #store-header img {
            width: 1200px;
        }

        #store-categories {
            display: flex;
            justify-content: space-between;
            gap: 5px;
        }

        #store-categories a:hover {
            transform: scale(1.05);
            transition: all 0.3s ease-in-out;
        }

        .products_table {
            width: 80%;
            overflow-x: auto;
        }

        .action-buttons {
            margin: 20px 0;
        }

        .log-out-btn {
            color: white;
            text-decoration: none;
            padding: 5px 10px;
        }

        .log-out-btn:hover {
            color: #ffc107;
        }

        #alert-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            width: 350px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-transparent">
        <div class="container-fluid">

            <a class="navbar-brand text-white" href="#"><img src="../assets/images/eurospice-logo-white-no-bg.svg"
                    alt=""></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active text-white" aria-current="page" href="../view/supplier_pos.php">Order Requests</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active text-white" aria-current="page" href="../view/supplier_products.php">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active text-white" aria-current="page" href="../view/supplier_profile.php">Profile</a>
                    </li>
                </ul>

                <div class="user-container d-flex me-auto mb-2 mb-lg-0">
                    <a class="log-out-btn" href="../index.html">Log out</a>
                </div>

                <div class="user-container d-flex me-auto mb-2 mb-lg-0">
                    <a class="log-out-btn" href="../view/cart.php">🛒</a>
                </div>

                <form class="d-flex" role="search">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-warning" type="submit">Search</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Alert Container for Messages -->
    <div id="alert-container"></div>

    <div class="body-container">
        <div class="section-container" id="">
            <div class="container-me" id="store-header">
                <h1>Welcome to Euro Spice Supplier Module</h1>
            </div>

            <div class="action-buttons">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">Add Product</button>
            </div>

            <div class="products_table">
                <h4>Products</h4>
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
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
                            <th>Approved</th>
                            <th>Finance Notes</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Products will be loaded dynamically via JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- Add Product Modal -->
            <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="addProductForm" action="../product_operations.php" method="POST" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label>Name</label>
                                        <input type="text" name="name" class="form-control" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Brand</label>
                                        <input type="text" name="brand" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Stock</label>
                                        <input type="number" name="stock" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Price</label>
                                        <input type="number" step="0.01" name="price" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Batch Code</label>
                                        <input type="text" name="batchCode" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Weight</label>
                                        <input type="text" name="weight" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Pack Type</label>
                                        <input type="text" name="packtype" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Pack Size</label>
                                        <input type="text" name="packsize" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Shelf Type</label>
                                        <input type="text" name="shelftype" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Expiration Date</label>
                                        <input type="datetime-local" name="expirationDate" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Country</label>
                                        <input type="text" name="country" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Delivered</label>
                                        <input type="datetime-local" name="delivered" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Image</label>
                                        <input type="file" name="image" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Approved by Finance</label>
                                        <select name="approved_by_finance" class="form-select">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label>Finance Notes</label>
                                        <textarea name="finance_notes" class="form-control" rows="2"></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label>Status</label>
                                        <input type="text" name="status" class="form-control" value="Active">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success">Add Product</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Product Modal -->
            <!-- Updated Add Product Modal -->
            <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="addProductForm" action="../product_operations.php" method="POST" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> New products will be submitted for finance approval before they are active in the system.
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label>Name</label>
                                        <input type="text" name="name" class="form-control" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Brand</label>
                                        <input type="text" name="brand" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Stock</label>
                                        <input type="number" name="stock" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Price</label>
                                        <input type="number" step="0.01" name="price" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Batch Code</label>
                                        <input type="text" name="batchCode" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Weight</label>
                                        <input type="text" name="weight" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Pack Type</label>
                                        <input type="text" name="packtype" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Pack Size</label>
                                        <input type="text" name="packsize" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Shelf Type</label>
                                        <input type="text" name="shelftype" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Expiration Date</label>
                                        <input type="datetime-local" name="expirationDate" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Country</label>
                                        <input type="text" name="country" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Delivered</label>
                                        <input type="datetime-local" name="delivered" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Image</label>
                                        <input type="file" name="image" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Notes for Finance (Optional)</label>
                                        <textarea name="supplier_notes" class="form-control" rows="2"
                                            placeholder="Add any comments for the finance department"></textarea>
                                    </div>
                                    <!-- Hidden fields for finance approval process -->
                                    <input type="hidden" name="approved_by_finance" value="0">
                                    <input type="hidden" name="finance_status" value="pending">
                                    <input type="hidden" name="status" value="Pending Approval">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success">Submit for Approval</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
        crossorigin="anonymous"></script>
    <script>
        // Wait for the DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize event listeners
            initializeEventListeners();

            // Load products when page loads
            loadProducts();
        });

        // Initialize all event listeners
        function initializeEventListeners() {
            // Add product form submission
            const addProductForm = document.getElementById('addProductForm');
            if (addProductForm) {
                addProductForm.addEventListener('submit', handleAddProduct);
            }

            // Edit product form submission
            const editProductForm = document.getElementById('editProductForm');
            if (editProductForm) {
                editProductForm.addEventListener('submit', handleUpdateProduct);
            }

            // Add click event listener for edit and delete buttons
            // Using event delegation since elements might be added dynamically
            document.addEventListener('click', function(event) {
                // Edit product button
                if (event.target && event.target.classList.contains('edit-product-btn')) {
                    const productId = event.target.getAttribute('data-id');
                    openEditProductModal(productId);
                }

                // Delete product button
                if (event.target && event.target.classList.contains('delete-product-btn')) {
                    const productId = event.target.getAttribute('data-id');
                    confirmDeleteProduct(productId);
                }
            });
        }

        // Load all products from the server
        function loadProducts() {
            fetch('../components/product_operations.php', {
                    method: 'GET'
                })
                .then(response => response.text()) // <-- change to .text() for now
                .then(text => {
                    console.log('Raw response:', text);
                    const data = JSON.parse(text); // Try to parse manually
                    if (data.success) {
                        displayProducts(data.products);
                    } else {
                        showAlert('Error loading products', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Failed to load products. Please try again.', 'danger');
                });
        }


        // Display products in the table
        function displayProducts(products) {
            const tableBody = document.querySelector('.products_table table tbody');

            if (!tableBody) {
                console.error('Products table body not found');
                return;
            }

            // Clear existing rows
            tableBody.innerHTML = '';

            // Add products to table
            products.forEach((product, index) => {
                const row = document.createElement('tr');

                row.innerHTML = `
            <td>${index + 1}</td>
            <td>${product.name || ''}</td>
            <td>${product.brand || ''}</td>
            <td>${product.stock || '0'}</td>
            <td>P${parseFloat(product.price || 0).toFixed(2)}</td>
            <td>${product.batchCode || ''}</td>
            <td>${product.weight || ''}</td>
            <td>${product.packtype || ''}</td>
            <td>${product.packsize || ''}</td>
            <td>${product.shelftype || ''}</td>
            <td>${formatDate(product.expirationDate)}</td>
            <td>${product.country || ''}</td>
            <td>${formatDate(product.delivered)}</td>
            <td>${product.image ? `<img src="../uploads/${product.image}" width="50" alt="Product Image">` : 'No image'}</td>
            <td>${product.approved_by_finance === '1' ? 'Yes' : 'No'}</td>
            <td>${product.finance_notes || ''}</td>
            <td>${product.status || ''}</td>
            <td>
                <button class="btn btn-sm btn-warning edit-product-btn" data-id="${product.id}">Edit</button>
                <button class="btn btn-sm btn-danger delete-product-btn" data-id="${product.id}">Delete</button>
            </td>
        `;

                tableBody.appendChild(row);
            });
        }

        // Handle add product form submission
        function handleAddProduct(event) {
            event.preventDefault();

            const form = event.target;
            const formData = new FormData(form);

            // Set finance approval status to pending (0) by default for new products
            formData.set('approved_by_finance', '0');
            formData.set('finance_status', 'pending');

            // Add action type
            formData.append('action', 'add_product');

            // Add notification flag for finance department
            formData.append('notify_finance', '1');

            fetch('../components/product_operations.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Hide modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('addProductModal'));
                        modal.hide();

                        // Reset form
                        form.reset();

                        // Show success message with finance notification
                        showAlert(data.message + ' Product has been submitted for finance approval.', 'success');

                        // Reload products
                        loadProducts();
                    } else {
                        showAlert(data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Failed to add product. Please try again.', 'danger');
                });
        }

        // Open edit product modal and load product data
        function openEditProductModal(productId) {
            const formData = new FormData();
            formData.append('action', 'get_product');
            formData.append('id', productId);

            fetch('../components/product_operations.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const product = data.product;

                        // Fill edit modal with product data
                        document.getElementById('edit_id').value = product.id;
                        document.getElementById('edit_name').value = product.name || '';
                        document.getElementById('edit_brand').value = product.brand || '';
                        document.getElementById('edit_stock').value = product.stock || '';
                        document.getElementById('edit_price').value = product.price || '';
                        document.getElementById('edit_batchCode').value = product.batchCode || '';
                        document.getElementById('edit_weight').value = product.weight || '';
                        document.getElementById('edit_packtype').value = product.packtype || '';
                        document.getElementById('edit_packsize').value = product.packsize || '';
                        document.getElementById('edit_shelftype').value = product.shelftype || '';
                        document.getElementById('edit_expirationDate').value = formatDateForInput(product.expirationDate);
                        document.getElementById('edit_country').value = product.country || '';
                        document.getElementById('edit_delivered').value = formatDateForInput(product.delivered);
                        document.getElementById('edit_approved_by_finance').value = product.approved_by_finance || '0';
                        document.getElementById('edit_finance_notes').value = product.finance_notes || '';
                        document.getElementById('edit_status').value = product.status || '';

                        if (product.image) {
                            document.getElementById('current_image').innerHTML = `
                        <img src="../uploads/${product.image}" width="100" class="mb-2" alt="Current Product Image">
                        <input type="hidden" name="current_image" value="${product.image}">
                    `;
                        } else {
                            document.getElementById('current_image').innerHTML = 'No image';
                        }

                        // Show edit modal
                        const editModal = new bootstrap.Modal(document.getElementById('editProductModal'));
                        editModal.show();
                    } else {
                        showAlert(data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Failed to load product details. Please try again.', 'danger');
                });
        }

        // Handle update product form submission
        function handleUpdateProduct(event) {
            event.preventDefault();

            const form = event.target;
            const formData = new FormData(form);
            formData.append('action', 'update_product');

            fetch('../components/product_operations.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Hide modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editProductModal'));
                        modal.hide();

                        // Show success message
                        showAlert(data.message, 'success');

                        // Reload products
                        loadProducts();
                    } else {
                        showAlert(data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Failed to update product. Please try again.', 'danger');
                });
        }

        // Confirm and delete product
        function confirmDeleteProduct(productId) {
            if (confirm('Are you sure you want to delete this product?')) {
                const formData = new FormData();
                formData.append('action', 'delete_product');
                formData.append('id', productId);

                fetch('../components/product_operations.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showAlert(data.message, 'success');
                            loadProducts();
                        } else {
                            showAlert(data.message, 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('Failed to delete product. Please try again.', 'danger');
                    });
            }
        }

        // Show alert message
        function showAlert(message, type) {
            // Create alert element
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.role = 'alert';
            alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

            // Find alert container
            const alertContainer = document.getElementById('alert-container');

            // Add alert to container
            alertContainer.appendChild(alertDiv);

            // Auto-remove alert after 5 seconds
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }

        // Helper function to format date for display
        function formatDate(dateString) {
            if (!dateString) return '';

            const date = new Date(dateString);
            if (isNaN(date.getTime())) return dateString; // Return original if invalid

            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        // Helper function to format date for datetime-local input
        function formatDateForInput(dateString) {
            if (!dateString) return '';

            const date = new Date(dateString);
            if (isNaN(date.getTime())) return ''; // Return empty if invalid

            // Format as YYYY-MM-DDThh:mm
            return date.toISOString().slice(0, 16);
        }
    </script>
</body>

</html>