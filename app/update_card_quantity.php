<?php
// Include your database connection file
require "config.php"; // adjust the path to your database connection file
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

$user_id = $_SESSION['user_id']; // Get user ID from the session

// Check if required data is present
if (isset($_POST['card_id']) && isset($_POST['new_quantity'])) {
    $cardId = $_POST['card_id'];
    $newQuantity = $_POST['new_quantity'];
    
    // Update the card quantity in the database
    $updateQuery = "UPDATE cards SET number_owned = ? WHERE card_id = ? AND owner = ?";
    
    // Prepare the query
    if ($stmt = $conn->prepare($updateQuery)) {
        $stmt->bind_param("iii", $newQuantity, $cardId, $user_id);

        // Execute the query and check if successful
        if ($stmt->execute()) {
            // Set session variable for success
            $_SESSION['update_status'] = 'success'; 
        } else {
            // Set session variable for failure
            $_SESSION['update_status'] = 'failure';
        }

        $stmt->close();
    } else {
        // Set session variable for failure in case of query preparation error
        $_SESSION['update_status'] = 'failure';
    }
} else {
    // Set session variable for failure if data is missing
    $_SESSION['update_status'] = 'failure';
}

// Redirect back to the previous page
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
?>
