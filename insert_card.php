<?php
session_start();

// Include the database connection
require 'db.php';

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

    // Start a transaction
    $pdo->beginTransaction();

    try {
        // Insert into the cards table
        $stmt = $pdo->prepare("INSERT INTO cards (set_id, name) VALUES (:set_id, :name)");
        $stmt->bindParam(':set_id', $card_game, PDO::PARAM_INT);
        $stmt->bindParam(':name', $card_name);
        $stmt->execute();

        // Get the last inserted card_id
        $card_id = $pdo->lastInsertId();

        // Insert into the pokemon_criteria table
        $stmt = $pdo->prepare("INSERT INTO pokemon_criteria (card_id, form, hp, typings, attack1, attack1_desc, attack1_cost, attack1_type1, attack1_type2, attack1_power, weakness, retreat_cost_type, retreat_cost, artist) 
                                VALUES (:card_id, :form, :hp, :typings, :attack1, :attack1_desc, :attack1_cost, :attack1_type1, :attack1_type2, :attack1_power, :weakness, :retreat_cost_type, :retreat_cost, :artist)");
        $stmt->bindParam(':card_id', $card_id, PDO::PARAM_INT);
        $stmt->bindParam(':form', $form);
        $stmt->bindParam(':hp', $hp, PDO::PARAM_INT);
        $stmt->bindParam(':typings', $typings);
        $stmt->bindParam(':attack1', $attack1);
        $stmt->bindParam(':attack1_desc', $attack1_desc);
        $stmt->bindParam(':attack1_cost', $attack1_cost);
        $stmt->bindParam(':attack1_type1', $attack1_type1);
        $stmt->bindParam(':attack1_type2', $attack1_type2);
        $stmt->bindParam(':attack1_power', $attack1_power, PDO::PARAM_INT);
        $stmt->bindParam(':weakness', $weakness);
        $stmt->bindParam(':retreat_cost_type', $retreat_cost_type);
        $stmt->bindParam(':retreat_cost', $retreat_cost, PDO::PARAM_INT);
        $stmt->bindParam(':artist', $artist);

        // Execute the second query
        $stmt->execute();

        // Commit the transaction
        $pdo->commit();

        echo "New card inserted successfully!";
    } catch (Exception $e) {
        // Rollback the transaction if something failed
        $pdo->rollBack();
        echo "Error inserting card: " . $e->getMessage();
    }
}
?>
