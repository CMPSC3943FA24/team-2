<?php
//define webroot
define('WEB_ROOT', '/home/crowellm/php/app');
// db_mysqli.php

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
?>