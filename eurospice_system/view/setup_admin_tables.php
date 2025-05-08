<?php
// This script sets up all necessary database tables for the admin system
require_once '../config/database.php';

try {
    $db = new Database();
    $conn = $db->connect();

    // Create admins table
    $conn->exec("
        CREATE TABLE IF NOT EXISTS admins (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            username VARCHAR(100) NOT NULL,
            password VARCHAR(255) NOT NULL,
            is_super_admin TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Create system settings table for storing configuration values
    $conn->exec("
        CREATE TABLE IF NOT EXISTS system_settings (
            setting_name VARCHAR(100) PRIMARY KEY,
            setting_value TEXT NOT NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");

    // Create admin activity logs table
    $conn->exec("
        CREATE TABLE IF NOT EXISTS admin_activity_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            admin_id INT NULL,
            email VARCHAR(255) NULL,
            action VARCHAR(255) NOT NULL,
            details TEXT NULL,
            ip_address VARCHAR(45) NOT NULL,
            user_agent TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Create code retrieval logs table
    $conn->exec("
        CREATE TABLE IF NOT EXISTS code_retrieval_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL,
            ip_address VARCHAR(45) NOT NULL,
            user_agent TEXT NOT NULL,
            success BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Create recovery keys table
    $conn->exec("
        CREATE TABLE IF NOT EXISTS recovery_keys (
            id INT AUTO_INCREMENT PRIMARY KEY,
            key_value VARCHAR(255) NOT NULL,
            description VARCHAR(255) NULL,
            is_active TINYINT(1) DEFAULT 1,
            created_by INT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            expires_at TIMESTAMP NULL DEFAULT NULL
        )
    ");

    // Insert default admin registration code if it doesn't exist
    $checkCode = $conn->prepare("SELECT setting_value FROM system_settings WHERE setting_name = 'admin_registration_code'");
    $checkCode->execute();

    if ($checkCode->rowCount() == 0) {
        $insertCode = $conn->prepare("INSERT INTO system_settings (setting_name, setting_value) VALUES (?, ?)");
        $insertCode->execute(['admin_registration_code', 'EURO123ADMIN']);
    }

    // Insert default recovery key if it doesn't exist
    $checkKey = $conn->prepare("SELECT id FROM recovery_keys WHERE key_value = ?");
    $checkKey->execute(['EURO-RECOVERY-2025']);

    if ($checkKey->rowCount() == 0) {
        $insertKey = $conn->prepare("INSERT INTO recovery_keys (key_value, description, is_active) VALUES (?, ?, ?)");
        $insertKey->execute(['EURO-RECOVERY-2025', 'Default recovery key for admin code retrieval', 1]);
    }

    echo "Database tables created successfully!";
} catch (PDOException $e) {
    die("Database setup error: " . $e->getMessage());
}
