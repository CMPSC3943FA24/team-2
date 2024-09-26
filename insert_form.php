<?php
// Fetch card sets from the database
require 'db.php'; // Use this to connect to the database
$cardSets = $pdo->query("SELECT game_id, game_name FROM games")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Insert Card</title>
</head>
<body>
    <?php include "./topmenu.php"; ?>
    <h1>Insert New Card</h1>
    <form action="insert_card.php" method="POST">
        <label for="card_set">Game:</label>
        <select id="card_set" name="card_game" required>
            <option value="">--Select a Game--</option>
            <?php foreach ($cardSets as $set): ?>
                <option value="<?= $set['gmae_id'] ?>"><?= htmlspecialchars($set['game_name']) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="card_name">Card Name:</label>
        <input type="text" id="card_name" name="card_name" required><br><br>

        <label for="card_quantity">Card Quantity:</label>
        <input type="number" id="card_quantity" name="card_quantity" min="1" required><br><br>

        <input type="submit" value="Submit">
    </form>
</body>
</html>
