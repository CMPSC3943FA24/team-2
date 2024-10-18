<?php
// Include the database connection file
require '/app/db_mysqli.php'; // Use this to connect to the database
session_start();

//Load config file
if (!file_exists('config.php')) {
    die('Configuration file not found.');
}
require 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // User is not logged in, redirect to the login page
    header('Location: /app/login.php');
    exit();
}

// Fetch card sets from the database
$query = "SELECT game_id, game_name FROM games";
$result = $conn->query($query);
$cardSets = $result->fetch_all(MYSQLI_ASSOC); // Fetch all results as an associative array

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/styles.css">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Insert Card</title>
</head>
<body>
    <?php include "/templates/navbar.php"; ?>
    
    <!-- Add the 'columns' wrapper here -->
    <section class="section">
        <div class="columns">
            <!-- Empty column for spacing -->
            <div class="column is-one-quarter"></div>
            
            <!-- Main form column -->
            <div class="column">
                <h1 class="title">Insert New Card</h1>
                <h2 class="subtitle">Only Pokemon is implemented atm!</h2>
                <form action="/Scripts/insert_card.php" method="POST">
                    <label class="label" for="card_set">Game:</label>
                    <select class="select" id="card_set" name="card_game" required>
                        <option value="">--Select a Game--</option>
                        <?php foreach ($cardSets as $set): ?>
                            <option value="<?= $set['game_id'] ?>"><?= htmlspecialchars($set['game_name']) ?></option>
                        <?php endforeach; ?>
                    </select><br><br>

                    <label class="label" for="card_name">Card Name:</label>
                    <input class="input" type="text" id="card_name" name="card_name" required><br><br>

                    <label class="label" for="form">Form:</label>
                    <input class="input" type="text" id="form" name="form" required><br><br>

                    <label class="label" for="hp">HP:</label>
                    <input class="input" type="number" id="hp" name="hp" required><br><br>

                    <label class="label" for="typings">Typings:</label>
                    <input class="input" type="text" id="typings" name="typings" required><br><br>

                    <label class="label" for="attack1">Attack 1:</label>
                    <input class="input" type="text" id="attack1" name="attack1" required><br><br>

                    <label class="label" for="attack1_desc">Attack 1 Description:</label>
                    <input class="input" type="text" id="attack1_desc" name="attack1_desc" required><br><br>

                    <label class="label" for="attack1_cost">Attack 1 Cost:</label>
                    <input class="input" type="text" id="attack1_cost" name="attack1_cost" required><br><br>

                    <label class="label" for="attack1_type1">Attack 1 Type 1:</label>
                    <input class="input" type="text" id="attack1_type1" name="attack1_type1" required><br><br>

                    <label class="label" for="attack1_type2">Attack 1 Type 2:</label>
                    <input class="input" type="text" id="attack1_type2" name="attack1_type2"><br><br>

                    <label class="label" for="attack1_power">Attack 1 Power:</label>
                    <input class="input" type="number" id="attack1_power" name="attack1_power"><br><br>

                    <label class="label" for="weakness">Weakness:</label>
                    <input class="input" type="text" id="weakness" name="weakness" required><br><br>

                    <label class="label" for="retreat_cost_type">Retreat Cost Type:</label>
                    <input class="input" type="text" id="retreat_cost_type" name="retreat_cost_type" required><br><br>

                    <label class="label" for="retreat_cost">Retreat Cost:</label>
                    <input class="input" type="number" id="retreat_cost" name="retreat_cost" required><br><br>

                    <label class="label" for="artists">Artists:</label>
                    <input class="input" type="text" id="artists" name="artists" required><br><br>

                    <input class="button" type="submit" value="Submit">
                </form>

            </div>

            <!-- Empty column for spacing -->
            <div class="column is-one-quarter"></div>
        </div>
    </section>
</body>
</html>
