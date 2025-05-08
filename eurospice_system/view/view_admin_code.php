<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in as a super admin
if (!isset($_SESSION["admin_id"]) || !isset($_SESSION["is_super_admin"]) || $_SESSION["is_super_admin"] != 1) {
    // Redirect to login page with an error message
    header("Location: login.php?error=unauthorized&admin=1");
    exit;
}

// Initialize database connection
try {
    $db = new Database();
    $conn = $db->connect();
} catch (PDOException $e) {
    die("Database connection error: " . $e->getMessage());
}

// Get the registration code from database
try {
    $stmt = $conn->prepare("SELECT setting_value FROM system_settings WHERE setting_name = 'admin_registration_code'");
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $registrationCode = $stmt->fetchColumn();
    } else {
        // Default code if not found in database (fallback)
        $registrationCode = "EURO123ADMIN";

        // Create the entry in the database
        $insertDefault = $conn->prepare("INSERT INTO system_settings (setting_name, setting_value) VALUES (?, ?)");
        $insertDefault->execute(['admin_registration_code', $registrationCode]);
    }
} catch (PDOException $e) {
    $error = "Error retrieving code: " . $e->getMessage();
    $registrationCode = "Error retrieving code";
}

// Check if user wants to generate a new code
$codeChanged = false;
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["generate_new_code"])) {
        // Generate a new secure random code
        $newCode = bin2hex(random_bytes(8)); // 16 character hexadecimal string

        try {
            // Update the registration code in the database
            $stmt = $conn->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_name = 'admin_registration_code'");
            $stmt->execute([$newCode]);

            // If no row was updated, insert it
            if ($stmt->rowCount() == 0) {
                $insert = $conn->prepare("INSERT INTO system_settings (setting_name, setting_value) VALUES (?, ?)");
                $insert->execute(['admin_registration_code', $newCode]);
            }

            // Update the variable for display
            $registrationCode = $newCode;
            $codeChanged = true;

            // Log this action
            $logAction = $conn->prepare("INSERT INTO admin_activity_logs (admin_id, action, details, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
            $logAction->execute([
                $_SESSION["admin_id"],
                "Generated new admin registration code",
                "Changed registration code to a new value",
                $_SERVER['REMOTE_ADDR'],
                $_SERVER['HTTP_USER_AGENT']
            ]);
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    } elseif (isset($_POST["manage_recovery_keys"])) {
        // Redirect to recovery key management page
        header("Location: manage_recovery_keys.php");
        exit;
    }
}

// Get recent code change logs
try {
    $logs = $conn->prepare("
        SELECT l.created_at, a.username, l.action
        FROM admin_activity_logs l
        JOIN admins a ON l.admin_id = a.id
        WHERE l.action LIKE '%registration code%'
        ORDER BY l.created_at DESC
        LIMIT 5
    ");
    $logs->execute();
    $recentLogs = $logs->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $recentLogs = [];
}

// Get recent recovery key usage
try {
    $recoveryUsage = $conn->prepare("
        SELECT email, created_at, success, ip_address
        FROM code_retrieval_logs
        ORDER BY created_at DESC
        LIMIT 10
    ");
    $recoveryUsage->execute();
    $recentRecoveryUsage = $recoveryUsage->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $recentRecoveryUsage = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Euro Spice | Admin Registration Code</title>
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
            width: 700px;
            background-color: white;
            padding: 40px;
            border-radius: 30px;
            margin-top: 50px;
            margin-bottom: 50px;
            box-shadow: -30px 30px 0px rgba(0, 0, 0, 0.1);
        }

        .page-title {
            color: #333;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .super-admin-badge {
            background-color: #dc3545;
            color: white;
            font-size: 0.8rem;
            padding: 5px 10px;
            border-radius: 10px;
            display: inline-block;
            margin-bottom: 20px;
        }

        .code-container {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            position: relative;
        }

        .code-value {
            font-family: monospace;
            font-size: 1.5rem;
            letter-spacing: 2px;
        }

        .copy-btn {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .security-notice {
            background-color: #fff3cd;
            border-left: 5px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }

        .tab-content {
            padding: 20px;
            border: 1px solid #dee2e6;
            border-top: none;
            border-radius: 0 0 5px 5px;
            background-color: #fff;
        }

        .success-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 5px;
        }

        .success-true {
            background-color: #28a745;
        }

        .success-false {
            background-color: #dc3545;
        }
    </style>
</head>

<body>
    <div class="container-me">
        <h1 class="text-center page-title">Admin Registration Code</h1>
        <div class="text-center">
            <span class="super-admin-badge">Super Admin Access Only</span>
        </div>

        <?php if ($codeChanged): ?>
            <div class="alert alert-success">
                <strong>Success!</strong> A new admin registration code has been generated.
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <strong>Error!</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="code-container text-center">
            <h5>Current Registration Code:</h5>
            <div class="code-value" id="registrationCode"><?php echo htmlspecialchars($registrationCode); ?></div>
            <button class="btn btn-sm btn-outline-secondary copy-btn" onclick="copyCode()">
                <i class="bi bi-clipboard"></i> Copy
            </button>
        </div>

        <div class="security-notice">
            <h5><i class="bi bi-shield-lock"></i> Security Notice</h5>
            <p>This code should only be shared with authorized individuals who need to create admin accounts.
                Always use secure channels when sharing this code.</p>
            <p class="mb-0"><strong>Recovery Option:</strong> Authorized users can retrieve this code using the recovery system if they have a valid recovery key.</p>
        </div>

        <form method="POST" action="" class="mt-4">
            <div class="d-grid gap-2">
                <button type="submit" name="generate_new_code" class="btn btn-danger">Generate New Code</button>
                <button type="submit" name="manage_recovery_keys" class="btn btn-warning">Manage Recovery Keys</button>
                <a href="admin_dashboard.php" class="btn btn-outline-secondary">Back to Dashboard</a>
            </div>
        </form>

        <div class="mt-4">
            <ul class="nav nav-tabs" id="adminTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="code-changes-tab" data-bs-toggle="tab" data-bs-target="#code-changes" type="button" role="tab" aria-controls="code-changes" aria-selected="true">Code Changes</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="recovery-usage-tab" data-bs-toggle="tab" data-bs-target="#recovery-usage" type="button" role="tab" aria-controls="recovery-usage" aria-selected="false">Recovery Usage</button>
                </li>
            </ul>
            <div class="tab-content" id="adminTabsContent">
                <div class="tab-pane fade show active" id="code-changes" role="tabpanel" aria-labelledby="code-changes-tab">
                    <h5>Recent Code Changes</h5>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Admin</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($recentLogs) > 0): ?>
                                <?php foreach ($recentLogs as $log): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars(date('M d, Y H:i', strtotime($log['created_at']))); ?></td>
                                        <td><?php echo htmlspecialchars($log['username']); ?></td>
                                        <td><?php echo htmlspecialchars($log['action']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">No recent activity</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="recovery-usage" role="tabpanel" aria-labelledby="recovery-usage-tab">
                    <h5>Recent Recovery System Usage</h5>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($recentRecoveryUsage) > 0): ?>
                                <?php foreach ($recentRecoveryUsage as $usage): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars(date('M d, Y H:i', strtotime($usage['created_at']))); ?></td>
                                        <td><?php echo htmlspecialchars($usage['email']); ?></td>
                                        <td>
                                            <span class="success-dot <?php echo $usage['success'] ? 'success-true' : 'success-false'; ?>"></span>
                                            <?php echo $usage['success'] ? 'Success' : 'Failed'; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($usage['ip_address']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">No recent recovery attempts</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function copyCode() {
            const codeText = document.getElementById('registrationCode').innerText;
            navigator.clipboard.writeText(codeText).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Copied!',
                    text: 'Registration code copied to clipboard',
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

        // Add confirmation for generating new code
        document.querySelector('button[name="generate_new_code"]').addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Generate New Code?',
                text: 'This will invalidate the current registration code. Existing admins will not be affected.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, generate new code'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.form.submit();
                }
            });
        });
    </script>
</body>

</html>