<?php
// Fetch card sets from the database
require 'db.php'; // Use this to connect to the database
$cardSets = $pdo->query("SELECT game_id, game_name FROM games")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Insert Card</title>
</head>
<body>
    <?php include "./topmenu.php"; ?>
    <h1>Insert New Card</h1>
        <div class="content">
        <form action="insert_card.php" method="POST">
            <label class = "label" for="card_set">Game:</label>
            <select class = "select" id="card_set" name="card_game" required>
                <option value="">--Select a Game--</option>
                <?php foreach ($cardSets as $set): ?>
                    <option class = "input" value="<?= $set['game_id'] ?>"><?= htmlspecialchars($set['game_name']) ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <label class = "label" for="card_name">Card Name</label>
            <input class = "input" type="text" id="card_name" name="card_name" required><br><br>

            <label class = "label" for="type">Type</label>
            <input class = "input" type="text" id="type" name="type" min="1" required><br><br>

            <label class = "label" for="Form">Form</label>
            <input class = "input" type="text" id="form" name="form" min="1" required><br><br>

            <label class = "label" for="hp">HP</label>
            <input class = "input" type="text" id="hp" name="hp" min="1" required><br><br>

            <label class = "label" for="typings">Typings</label>
            <input class = "input" type="text" id="typings" name="typings" min="1" required><br><br>

            <label class = "label" for="card_text">Card Text</label>
            <input class = "input" type="text" id="card_text" name="card_text" min="1" required><br><br>

            <label class = "label" for="weakness">Weakness</label>
            <input class = "input" type="text" id="weakness" name="weakness" min="1" required><br><br>

            <label class = "label" for="resistance">Resistance</label>
            <input class = "input" type="text" id="resistance" name="resistance" min="1" required><br><br>

            <label class = "label" for="retreat_cost">Retreat Cost</label>
            <input class = "input" type="text" id="retreat_cost" name="retreat_cost" min="1" required><br><br>

            <label class = "label" for="set">Set</label>
            <input class = "input" type="text" id="set" name="set" min="1" required><br><br>

            <label class = "label" for="artists">Artists</label>
            <input class = "input" type="text" id="artists" name="artists" min="1" required><br><br>

            <input class = "button" type="submit" value="Submit">
        </form>
    </div>
</body>
</html>
