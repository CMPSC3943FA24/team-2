<?php
// Database connection details
$host = 'localhost';
$user = 'dev';
$pass = 'dsVZ"^78/7S';
$db = 'cardstock_dev_0';

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
?>