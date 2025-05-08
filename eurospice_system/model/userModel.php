<?php
require_once(__DIR__ . '/../config/database.php');

class User {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
        // Set error mode for better debugging
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function login($username, $password) {
        try {
            // Lowercase the username to match case-insensitive query
            $username = strtolower($username);

            $query = "SELECT users.*, user_roles.role_name FROM users 
                      LEFT JOIN user_roles ON users.user_role = user_roles.role_id 
                      WHERE LOWER(users.username) = :username LIMIT 1";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":username", $username);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                error_log("🚫 No user found for username: $username");
                return false;
            }

            // Log the hash and attempt
            error_log("🔍 Checking password for username: $username");
            error_log("🔐 Entered password: $password");
            error_log("🔐 Stored hash: " . $user['password']);

            if (password_verify($password, $user['password'])) {
                // Log match
                error_log("✅ Password match for user: $username");

                // Update last_login timestamp
                $update = "UPDATE users SET last_login = NOW() WHERE user_id = :id";
                $upStmt = $this->conn->prepare($update);
                $upStmt->bindParam(":id", $user['user_id']);
                $upStmt->execute();

                return $user;
            } else {
                error_log("❌ Password mismatch for user: $username");
                return false;
            }
        } catch (PDOException $e) {
            error_log("❗ Login error: " . $e->getMessage());
            return false;
        }
    }
}
?>
