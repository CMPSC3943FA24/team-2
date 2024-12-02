<?php
require_once 'config.php';

// Start session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php include "../templates/navbar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deck Builder</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
<div class="container">
    <h1 class="title has-text-current">Decks</h1>
    <a href="edit_decks.php" class="button is-primary">Edit Decks</a>
        <div class="box">
            <h2 class="subtitle">View Deck</h2>
            <div class="select">
                <select name="deck_id" id="deck_id" required>
                    <option value="">Select a Deck</option>
                    <?php
                    // Fetch all decks from the database
                    $result = $conn->query("SELECT deck_id, deck_name FROM decks");
                    while ($deck = $result->fetch_assoc()) {
                        echo "<option value=\"{$deck['deck_id']}\">{$deck['deck_name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div id="card_list">
                <!-- The card list will be dynamically updated here -->
            </div>
        </div>

        <script>
        // JavaScript to handle AJAX request
        document.getElementById('deck_id').addEventListener('change', function() {
            var deck_id = this.value;

            if (deck_id) {
                // Send AJAX request to fetch cards based on the selected deck
                var xhr = new XMLHttpRequest();
                xhr.open('GET', 'fetch_cards.php?deck_id=' + deck_id, true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        // Update the card list with the response
                        document.getElementById('card_list').innerHTML = xhr.responseText;
                    }
                };
                xhr.send();
            } else {
                document.getElementById('card_list').innerHTML = ''; // Clear card list if no deck is selected
            }
        });
        </script>


    </div>
</div>
</body>
</html>
