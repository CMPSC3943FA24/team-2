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

    // Update the card quantity in the database
    $updateQuery = "UPDATE cards SET number_owned = ? WHERE card_id = ? AND owner = ?";
    $cardId = 52465;
    $newQuantity = 4;
    // Prepare the query
    if ($stmt = $conn->prepare($updateQuery)) {
        $stmt->bind_param("iii", $newQuantity, $cardId, $user_id);

        // Execute the query and check if successful
        if ($stmt->execute()) {
            echo "Quantity updated successfully";  // Return success message
        } else {
            echo "Error updating quantity";  // Return error message
        }

        $stmt->close();
    } else {
        echo "Failed to prepare query";  // Handle query preparation failure
    }
} else {
    echo "Missing data";  // Handle missing POST data
}
?>
