<?php

// Include database connection class
require_once '../config/database.php';

// Set header for JSON response
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Initialize database connection
        $database = new Database();
        $conn = $database->connect();

        // Get form data
        $firstName = $_POST['firstName'] ?? '';
        $lastName = $_POST['lastName'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';

        // Insert data into the database
        $stmt = $conn->prepare("INSERT INTO careers (firstname, lastname, email, phone) VALUES (:firstname, :lastname, :email, :phone)");

        // Bind parameters
        $stmt->bindParam(':firstname', $firstName);
        $stmt->bindParam(':lastname', $lastName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);

        // Execute the statement
        $stmt->execute();

        // Return success message
        echo json_encode([
            "success" => true,
            "message" => "Application submitted successfully"
        ]);
    } catch (PDOException $e) {
        // Return error message
        echo json_encode([
            "success" => false,
            "message" => "Database error: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method"
    ]);
}
