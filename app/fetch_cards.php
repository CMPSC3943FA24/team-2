<?php
require_once 'config.php';
if (isset($_GET['deck_id']) && is_numeric($_GET['deck_id'])) {
    $deck_id = (int)$_GET['deck_id'];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT c.card_id, c.name FROM cards c
                            JOIN deck_cards dc ON c.card_id = dc.card_id
                            WHERE dc.deck_id = ?");
    
    if ($stmt === false) {
        die('MySQL prepare error: ' . $conn->error); // Check if statement preparation failed
    }

    $stmt->bind_param("i", $deck_id);

    // Check for bind_param errors
    if (!$stmt->execute()) {
        die('Execute error: ' . $stmt->error);
    }

    $cards_result = $stmt->get_result();

    // Return the list of cards
    if ($cards_result->num_rows > 0) {
        echo "<ul>";
        while ($card = $cards_result->fetch_assoc()) {
            echo "<li>{$card['name']}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No cards found for this deck.</p>";
    }
} else {
    echo "<p>Invalid deck selected.</p>";
}

?>