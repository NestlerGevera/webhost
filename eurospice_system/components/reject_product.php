<?php
require '../config/database.php'; // connect to DB

$id = $_GET['id'];
// Move to rejected
$moveQuery = "INSERT INTO rejected_orders SELECT * FROM pending_orders WHERE id = ?";
$deleteQuery = "DELETE FROM pending_orders WHERE id = ?";

$stmtMove = $conn->prepare($moveQuery);
$stmtMove->bind_param("i", $id);
$stmtMove->execute();

$stmtDelete = $conn->prepare($deleteQuery);
$stmtDelete->bind_param("i", $id);
$stmtDelete->execute();

header("Location: finance.php");
exit;
