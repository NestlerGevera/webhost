<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Euro Spice | Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="x-icon" href="../assets/images/eurospice-favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link
        href="https://cdn.prod.website-files.com/66a833f537135b05bc1eaecb/css/maria-bettinas-dynamite-site.webflow.05b59e178.css"
        rel="stylesheet" type="text/css">
    <style>
        body {
            background-color: #F15B31;
        }

        .space {
            height: 80px;
            width: 100%;
        }

        .body-container {
            padding: 10px;
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

        .container-me {
            background-color: #faf2e9;
            width: 100%;
            height: 1000px;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="space"></div>
    <section class="body-container">
        <!-- Offcanvas Sidebar -->
        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvas" aria-labelledby="offcanvasLabel">
            <img src="../assets/images/eurospice-logo.png" alt="Euro Spice Logo" width="100%">
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

        <div class="container-me">
            <div class="spacer-large"></div>

            <div id="currentDateTime" style="width: 100%; text-align: center;"></div>
            <h1 style="text-align: center; margin-top: 20px;">Welcome to Admin Dashboard</h1>

            <div id="dashboard" style="display: flex; justify-content: space-around; width: 100%; gap: 20px;">
                <div style="background: #f2f2f2; padding: 20px; border-radius: 8px; border: 1px solid red; margin-top: 20px;">
                    <h3>Pending Orders</h3>
                    <p id="pendingOrdersCount" style="font-size: 24px; font-weight: bold;">0</p>
                </div>
                <div style="background: #f2f2f2; padding: 20px; border-radius: 8px; border: 1px solid red; margin-top: 20px;">
                    <h3>Today's Approved Budget</h3>
                    <p id="todaysBudget">₱245,780.00</p>
                </div>
                <div style="background: #f2f2f2; padding: 20px; border-radius: 8px; border: 1px solid red; margin-top: 20px;">
                    <h3>Monthly Expenses</h3>
                    <p id="monthlyExpenses">₱1,854,320.00</p>
                </div>
                <div style="background: #f2f2f2; padding: 20px; border-radius: 8px; border: 1px solid red; margin-top: 20px;">
                    <h3>Available Budget</h3>
                    <p id="availableBudget">₱3,254,600.00</p>
                </div>
            </div>

            <div class="spacer-large"></div>

            <div class="users-dash" style="width: 100%; display: flex; justify-content: space-around; flex-wrap: wrap; gap: 20px;">
                <div class="card" style="width: 18rem;">
                    <img src="../assets/images/users-pic.svg" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title">Users</h5>
                        <p class="card-text">Dito Number ng Users na nagamit Nelle</p>
                        <br>
                        <a href="user_profile.php" class="btn btn-primary">Go to User Profiles</a>
                    </div>
                </div>
                <div class="card" style="width: 18rem;">
                    <div class="image-container" style="width: 100%; display: grid; justify-content: center; align-items: center;">
                        <img src="../assets/images/inventory-pic.svg" class="card-img-top" alt="..." style="width: 250px;">
                    </div>
                    <div class=" card-body">
                        <h5 class="card-title">Inventory</h5>
                        <p class="card-text">Dito Number ng stocks sa Inventory Nelle</p>
                        <br>
                        <a href="inventory.php" class="btn btn-primary">Go to Inventory</a>
                    </div>
                </div>
            </div>

            <div class="spacer-large"></div>
        </div>

        <script>
            function updateDateTime() {
                const dateTimeElement = document.getElementById("currentDateTime");
                if (dateTimeElement) {
                    dateTimeElement.textContent = new Date().toLocaleString();
                }
            }
            setInterval(updateDateTime, 1000);
            updateDateTime();

            function toggleMenu() {
                document.getElementById("dropdownMenu").classList.toggle("show");
            }
        </script>
    </section>
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