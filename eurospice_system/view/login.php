<?php
session_start();
require_once '../config/database.php';

$loginError = "";
$registrationSuccess = isset($_GET['registered']) && $_GET['registered'] == 1;
$isAdminLogin = isset($_GET['admin']) && $_GET['admin'] == 1;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["email"], $_POST["password"])) {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $userType = isset($_POST["user_type"]) ? $_POST["user_type"] : "user";

    try {
        $db = new Database();
        $conn = $db->connect();

        // Check if logging in as a user, supplier, or admin
        if ($userType === "user") {
            $stmt = $conn->prepare("SELECT id, email, username, password FROM users WHERE email = ?");
            $sessionPrefix = "user";
            $redirect = "client_pos.php";
        } elseif ($userType === "supplier") {
            // Using the suppliers table structure from the database
            $stmt = $conn->prepare("SELECT id, email, username, password, brand_name FROM suppliers WHERE email = ?");
            $sessionPrefix = "supplier";
            $redirect = "supplier_dashboard.php"; // Create this page for suppliers
        } else {
            // Admin login
            $stmt = $conn->prepare("SELECT id, email, username, password FROM admins WHERE email = ?");
            $sessionPrefix = "admin";
            $redirect = "admin_dashboard.php"; // Create this page for admins
        }

        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($password, $user["password"])) {
                $_SESSION["{$sessionPrefix}_id"] = $user["id"];
                $_SESSION["{$sessionPrefix}_email"] = $user["email"];
                $_SESSION["{$sessionPrefix}_username"] = $user["username"];

                // Set login success flag
                $_SESSION["login_success"] = true;

                // Store brand name for suppliers
                if ($userType === "supplier" && isset($user["brand_name"])) {
                    $_SESSION["supplier_brand"] = $user["brand_name"];
                }

                // Record login timestamp (you may want to add this to your database)
                $timestamp = date('Y-m-d H:i:s');
                $_SESSION["{$sessionPrefix}_last_login"] = $timestamp;

                header("Location: $redirect");
                exit;
            } else {
                $loginError = "Invalid password.";
            }
        } else {
            $loginError = "Email not found.";
        }
    } catch (PDOException $e) {
        $loginError = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Euro Spice | Sign In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.prod.website-files.com/66a833f537135b05bc1eaecb/css/maria-bettinas-dynamite-site.webflow.05b59e178.css" rel="stylesheet">
    <link rel="icon" type="x-icon" href="../assets/images/eurospice-favicon.png">
    <style>
        body {
            width: 100%;
            height: 100%;
            display: grid;
            place-items: center;
            background-color: #F15B31;
        }

        .container-me {
            width: 400px;
            background-color: white;
            padding: 40px;
            border-radius: 30px;
            margin-top: 100px;
            box-shadow: -30px 30px 0px rgba(0, 0, 0, 0.1);
        }

        p {
            margin-top: 30px;
            margin-bottom: 10px;
        }

        .user-type-selector {
            margin-bottom: 20px;
        }

        .user-type-selector .btn {
            width: 33.33%;
        }

        .user-type-selector .btn.active {
            background-color: #F15B31;
            color: white;
            border-color: #F15B31;
        }

        .admin-badge {
            background-color: #343a40;
            color: white;
            font-size: 0.8rem;
            padding: 3px 8px;
            border-radius: 10px;
            margin-left: 5px;
        }
    </style>
</head>

<body>
    <div class="container-me">
        <h1 class="text-center mb-4">Sign In</h1>

        <?php if ($registrationSuccess): ?>
            <?php if (isset($_GET['admin']) && $_GET['admin'] == 1): ?>
                <div class="alert alert-success">Admin registration successful! Please sign in with your credentials.</div>
            <?php else: ?>
                <div class="alert alert-success">Registration successful! Please sign in with your credentials.</div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (!empty($loginError)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($loginError) ?></div>
        <?php endif; ?>

        <!-- User type selection buttons -->
        <div class="user-type-selector btn-group mb-3 w-100">
            <button type="button" class="btn btn-outline-warning <?= $isAdminLogin ? '' : 'active' ?>" onclick="setUserType('user')">Customer</button>
            <button type="button" class="btn btn-outline-warning" onclick="setUserType('supplier')">Supplier</button>
            <button type="button" class="btn btn-outline-warning <?= $isAdminLogin ? 'active' : '' ?>" onclick="setUserType('admin')">Admin</button>
        </div>

        <form id="loginForm" action="login.php<?= $isAdminLogin ? '?admin=1' : '' ?>" method="POST">
            <input type="hidden" id="user_type" name="user_type" value="<?= $isAdminLogin ? 'admin' : 'user' ?>">

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required
                    pattern="(?=.*\d).{8,}"
                    title="Password must be at least 8 characters long and contain at least one number.">
            </div>

            <button type="submit" class="btn btn-warning w-100">Sign In</button>
        </form>

        <p class="text-center">Don't have an account?</p>
        <div id="signupOptions">
            <form action="sign_up_choice.php" method="get" class="mb-2">
                <button type="submit" class="btn btn-outline-warning w-100">Sign Up as Customer</button>
            </form>
            <form action="apply_supplier.php" method="get" class="mb-2">
                <button type="submit" class="btn btn-outline-warning w-100">Sign Up as Supplier</button>
            </form>
            <form action="register_admin.php" method="get">
                <button type="submit" class="btn btn-outline-warning w-100">Register as Admin <span class="admin-badge">Restricted</span></button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function setUserType(type) {
            document.getElementById('user_type').value = type;

            // Update active button
            const buttons = document.querySelectorAll('.user-type-selector .btn');
            buttons.forEach(btn => btn.classList.remove('active'));

            if (type === 'user') {
                buttons[0].classList.add('active');
                document.getElementById('loginForm').action = 'login.php';
            } else if (type === 'supplier') {
                buttons[1].classList.add('active');
                document.getElementById('loginForm').action = 'login.php';
            } else if (type === 'admin') {
                buttons[2].classList.add('active');
                document.getElementById('loginForm').action = 'login.php?admin=1';
            }
        }

        document.getElementById("loginForm").addEventListener("submit", function(e) {
            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value;

            let errorMessage = "";

            if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                errorMessage = "Please enter a valid email address.";
            } else if (!password.match(/^(?=.*\d).{8,}$/)) {
                errorMessage = "Password must be at least 8 characters and contain at least one number.";
            }

            if (errorMessage) {
                e.preventDefault(); // Stop form from submitting
                showAlert(errorMessage);
            }
        });

        function showAlert(message) {
            let existingAlert = document.querySelector(".alert");
            if (existingAlert) {
                existingAlert.textContent = message;
                existingAlert.classList.remove("d-none");
            } else {
                const alertBox = document.createElement("div");
                alertBox.className = "alert alert-danger mt-3";
                alertBox.textContent = message;
                const form = document.getElementById("loginForm");
                form.parentNode.insertBefore(alertBox, form);
            }
        }

        // Initialize the user type based on URL parameter
        <?php if ($isAdminLogin): ?>
            setUserType('admin');
        <?php endif; ?>
    </script>
</body>

</html>