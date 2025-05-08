<?php
session_start();
require_once '../config/database.php';

$allowedDomains = ['gmail.com', 'eurospice.ph']; // Change as needed
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"] ?? '');
    $username = trim($_POST["username"] ?? '');
    $password = $_POST["password"] ?? '';
    $confirm = $_POST["confirm_password"] ?? '';

    $emailParts = explode('@', $email);
    $domain = array_pop($emailParts);

    if (!in_array($domain, $allowedDomains)) {
        $error = "Only emails from these domains are allowed: " . implode(', ', $allowedDomains);
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        try {
            $db = new Database();
            $conn = $db->connect();

            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->rowCount() > 0) {
                $error = "Email is already registered.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Fixed: Insert with proper column order based on table structure
                $insert = $conn->prepare("INSERT INTO users (email, username, password) VALUES (?, ?, ?)");
                $insert->execute([$email, $username, $hashed_password]);

                // Redirect to login page with registered=1 flag
                header("Location: login.php?registered=1");
                exit;
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Euro Spice | Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="x-icon" href="../assets/images/eurospice-favicon.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            display: grid;
            place-items: center;
            height: 100vh;
            background-color: #F15B31;
        }

        .container-me {
            width: 400px;
            background-color: white;
            padding: 40px;
            border-radius: 100px;
            box-shadow: -30px 30px 0px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="container-me">
        <h1 class="text-center">Sign Up</h1>
        <!-- Fixed: Changed action to sign_up.php -->
        <form action="sign_up.php" method="POST" id="signupForm">
            <div class="mb-3">
                <label for="email" class="form-label">Email (<?= implode(', ', $allowedDomains) ?>)</label>
                <input type="email" class="form-control" name="email" required>
            </div>

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" name="username" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" name="password" required
                    pattern="(?=.*\d).{8,}" title="At least 8 characters and one number.">
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" name="confirm_password" required>
            </div>

            <button type="submit" class="btn btn-warning w-100">Sign Up</button>
        </form>

        <p class="text-center mt-3">Already have an account?</p>
        <form action="login.php" method="get">
            <button type="submit" class="btn btn-warning w-100">Sign In</button>
        </form>
    </div>

    <script>
        <?php if (!empty($error)): ?>
            Swal.fire({
                icon: 'error',
                title: 'Oops!',
                text: <?= json_encode($error) ?>
            });
        <?php elseif (!empty($success)): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: <?= json_encode($success) ?>,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = 'dashboard.php';
            });
        <?php endif; ?>

        const allowedDomains = <?= json_encode($allowedDomains) ?>;

        document.getElementById("signupForm").addEventListener("submit", function(e) {
            const email = document.querySelector("input[name='email']").value.trim();
            const username = document.querySelector("input[name='username']").value.trim();
            const password = document.querySelector("input[name='password']").value;
            const confirmPassword = document.querySelector("input[name='confirm_password']").value;

            const emailParts = email.split("@");
            const domain = emailParts.length === 2 ? emailParts[1] : "";

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