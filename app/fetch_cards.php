<?php
require_once 'config.php';
// Fetch cards based on the deck_id
if (isset($_GET['deck_id']) && is_numeric($_GET['deck_id'])) {
    $deck_id = (int)$_GET['deck_id'];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT c.card_id, c.card_name FROM cards c
                            JOIN deck_cards dc ON c.card_id = dc.card_id
                            WHERE dc.deck_id = ?");
    $stmt->bind_param("i", $deck_id);
    $stmt->execute();
    $cards_result = $stmt->get_result();

    // Return the list of cards
    if ($cards_result->num_rows > 0) {
        echo "<ul>";
        while ($card = $cards_result->fetch_assoc()) {
            echo "<li>{$card['card_name']}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No cards found for this deck.</p>";
    }
} else {
    echo "<p>Invalid deck selected.</p>";
}
?>