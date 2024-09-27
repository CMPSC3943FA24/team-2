<?php
session_start();

// Include the database connection
require 'db.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect data from the form
    $card_id = $_POST['card_id']; // This is the card_id

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
            die("set_id could not be read");
        }

        // Redirect back to the Print Card page
        header('Location: ../app/print_card.php'); // Update this to the correct path
        exit();
    } else {
        die("Card not found.");
    }
}
?>
