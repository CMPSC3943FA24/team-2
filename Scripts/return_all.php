<?php
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the database connection
require '../db.php';

try {
    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Prepare the query
        $query = $pdo->prepare("SELECT * FROM cards");
        // Bind the parameter to prevent SQL injection
        $query->execute(['card_id' => $card]);
        $card = $query->fetch(PDO::FETCH_ASSOC);

        if (!$card) {
            throw new Exception('Card not found.');
        }

        // Redirect back to the Print Card page
        header('Location: ../app/print_card.php'); // Update this to the correct path
        exit();
    }
} catch (Exception $e) {
    // Display the error message
    echo 'Error: ' . $e->getMessage();
}
?>
