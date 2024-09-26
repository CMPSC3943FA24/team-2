<?php
//session_start();

// Include the database connection
require 'db.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $card_set = $_POST['card_set'];
    $card_name = $_POST['card_name'];
    $card_quantity = $_POST['card_quantity'];

    // Prepare SQL and bind parameters
    $stmt = $pdo->prepare("INSERT INTO cards (card_set, card_name, card_quantity) VALUES (:card_set, :card_name, :card_quantity)");

    $stmt->bindParam(':card_set', $card_set, PDO::PARAM_INT);
    $stmt->bindParam(':card_name', $card_name);
    $stmt->bindParam(':card_quantity', $card_quantity, PDO::PARAM_INT);

    // Execute the query
    if ($stmt->execute()) {
        echo "New card inserted successfully!";
    } else {
        echo "Error inserting card.";
    }
}
?>
