<?php
// Fetch card sets from the database
require '../db.php'; // Use this to connect to the database
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // User is not logged in, redirect to the login page
    header('Location: login.php');
    exit();
}

// Get the card data from the session if available
$card = isset($_SESSION['card']) ? $_SESSION['card'] : null;
$card_criteria = isset($_SESSION['card_criteria']) ? $_SESSION['card_criteria'] : null;
$card_all = isset($_SESSION['card_all']) ? $_SESSION['card_all'] : [];

// Clear the session data after retrieving
unset($_SESSION['card']);
unset($_SESSION['card_criteria']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Get Card</title>
</head>
<body>
    <?php include "../templates/navbar.php"; ?>
    
    <section class="section">
        <div class="columns">
            <div class="column is-one-quarter"></div>
            
            <div class="column">
                <h1 class="title">Get Card by ID (Testing)</h1>
                <form action="../Scripts/get_card.php" method="POST">
                    <label class="label" for="card_id">Card ID</label>
                    <input class="input" type="number" id="card_id" name="card_id" required><br><br>
                    <input class="button" type="submit" value="Submit">
                </form>
                <form action="../Scripts/return_all.php" method="POST">
                    <input class="button" type="submit" value="Get All">
                </form>
                
                <!-- Display card information if available -->
                <?php if ($card): ?>
                    <h2 class="subtitle">Card Details:</h2>
                    <p><strong>Card ID:</strong> <?= htmlspecialchars($card['card_id']) ?></p>
                    <p><strong>Name:</strong> <?= htmlspecialchars($card['name']) ?></p>
                    <p><strong>Set ID:</strong> <?= htmlspecialchars($card['set_id']) ?></p>
                    <?php if ($card_criteria): ?>
                        <h3>Criteria:</h3>
                        <pre><?= htmlspecialchars(print_r($card_criteria, true)) ?></pre>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($card_all): ?>
                    <h2 class="subtitle">All Cards:</h2>
                    <ul>
                        <?php foreach ($card_all as $card): ?>
                            <li>
                                <strong>Card ID:</strong> <?= htmlspecialchars($card['card_id']) ?><br>
                                <strong>Name:</strong> <?= htmlspecialchars($card['name']) ?><br>
                                <strong>Set ID:</strong> <?= htmlspecialchars($card['set_id']) ?><br>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <?php
                    if ($card) {
                        unset($_SESSION['card']);
                    }
                    if ($card_criteria) {
                        unset($_SESSION['card_criteria']);
                    }
                    if ($card_all) { 
                        unset($_SESSION['card_all']); // Corrected syntax here
                    }
                ?>

            </div>

            <div class="column is-one-quarter"></div>
        </div>
    </section>
</body>
</html>
