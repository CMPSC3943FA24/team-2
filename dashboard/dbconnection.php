<?php

# Define DB Variables
$servername = "localhost";
$username = "dev";
$password = 'dsVZ"^78/7S';
$dbname = "cardstock_dev_0";
global $conn;

# Make DB Connection
$conn = new mysqli($servername, $username, $password, $dbname);

# Check if the connection with DB was successful
if ($conn->connect_error) {
    die("Connection Failed: ") . $conn->connect_error;
    echo "\n";
}
else {
    return $conn;
}
?>
