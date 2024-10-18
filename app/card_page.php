<?php
session_start();

// Include the database connection
require 'config.php';

// Get the card_id from the URL (make sure to sanitize the input)
if (isset($_GET['card_id']) && is_numeric($_GET['card_id'])) {
    $card_id = $_GET['card_id'];

    // Prepare and execute the SQL statement to fetch the card and criteria info
    $stmt = $conn->prepare("
        SELECT c.card_id, c.name AS card_name, pc.form, pc.hp, pc.typings,
               pc.attack1, pc.attack1_desc, pc.attack1_cost, 
               pc.attack1_type1, pc.attack1_type2, pc.attack1_power, 
               pc.weakness, pc.retreat_cost_type, pc.retreat_cost, pc.artist
        FROM cards c
        JOIN pokemon_criteria pc ON c.card_id = pc.card_id
        WHERE c.card_id = ?
    ");
    $stmt->bind_param("i", $card_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if we have a result
    if ($result->num_rows > 0) {
        $card = $result->fetch_assoc();
    } else {
        // Handle the case where no card is found
        die("Card not found.");
    }
} else {
    // Handle invalid card_id
    die("Invalid card ID.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" href="../styles.css">
	<link rel="stylesheet" type="text/css" href="styles.css">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo htmlspecialchars($card['card_name']); ?> - Cardstock</title>
</head>
<body>

<?php include "navbar.php"; ?>

<section class="section">
	<div class="container card-info-wrapper">
		<div class="columns">
			<!-- Card Image -->
			<div class="column is-one-third">
				<div class="card-image">
					<img src="<?php echo htmlspecialchars($card['images']); ?>" alt="Card Image"> <!-- Assuming images is the path -->
				</div>
			</div>
			<!-- Card Details -->
			<div class="column">
				<h1 class="title"><?php echo htmlspecialchars($card['card_name']); ?></h1>
				<table class="table is-bordered is-striped is-narrow is-fullwidth">
					<tbody>
						<tr>
							<th>Form</th>
							<td><?php echo htmlspecialchars($card['form']); ?></td>
						</tr>
						<tr>
							<th>HP</th>
							<td><?php echo htmlspecialchars($card['hp']); ?></td>
						</tr>
						<tr>
							<th>Typings</th>
							<td><?php echo htmlspecialchars($card['typings']); ?></td>
						</tr>
						<tr>
							<th>Attack 1</th>
							<td><?php echo htmlspecialchars($card['attack1']); ?></td>
						</tr>
						<tr>
							<th>Attack 1 Description</th>
							<td><?php echo htmlspecialchars($card['attack1_desc']); ?></td>
						</tr>
						<tr>
							<th>Attack 1 Cost</th>
							<td><?php echo htmlspecialchars($card['attack1_cost']); ?></td>
						</tr>
						<tr>
							<th>Attack 1 Type 1</th>
							<td><?php echo htmlspecialchars($card['attack1_type1']); ?></td>
						</tr>
						<tr>
							<th>Attack 1 Type 2</th>
							<td><?php echo htmlspecialchars($card['attack1_type2']); ?></td>
						</tr>
						<tr>
							<th>Attack 1 Power</th>
							<td><?php echo htmlspecialchars($card['attack1_power']); ?></td>
						</tr>
						<tr>
							<th>Weakness</th>
							<td><?php echo htmlspecialchars($card['weakness']); ?></td>
						</tr>
						<tr>
							<th>Retreat Cost Type</th>
							<td><?php echo htmlspecialchars($card['retreat_cost_type']); ?></td>
						</tr>
						<tr>
							<th>Retreat Cost</th>
							<td><?php echo htmlspecialchars($card['retreat_cost']); ?></td>
						</tr>
						<tr>
							<th>Artist</th>
							<td><?php echo htmlspecialchars($card['artist']); ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</section>
</body>
</html>
