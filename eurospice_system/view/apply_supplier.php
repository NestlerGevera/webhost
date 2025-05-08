<?php
session_start();
require_once '../config/database.php';

$allowedDomains = ['gmail.com', 'eurospice.ph'];
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? '');
    $username = trim($_POST["username"] ?? '');
    $password = $_POST["password"] ?? '';
    $confirm = $_POST["confirm_password"] ?? '';
    $brand = trim($_POST["brand"] ?? '');

    if (empty($email) || empty($username) || empty($password) || empty($confirm) || empty($brand)) {
        $error = "All fields are required.";
    } else {
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

                $stmt = $conn->prepare("SELECT id FROM suppliers WHERE email = ?");
                $stmt->execute([$email]);

                if ($stmt->rowCount() > 0) {
                    $error = "This email is already registered.";
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $insert = $conn->prepare("INSERT INTO suppliers (email, username, password, brand_name) VALUES (?, ?, ?, ?)");
                    $insert->execute([$email, $username, $hashed_password, $brand]);

                    // Keep this original behavior - redirect to login page with registered flag
                    header("Location: ../view/login.php?registered=1");
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
    <title>Euro Spice | Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link href="https://cdn.prod.website-files.com/66a833f537135b05bc1eaecb/css/maria-bettinas-dynamite-site.webflow.05b59e178.css" rel="stylesheet" type="text/css">
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

        .space {
            height: 50px;
        }
    </style>
    <script>
        function validatePasswords() {
            const password = document.getElementById("password").value;
            const confirm = document.getElementById("confirm_password").value;

            if (password !== confirm) {
                alert("Passwords do not match.");
                return false;
            }

            return true;
        }
    </script>
</head>

<body>
    <div class="container-me">
        <h1 class="text-center mb-4">Be our Supplier!</h1>

        <!-- Registration form -->
        <form action="login.php" method="POST" onsubmit="return validatePasswords()">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <!-- <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div> -->
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required
                    pattern="(?=.*\d).{8,}" title="Password must be at least 8 characters long and contain at least one number.">
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="mb-3">
                <label for="brand" class="form-label">Brand Name</label>
                <input type="text" class="form-control" id="brand" name="brand" required>
            </div>

            <button type="submit" class="btn btn-warning w-100">Sign Up</button>
        </form>

        <!-- Already have account -->
        <p class="text-center mt-4">Already have an Account?</p>
        <form action="../view/login.php" method="GET">
            <button type="submit" class="btn btn-outline-warning w-100">Sign In</button>
        </form>

        <!-- Error message -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger mt-3"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
    </div>

    <div class="space"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>