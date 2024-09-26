<?php
// Include database connection
require 'db.php'; // Use this to connect to the database

// Fetch card details for card ID 1
$cardId = 1; // Specify the card ID
$query = $pdo->prepare("SELECT * FROM pokemon_criteria WHERE card_id = :card_id");
$query->execute(['card_id' => $cardId]);
$card_criteria = $query->fetch(PDO::FETCH_ASSOC);

$query = $pdo->prepare("SELECT * FROM cards WHERE card_id = :card_id");
$query->execute(['card_id' => $cardId]);
$card_name = $query->fetch(PDO::FETCH_ASSOC);


// Check if the card was found
if (!$card_criteria && !$card_name) {
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
					<h1><?= htmlspecialchars($card_name['name']) ?></h1>	
					<!-- Card Type -->
					<h2>Card Type</h2>
						<p><?= htmlspecialchars($card_criteria['typings']) ?></p>
					<!-- Form -->
					<h2>Form</h2>
						<p><?= htmlspecialchars($card_criteria['form']) ?></p>
					<!-- HP -->
					<h2>HP</h2>
						<p><?= htmlspecialchars($card_criteria['hp']) ?></p>
					<!-- Typings -->
					<h2>Typings</h2>
						<p><?= htmlspecialchars($card_criteria['typings']) ?></p>
					<!-- Card Text -->
					<h2>Card Text</h2>
						<h4 class="subtitle is-4">Attack</h4>
							<p><?= htmlspecialchars($card_criteria['attack1']) ?></p>
						<h4 class="subtitle is-4">Description</h4>
							<p><?= htmlspecialchars($card_criteria['attack1_desc']) ?></p>
						<h4 class="subtitle is-4">Cost</h4>
							<p><?= htmlspecialchars($card_criteria['attack1_cost']) ?></p>
						<h4 class="subtitle is-4">Type</h4>
							<p><?= htmlspecialchars($card_criteria['attack1_type1']) ?></p>
							<p><?= htmlspecialchars($card_criteria['attack1_type2']) ?></p>
						<h4 class="subtitle is-4">Power</h4>
							<p><?= htmlspecialchars($card_criteria['attack1_power']) ?></p>
					<!-- Weakness -->
					<h2>Weakness</h2>
						<p><?= htmlspecialchars($card_criteria['weakness']) ?></p>
					<!-- Resistance -->
					<h2>Resistance</h2>
						<p><?= htmlspecialchars($card_criteria['resistance']) ?></p>
					<!-- Retreat Cost -->
					<h2>Retreat Cost</h2>
						<p><?= htmlspecialchars($card_criteria['retreat_cost']) ?></p>
					<!-- Set -->
					<h2>Set</h2>
						<p><?= htmlspecialchars($card_criteria['set']) ?></p>
					<!-- Artist -->
					<h2>Artist</h2>
						<p><?= htmlspecialchars($card_criteria['artist']) ?></p>
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
