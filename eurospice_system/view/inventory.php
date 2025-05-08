<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Approved Products Inventory</title>
    <link rel="icon" type="x-icon" href="../assets/images/eurospice-favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link
        href="https://cdn.prod.website-files.com/66a833f537135b05bc1eaecb/css/maria-bettinas-dynamite-site.webflow.05b59e178.css"
        rel="stylesheet" type="text/css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #faf2e9;
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
            max-height: 200px;
        }

        h2 {
            margin-bottom: 10px;
        }

        .filters {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        select {
            padding: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 14px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        img {
            max-width: 60px;
            max-height: 60px;
        }

        .status-delivered {
            background-color: #d4edda;
        }

        .status-pending {
            background-color: #fff3cd;
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
                <!-- <a href="supplier_profile.php">Supplier Profile</a>
                    <a href="supplier_products.php">Supplier Products</a> -->
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

    <div class="spacer-xxlarge"></div>

    <h2>Approved Products Inventory</h2>

    <div class="filters">
        <div>
            <label for="brandFilter">Filter by Brand:</label>
            <select id="brandFilter">
                <option value="All">All Brands</option>
            </select>
        </div>
        <div>
            <label for="countryFilter">Filter by Country:</label>
            <select id="countryFilter">
                <option value="All">All Countries</option>
            </select>
        </div>
    </div>

    <table id="productTable">
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Brand</th>
                <th>Stock</th>
                <th>Price</th>
                <th>Batch Code</th>
                <th>Weight</th>
                <th>Pack Type</th>
                <th>Pack Size</th>
                <th>Shelf Type</th>
                <th>Expiration Date</th>
                <th>Country</th>
                <th>Delivered</th>
            </tr>
        </thead>
        <tbody>
            <!-- Products will be inserted here -->
        </tbody>
    </table>

    <script>
        const brandSelect = document.getElementById("brandFilter");
        const countrySelect = document.getElementById("countryFilter");
        const tableBody = document.querySelector("#productTable tbody");
        let allProducts = [];

        // Fetch products from the approved_orders connection
        fetch("../components/get_approved_products.php")
            .then(res => res.json())
            .then(data => {
                allProducts = data;
                populateFilterOptions(data);
                displayProducts(data);
            })
            .catch(err => {
                console.error("Error loading products:", err);
                alert("Failed to load approved products. Please check your internet connection or try again later.");
            });

        function populateFilterOptions(products) {
            // Add unique brands to the brand filter
            const brands = new Set(products.map(p => p.brand).filter(Boolean));
            brands.forEach(brand => {
                const option = document.createElement("option");
                option.value = brand;
                option.textContent = brand;
                brandSelect.appendChild(option);
            });

            // Add unique countries to the country filter
            const countries = new Set(products.map(p => p.country).filter(Boolean));
            countries.forEach(country => {
                const option = document.createElement("option");
                option.value = country;
                option.textContent = country;
                countrySelect.appendChild(option);
            });
        }

        function displayProducts(products) {
            tableBody.innerHTML = "";
            products.forEach(p => {
                const row = document.createElement("tr");

                // Add row class based on delivery status
                if (p.delivered === "Yes") {
                    row.classList.add("status-delivered");
                } else if (p.delivered === "No") {
                    row.classList.add("status-pending");
                }

                row.innerHTML = `
                    <td><img src="${p.image || '/assets/images/no-image.png'}" alt="${p.name}" /></td>
                    <td>${p.name}</td>
                    <td>${p.brand || ''}</td>
                    <td>${p.stock || '0'}</td>
                    <td>${p.price || '0.00'}</td>
                    <td>${p.batchCode || ''}</td>
                    <td>${p.weight || ''}</td>
                    <td>${p.packtype || ''}</td>
                    <td>${p.packsize || ''}</td>
                    <td>${p.shelftype || ''}</td>
                    <td>${p.expirationDate || ''}</td>
                    <td>${p.country || ''}</td>
                    <td>${p.delivered || 'No'}</td>
                `;
                tableBody.appendChild(row);
            });
        }

        // Filter products when brand selection changes
        brandSelect.addEventListener("change", filterProducts);

        // Filter products when country selection changes
        countrySelect.addEventListener("change", filterProducts);

        function filterProducts() {
            const selectedBrand = brandSelect.value;
            const selectedCountry = countrySelect.value;

            // Apply both filters
            let filtered = allProducts;

            if (selectedBrand !== "All") {
                filtered = filtered.filter(p => p.brand === selectedBrand);
            }

            if (selectedCountry !== "All") {
                filtered = filtered.filter(p => p.country === selectedCountry);
            }

            displayProducts(filtered);
        }
    </script>

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