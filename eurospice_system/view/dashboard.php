<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Euro Spice | Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="x-icon" href="../assets/images/eurospice-favicon.png">
    <style>
        body {
            background-color: #F15B31;
        }

        .space {
            height: 54px;
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


        .container-me {
            background-color: #faf2e9;
            width: 100%;
            height: 100vh;
            border-radius: 5px;
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
    </style>
</head>

<body>
    <div class="space"></div>
    <section class="body-container">
        <!-- Offcanvas Sidebar -->
        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvas" aria-labelledby="offcanvasLabel">
            <img src="../assets/images/eurospice-logo.png" alt="Euro Spice Logo" width="100%">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasLabel">Welcome User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <nav id="sidebar-nav">
                    <a href="dashboard.php">Dashboard</a>
                    <a href="#">User Profile</a>
                    <a href="#">User Accounts</a>
                    <a href="#">Add Profile</a>

                    <!-- Add more links as needed -->
                </nav>
            </div>
        </div>

        <div class="navbar navbar-dark bg-primary" id="navbar-container">
            <a class="sidebar-emoji" id="open-sidebar" data-bs-toggle="offcanvas" href="#offcanvas" role="button" aria-controls="offcanvas">
                ☰
            </a>
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

        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Logout function with notification
        function logout() {
            // Show confirmation dialog
            if (confirm("Are you sure you want to lowg out?")) {
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
</body>

</html>