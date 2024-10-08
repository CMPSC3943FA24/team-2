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

        $query = $pdo->prepare("SELECT * FROM cards WHERE card_id = :card_id");
        $query->execute(['card_id' => $card_id]); // Fixed variable name
        $card = $query->fetch(PDO::FETCH_ASSOC);

        if ($card) {
            // Store card information in the session
            $_SESSION['card'] = $card;

            // Get criteria based on set_id
            if ($card['set_id'] == 1) {
                $query = $pdo->prepare("SELECT * FROM magic_criteria WHERE card_id = :card_id");
                $query->execute(['card_id' => $card_id]); // Fixed variable name
                $_SESSION['card_criteria'] = $query->fetch(PDO::FETCH_ASSOC);
            } elseif ($card['set_id'] == 2) {
                $query = $pdo->prepare("SELECT * FROM pokemon_criteria WHERE card_id = :card_id");
                $query->execute(['card_id' => $card_id]); // Fixed variable name
                $_SESSION['card_criteria'] = $query->fetch(PDO::FETCH_ASSOC);
            } elseif ($card['set_id'] == 3) {
                $query = $pdo->prepare("SELECT * FROM yugioh_criteria WHERE card_id = :card_id");
                $query->execute(['card_id' => $card_id]); // Fixed variable name
                $_SESSION['card_criteria'] = $query->fetch(PDO::FETCH_ASSOC);
            } else {
                throw new Exception("Unable to read set_id.");
            }

            // Redirect back to the Print Card page
            header('Location: ../app/print_card.php'); // Update this to the correct path
            exit();
        } else {
            throw new Exception("Card not found for ID: $card_id.");
        }
    }
} catch (Exception $e) {
    // Display error message
    echo '<div style="color: red;">' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>
