<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start a new session if one is not already started
}

// Include the database connection
require '../app/db_mysqli.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect data from the form
    $card_game = $_POST['card_game']; // This is the game_id
    $card_name = $_POST['card_name'];
    $form = $_POST['form'];
    $hp = $_POST['hp'];
    $typings = $_POST['typings'];
    $attack1 = $_POST['attack1'];
    $attack1_desc = $_POST['attack1_desc'];
    $attack1_cost = $_POST['attack1_cost'];
    $attack1_type1 = $_POST['attack1_type1'];
    $attack1_type2 = $_POST['attack1_type2'];
    $attack1_power = $_POST['attack1_power'];
    $weakness = $_POST['weakness'];
    $retreat_cost_type = $_POST['retreat_cost_type'];
    $retreat_cost = $_POST['retreat_cost'];
    $artist = $_POST['artists'];
    $owner = $_POST['owner'];

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Insert into the cards table
        $stmt = $conn->prepare("INSERT INTO cards (set_id, name, owner) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $card_game, $card_name, $owner);
        $stmt->execute();

        // Get the last inserted card_id
        $card_id = $conn->insert_id;

        // Insert into the pokemon_criteria table
        $stmt = $conn->prepare("INSERT INTO pokemon_criteria (card_id, form, hp, typings, attack1, attack1_desc, attack1_cost, attack1_type1, attack1_type2, attack1_power, weakness, retreat_cost_type, retreat_cost, artist) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isisssssssisss", $card_id, $form, $hp, $typings, $attack1, $attack1_desc, $attack1_cost, $attack1_type1, $attack1_type2, $attack1_power, $weakness, $retreat_cost_type, $retreat_cost, $artist);
        
        // Execute the second query
        $stmt->execute();

        // Commit the transaction
        $conn->commit();

        echo "New card inserted successfully!";
    } catch (Exception $e) {
        // Rollback the transaction if something failed
        $conn->rollback();
        echo "Error inserting card: " . $e->getMessage();
    }
}
?>

