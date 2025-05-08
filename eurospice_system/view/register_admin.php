<?php
session_start();
require_once '../config/database.php';

// Check if the user is already logged in as an admin
if (isset($_SESSION["admin_id"])) {
    header("Location: admin_dashboard.php");
    exit;
}

$allowedDomains = ['gmail.com', 'eurospice.ph']; // Customize as needed
$error = "";
$registrationCode = "EURO123ADMIN"; // Change this to your secure admin registration code

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? '');
    $username = trim($_POST["username"] ?? '');
    $password = $_POST["password"] ?? '';
    $confirm = $_POST["confirm_password"] ?? '';
    $adminCode = $_POST["admin_code"] ?? '';

    if (empty($email) || empty($username) || empty($password) || empty($confirm) || empty($adminCode)) {
        $error = "All fields are required.";
    } else {
        $emailParts = explode('@', $email);
        $domain = array_pop($emailParts);

        if (!in_array($domain, $allowedDomains)) {
            $error = "Only emails from these domains are allowed: " . implode(', ', $allowedDomains);
        } elseif ($password !== $confirm) {
            $error = "Passwords do not match.";
        } elseif ($adminCode !== $registrationCode) {
            $error = "Invalid admin registration code.";
        } else {
            try {
                $db = new Database();
                $conn = $db->connect();

                // Check if the email is already registered as an admin
                $stmt = $conn->prepare("SELECT id FROM admins WHERE email = ?");
                $stmt->execute([$email]);

                if ($stmt->rowCount() > 0) {
                    $error = "This email is already registered as an admin.";
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // Create the admins table if it doesn't exist
                    $createTable = $conn->prepare("
                        CREATE TABLE IF NOT EXISTS admins (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            email VARCHAR(255) NOT NULL UNIQUE,
                            username VARCHAR(100) NOT NULL,
                            password VARCHAR(255) NOT NULL,
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                        )
                    ");
                    $createTable->execute();

                    // Insert the new admin
                    $insert = $conn->prepare("INSERT INTO admins (email, username, password) VALUES (?, ?, ?)");
                    $insert->execute([$email, $username, $hashed_password]);

                    // Redirect to login page with registered flag
                    header("Location: login.php?registered=1&admin=1");
                    exit;
                }
            } catch (PDOException $e) {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Euro Spice | Admin Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.prod.website-files.com/66a833f537135b05bc1eaecb/css/maria-bettinas-dynamite-site.webflow.05b59e178.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="icon" type="x-icon" href="../assets/images/eurospice-favicon.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        .space {
            height: 50px;
        }

        .page-title {
            color: #333;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .admin-badge {
            background-color: #343a40;
            color: white;
            font-size: 0.8rem;
            padding: 5px 10px;
            border-radius: 10px;
            display: inline-block;
            margin-bottom: 20px;
        }

        .need-code {
            margin-top: 15px;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <div class="container-me">
        <h1 class="text-center page-title">Admin Registration</h1>
        <div class="text-center">
            <span class="admin-badge">Restricted Access</span>
        </div>

        <!-- Registration form -->
        <form action="" method="POST" id="adminRegistrationForm">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
                <div class="form-text">Must be from: <?= implode(', ', $allowedDomains) ?></div>
            </div>

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required minlength="3">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required
                    pattern="(?=.*\d).{8,}"
                    title="Password must be at least 8 characters long and contain at least one number.">
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>

            <div class="mb-3">
                <label for="admin_code" class="form-label">Admin Registration Code</label>
                <input type="password" class="form-control" id="admin_code" name="admin_code" required>
                <div class="form-text">Enter the secure code provided by system administrator</div>
                <div class="need-code text-center">
                    <a href="retrieve_admin_code.php" class="text-decoration-none">Need a registration code?</a>
                </div>
            </div>

            <button type="submit" class="btn btn-warning w-100">Register Admin Account</button>
        </form>

        <!-- Already have account -->
        <p class="text-center mt-4">Already have an Admin Account?</p>
        <form action="login.php" method="GET">
            <input type="hidden" name="admin" value="1">
            <button type="submit" class="btn btn-outline-warning w-100">Sign In</button>
        </form>

        <!-- Error message -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger mt-3"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
    </div>

    <div class="space"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById("adminRegistrationForm").addEventListener("submit", function(e) {
            const email = document.getElementById("email").value.trim();
            const username = document.getElementById("username").value.trim();
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirm_password").value;
            const adminCode = document.getElementById("admin_code").value;

            const emailParts = email.split("@");
            const domain = emailParts.length === 2 ? emailParts[1] : "";
            const allowedDomains = <?= json_encode($allowedDomains) ?>;

            let error = "";

            if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                error = "Please enter a valid email address.";
            } else if (!allowedDomains.includes(domain)) {
                error = `Only emails from these domains are allowed: ${allowedDomains.join(", ")}`;
            } else if (username.length < 3) {
                error = "Username must be at least 3 characters.";
            } else if (!password.match(/^(?=.*\d).{8,}$/)) {
                error = "Password must be at least 8 characters and contain at least one number.";
            } else if (password !== confirmPassword) {
                error = "Passwords do not match.";
            } else if (adminCode === "") {
                error = "Admin registration code is required.";
            }

            if (error) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: error
                });
            }
        });
    </script>
</body>

</html>