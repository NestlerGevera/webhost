<?php
session_start();
require_once '../config/database.php';

// Security headers
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net; style-src 'self' https://cdn.jsdelivr.net https://cdn.prod.website-files.com; img-src 'self' data:;");

$error = "";
$code = "";
$showCode = false;
$allowedDomains = ['gmail.com', 'eurospice.ph']; // This should match your registration allowed domains

// Initialize database connection
try {
    $db = new Database();
    $conn = $db->connect();
} catch (PDOException $e) {
    $error = "Database connection error. Please try again later.";
}

// Get the registration code from database (similar to admin_registration_code.php)
if (isset($conn)) {
    try {
        $stmt = $conn->prepare("SELECT setting_value FROM system_settings WHERE setting_name = 'admin_registration_code'");
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $systemCode = $stmt->fetchColumn();
        } else {
            // Default code if not found in database (fallback)
            $systemCode = "EURO123ADMIN";
        }
    } catch (PDOException $e) {
        $systemCode = "EURO123ADMIN"; // Fallback in case of error
    }
} else {
    $systemCode = "EURO123ADMIN"; // Fallback if no connection
}

// Get valid recovery keys from database
$validRecoveryKeys = [];
if (isset($conn)) {
    try {
        // Check if recovery_keys table exists
        $tableCheck = $conn->prepare("SHOW TABLES LIKE 'recovery_keys'");
        $tableCheck->execute();

        if ($tableCheck->rowCount() > 0) {
            $keyStmt = $conn->prepare("SELECT key_value FROM recovery_keys WHERE is_active = 1");
            $keyStmt->execute();
            while ($row = $keyStmt->fetch(PDO::FETCH_ASSOC)) {
                $validRecoveryKeys[] = $row['key_value'];
            }
        } else {
            // Fallback if table doesn't exist
            $validRecoveryKeys = ["EURO-RECOVERY-2025"];
        }
    } catch (PDOException $e) {
        // Fallback in case of error
        $validRecoveryKeys = ["EURO-RECOVERY-2025"];
    }
} else {
    // Fallback if no connection
    $validRecoveryKeys = ["EURO-RECOVERY-2025"];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? '');
    $recoveryKey = trim($_POST["recovery_key"] ?? '');

    if (empty($email) || empty($recoveryKey)) {
        $error = "Both email and recovery key are required.";
    } else {
        $emailParts = explode('@', $email);
        $domain = array_pop($emailParts);

        if (!in_array($domain, $allowedDomains)) {
            $error = "Only authorized email domains are allowed.";
        } else {
            // Rate limiting - check if there have been too many attempts from this IP
            $ipAddress = $_SERVER['REMOTE_ADDR'];
            $rateLimitHours = 2; // Limit window in hours
            $maxAttempts = 5; // Maximum attempts in the window

            try {
                if (isset($conn)) {
                    // Create log table if it doesn't exist
                    $logStmt = $conn->prepare("
                        CREATE TABLE IF NOT EXISTS code_retrieval_logs (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            email VARCHAR(255) NOT NULL,
                            ip_address VARCHAR(45) NOT NULL,
                            user_agent TEXT NOT NULL,
                            success BOOLEAN DEFAULT FALSE,
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                        )
                    ");
                    $logStmt->execute();

                    // Check for rate limiting
                    $rateCheck = $conn->prepare("
                        SELECT COUNT(*) FROM code_retrieval_logs 
                        WHERE ip_address = ? 
                        AND created_at > DATE_SUB(NOW(), INTERVAL ? HOUR)
                    ");
                    $rateCheck->execute([$ipAddress, $rateLimitHours]);
                    $attemptCount = $rateCheck->fetchColumn();

                    if ($attemptCount >= $maxAttempts) {
                        $error = "Too many attempts. Please try again later.";
                    } else {
                        // Verify recovery key
                        if (in_array($recoveryKey, $validRecoveryKeys)) {
                            // Success - show the code
                            $code = $systemCode;
                            $showCode = true;

                            // Check if user exists in admins
                            $userCheck = $conn->prepare("SELECT COUNT(*) FROM admins WHERE email = ?");
                            $userCheck->execute([$email]);
                            $userExists = $userCheck->fetchColumn() > 0;

                            // Log success
                            $insertLog = $conn->prepare("
                                INSERT INTO code_retrieval_logs (email, ip_address, user_agent, success) 
                                VALUES (?, ?, ?, ?)
                            ");
                            $insertLog->execute([
                                $email,
                                $ipAddress,
                                $_SERVER['HTTP_USER_AGENT'],
                                true
                            ]);

                            // If user exists in admin table, notify superadmin via email (example)
                            if ($userExists) {
                                // This would be where you'd implement email notification
                                // For security reasons, alert super admins of successful code retrievals
                            }
                        } else {
                            $error = "Invalid recovery key. Please contact a system administrator.";

                            // Log failed attempt
                            $insertLog = $conn->prepare("
                                INSERT INTO code_retrieval_logs (email, ip_address, user_agent, success) 
                                VALUES (?, ?, ?, ?)
                            ");
                            $insertLog->execute([
                                $email,
                                $ipAddress,
                                $_SERVER['HTTP_USER_AGENT'],
                                false
                            ]);
                        }
                    }
                } else {
                    $error = "System error. Database connection failed.";
                }
            } catch (PDOException $e) {
                $error = "System error. Please try again later.";
            }
        }
    }
}

// Generate CSRF token for form protection
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Euro Spice | Retrieve Admin Code</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.prod.website-files.com/66a833f537135b05bc1eaecb/css/maria-bettinas-dynamite-site.webflow.05b59e178.css" rel="stylesheet" type="text/css">
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

        .code-display {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
            position: relative;
        }

        .code-text {
            font-family: monospace;
            font-size: 1.2rem;
            letter-spacing: 1px;
            color: #343a40;
        }

        .security-alert {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 10px 15px;
            margin: 15px 0;
            font-size: 0.9rem;
        }

        .countdown {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container-me">
        <h1 class="text-center page-title">Retrieve Admin Code</h1>
        <div class="text-center">
            <span class="admin-badge">Secure Verification</span>
        </div>

        <?php if ($showCode): ?>
            <div class="alert alert-success">
                <strong><i class="bi bi-check-circle-fill"></i> Success!</strong> Your admin registration code is below.
            </div>

            <div class="code-display">
                <p class="mb-1">Admin Registration Code:</p>
                <div class="code-text" id="adminCode"><?php echo htmlspecialchars($code); ?></div>
                <button class="btn btn-sm btn-outline-secondary mt-2" onclick="copyCode()">
                    <i class="bi bi-clipboard"></i> Copy Code
                </button>
                <div class="countdown mt-2" id="codeTimer">
                    This code will be hidden in <span id="timer">300</span> seconds
                </div>
            </div>

            <div class="security-alert">
                <strong><i class="bi bi-shield-exclamation"></i> Important:</strong> This code should be used immediately and not shared with unauthorized persons.
                <p class="mb-0 mt-1">Your access to this code has been logged for security purposes.</p>
            </div>

            <div class="d-grid gap-2 mt-4">
                <a href="admin_register.php" class="btn btn-warning">
                    <i class="bi bi-person-plus"></i> Go to Registration
                </a>
                <button class="btn btn-outline-secondary" onclick="window.history.back()">
                    <i class="bi bi-arrow-left"></i> Go Back
                </button>
            </div>
        <?php else: ?>
            <p class="text-center">Enter your authorized email and the admin recovery key to retrieve the admin registration code.</p>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" id="retrieveCodeForm">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                <div class="mb-3">
                    <label for="email" class="form-label">Your Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-text">Must be an authorized email domain</div>
                </div>

                <div class="mb-3">
                    <label for="recovery_key" class="form-label">Recovery Key</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-key"></i></span>
                        <input type="password" class="form-control" id="recovery_key" name="recovery_key" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <div class="form-text">Enter the recovery key provided by your system administrator</div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-unlock"></i> Retrieve Admin Code
                    </button>
                    <a href="register_admin.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Registration
                    </a>
                </div>
            </form>

            <div class="security-alert mt-4">
                <strong><i class="bi bi-shield-lock"></i> Security Notice:</strong>
                <p class="mb-0">All code retrieval attempts are logged and monitored for security purposes.</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="space"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        <?php if ($showCode): ?>
            // Timer to hide the code after 5 minutes (300 seconds)
            let timeLeft = 300;
            const timerElement = document.getElementById('timer');
            const timerInterval = setInterval(function() {
                timeLeft--;
                timerElement.textContent = timeLeft;

                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    document.getElementById('adminCode').textContent = "********";
                    document.getElementById('codeTimer').textContent = "Code hidden for security";

                    Swal.fire({
                        icon: 'info',
                        title: 'Code Hidden',
                        text: 'The admin code has been hidden for security purposes.',
                        confirmButtonText: 'Understand'
                    });
                }
            }, 1000);

            function copyCode() {
                const codeText = document.getElementById('adminCode').innerText;
                navigator.clipboard.writeText(codeText).then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Copied!',
                        text: 'Admin code copied to clipboard',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }).catch(err => {
                    console.error('Could not copy text: ', err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Failed to copy code'
                    });
                });
            }
        <?php else: ?>
            // Toggle password visibility
            document.getElementById('togglePassword').addEventListener('click', function() {
                const recoveryKeyInput = document.getElementById('recovery_key');
                const type = recoveryKeyInput.getAttribute('type') === 'password' ? 'text' : 'password';
                recoveryKeyInput.setAttribute('type', type);

                // Toggle icon
                const icon = this.querySelector('i');
                icon.classList.toggle('bi-eye');
                icon.classList.toggle('bi-eye-slash');
            });

            // Form validation
            document.getElementById('retrieveCodeForm').addEventListener('submit', function(e) {
                const email = document.getElementById('email').value.trim();
                const recoveryKey = document.getElementById('recovery_key').value.trim();

                const emailParts = email.split('@');
                if (emailParts.length !== 2) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Email',
                        text: 'Please enter a valid email address',
                    });
                    return;
                }

                const domain = emailParts[1];
                const allowedDomains = <?php echo json_encode($allowedDomains); ?>;

                if (!allowedDomains.includes(domain)) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Domain Not Allowed',
                        text: 'Only emails from authorized domains are allowed: ' + allowedDomains.join(', '),
                    });
                    return;
                }

                if (recoveryKey.length < 8) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Key',
                        text: 'Please enter a valid recovery key',
                    });
                    return;
                }
            });
        <?php endif; ?>
    </script>
</body>

</html>