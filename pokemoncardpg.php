<?php
// Include database connection
require 'db.php'; // Use this to connect to the database

// Fetch card details for card ID 1
$cardId = 1; // Specify the card ID
$query = $pdo->prepare("SELECT * FROM pokemon_criteria WHERE card_id = :card_id");
$query->execute(['card_id' => $cardId]);
$card = $query->fetch(PDO::FETCH_ASSOC);

// Check if the card was found
if (!$card) {
    die("Card not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" type="text/css" href="styles.css">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Cardstock</title>
</head>
<body>

<?php include "./topmenu.php"; ?>

<section class="section">
</section>

<section class="section">
	<div class="container card-info-wrapper">
		<div class="columns">
			<!-- Card Details -->
			<div class="column is-three-fifths">
				<div class="content">
					<!-- Card Name -->
					<h1><?= htmlspecialchars($card['card_name']) ?></h1>	
					<!-- Card Type -->
					<h2>Card Type</h2>
					<p><?= htmlspecialchars($card['type']) ?></p>
					<!-- Form -->
					<h2>Form</h2>
					<p><?= htmlspecialchars($card['form']) ?></p>
					<!-- HP -->
					<h2>HP</h2>
					<p><?= htmlspecialchars($card['hp']) ?></p>
					<!-- Typings -->
					<h2>Typings</h2>
					<p><?= htmlspecialchars($card['typings']) ?></p>
					<!-- Card Text -->
					<h2>Card Text</h2>
					<p><?= htmlspecialchars($card['card_text']) ?></p>
					<!-- Weakness -->
					<h2>Weakness</h2>
					<p><?= htmlspecialchars($card['weakness']) ?></p>
					<!-- Resistance -->
					<h2>Resistance</h2>
					<p><?= htmlspecialchars($card['resistance']) ?></p>
					<!-- Retreat Cost -->
					<h2>Retreat Cost</h2>
					<p><?= htmlspecialchars($card['retreat_cost']) ?></p>
					<!-- Set -->
					<h2>Set</h2>
					<p><?= htmlspecialchars($card['set']) ?></p>
					<!-- Artist -->
					<h2>Artist</h2>
					<p><?= htmlspecialchars($card['artist']) ?></p>
				</div>
			</div>
			<!-- Card Image -->
			<div class="column">
				<div class="card-image">
					<img src="test_pkmn.png" alt="Card Image">	<!-- Picture of Card -->
				</div>
			</div>
		</div>
	</div>
</section>
</body>
</html>
