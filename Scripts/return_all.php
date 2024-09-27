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
        // Collect data from the form
        $card_id = $_POST['card_id']; // This is the card_id

        // Validate card_id
        if (empty($card_id) || !is_numeric($card_id)) {
            throw new Exception('Invalid Card ID. Please enter a valid number.');
        }

        $query = $pdo->prepare("SELECT * FROM cards");
        $query->execute(['card_id' => $card_id]); // Fixed variable name
        $card = $query->fetch(PDO::FETCH_ASSOC);

        // Redirect back to the Print Card page
        header('Location: ../app/print_card.php'); // Update this to the correct path
        exit();
    }
?>
