<?php
require_once 'config.php';

// Start session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // User is not logged in, redirect to the login page
    header('Location: login.php');
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create_deck') {
            $deck_name = $conn->real_escape_string($_POST['deck_name']);
            $description = $conn->real_escape_string($_POST['description']);
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            $conn->query("INSERT INTO decks (deck_name, description, is_active) VALUES ('$deck_name', '$description', $is_active)");
        } elseif ($_POST['action'] === 'add_cards') {
            $deck_id = (int)$_POST['deck_id'];
            $card_ids = $_POST['card_ids'] ?? [];

            foreach ($card_ids as $card_id) {
                $card_id = (int)$card_id;
                $conn->query("INSERT IGNORE INTO deck_cards (deck_id, card_id) VALUES ($deck_id, $card_id)");
            }
        }
    }
}

// Handle card removal
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action'], $_GET['deck_id'], $_GET['card_id']) && $_GET['action'] === 'remove_card') {
    $deck_id = (int)$_GET['deck_id'];
    $card_id = (int)$_GET['card_id'];

    $conn->query("DELETE FROM deck_cards WHERE deck_id = $deck_id AND card_id = $card_id");
    header("Location: ?view_deck=$deck_id");
    exit;
}
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
    <h1 class="title">Deck Builder</h1>

    <!-- Deck Creation Form -->
    <div class="box">
        <h2 class="subtitle">Create New Deck</h2>
        <form method="POST">
            <input type="hidden" name="action" value="create_deck">
            <div class="field">
                <label class="label">Deck Name</label>
                <div class="control">
                    <input class="input" type="text" name="deck_name" required>
                </div>
            </div>
            <div class="field">
                <label class="label">Description</label>
                <div class="control">
                    <textarea class="textarea" name="description"></textarea>
                </div>
            </div>
            <div class="field">
                <label class="checkbox">
                    <input type="checkbox" name="is_active"> Active
                </label>
            </div>
            <div class="control">
                <button class="button is-primary" type="submit">Create Deck</button>
            </div>
        </form>
    </div>

    <!-- Add Cards to Deck -->
    <div class="box">
        <h2 class="subtitle">Add Cards to Deck</h2>
        <form method="POST">
            <input type="hidden" name="action" value="add_cards">
            <div class="field">
                <label class="label">Select Deck</label>
                <div class="select">
                    <select name="deck_id" required>
                        <?php
                        $result = $conn->query("SELECT deck_id, deck_name FROM decks");
                        while ($deck = $result->fetch_assoc()) {
                            echo "<option value=\"{$deck['deck_id']}\">{$deck['deck_name']}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="field">
                <label class="label">Select Cards</label>
                <div class="control">
                    <?php
                    $stmt = $conn->prepare("SELECT card_id, name FROM cards WHERE owner = ?");
                    $stmt->bind_param("i", $_SESSION['user_id']); // Bind user_id as an integer
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($card = $result->fetch_assoc()) {
                        echo "<label class='checkbox'><input type='checkbox' name='card_ids[]' value='{$card['card_id']}'> {$card['name']}</label><br>";
                    }
                    ?>
                </div>
            </div>
            <div class="control">
                <button class="button is-primary" type="submit">Add Cards</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
