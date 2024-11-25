<?php
// Include your database connection file
require "config.php";

// Start session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // User is not logged in, exit
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id']; // Get user ID from the session

// Get the card_id and new_quantity from the AJAX request
$card_id = isset($_POST['card_id']) ? (int)$_POST['card_id'] : 0;
$new_quantity = isset($_POST['new_quantity']) ? (int)$_POST['new_quantity'] : 0;

// Ensure valid card_id and new_quantity
if ($card_id <= 0 || $new_quantity < 0) {
    die("Invalid data.");
}

// Query to update the number_owned in the database
$updateQuery = "UPDATE cards SET number_owned = ? WHERE card_id = ? AND owner = ?";

// Prepare and execute the update query
$stmt = $conn->prepare($updateQuery);
if (!$stmt) {
    die("Prepare failed for update: " . $conn->error);
}

$stmt->bind_param("iii", $new_quantity, $card_id, $user_id);

if (!$stmt->execute()) {
    die("Execution failed for update: " . $stmt->error);
}

echo "Card quantity updated successfully.";

$stmt->close();
$conn->close();
?>
