<?php
// Include your database connection file
include "config.php"; // adjust the path to your database connection file

// Start session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // User is not logged in, redirect to the login page
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id']; // Get user ID from the session

// Query to get the total cards, unique cards, and total decks
$totalCardsQuery = "SELECT COUNT(*) AS total_cards FROM cards WHERE owner = ?";
$uniqueCardsQuery = "SELECT COUNT(DISTINCT name) AS unique_cards FROM cards WHERE owner = ?";
$totalDecksQuery = "SELECT COUNT(*) AS total_decks FROM decks WHERE owner = ?";

// Prepare and execute the queries
$stmtTotalCards = $conn->prepare($totalCardsQuery);
$stmtUniqueCards = $conn->prepare($uniqueCardsQuery);
$stmtTotalDecks = $conn->prepare($totalDecksQuery);

$stmtTotalCards->bind_param("i", $user_id);
$stmtUniqueCards->bind_param("i", $user_id);
$stmtTotalDecks->bind_param("i", $user_id);

$stmtTotalCards->execute();
$stmtUniqueCards->execute();
$stmtTotalDecks->execute();

$totalCardsResult = $stmtTotalCards->get_result();
$uniqueCardsResult = $stmtUniqueCards->get_result();
$totalDecksResult = $stmtTotalDecks->get_result();

$totalCards = $totalCardsResult->fetch_assoc()['total_cards'];
$uniqueCards = $uniqueCardsResult->fetch_assoc()['unique_cards'];
$totalDecks = $totalDecksResult->fetch_assoc()['total_decks'];

// Query to get the card inventory
$inventoryQuery = "SELECT name AS card_name, number_owned, set_id AS game, images AS card_image FROM cards WHERE owner = ?";

// Prepare and execute the inventory query
$stmtInventory = $conn->prepare($inventoryQuery);
$stmtInventory->bind_param("i", $user_id);
$stmtInventory->execute();
$inventoryResult = $stmtInventory->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" href="../styles.css">
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
	
	<?php include "../templates/navbar.php"; ?>

	<p>
		<?php
		echo 'Session ID: ' . session_id();
		echo 'User ID: ' . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Not set');
		echo 'Username: ' . (isset($_SESSION['username']) ? $_SESSION['username'] : 'Not set');
		?>
	</p>

	<!-- Spacer -->
	<section class="section">
		<div class="container columns is-8">
			<!-- Left table for totals -->
			<div class="column is-narrow">
				<table class="table is-bordered is-striped is-fullwidth">
					<tbody>
						<tr>
							<th>Total Cards</th>
							<td><?php echo $totalCards; ?></td>
						</tr>
						<tr>
							<th>Unique Cards</th>
							<td><?php echo $uniqueCards; ?></td>
						</tr>
						<tr>
							<th>Total Decks</th>
							<td><?php echo $totalDecks; ?></td>
						</tr>
					</tbody>
				</table>
			</div>
			
			<!-- Right table for inventory -->
			<div class="column">
				<div class="is-size-2">
					<p>Inventory</p>
				</div>
				<br><br>
				<table class="table is-bordered is-striped is-fullwidth">
					<tbody>
						<tr>
							<th>Card Name</th>
							<th>Number Owned</th>
							<th>Game</th>
							<th>Card Image</th>
						</tr>
						
						<?php while ($row = $inventoryResult->fetch_assoc()): ?>
						<tr>
							<td><?php echo htmlspecialchars($row['card_name']); ?></td>
							<td><?php echo htmlspecialchars($row['number_owned']); ?></td>
							<td><?php echo htmlspecialchars($row['game']); ?></td>
							<td><img src="<?php echo htmlspecialchars($row['card_image']); ?>" alt="Card Image" width="50"></td>
						</tr>
						<?php endwhile; ?>
					</tbody>
				</table>
			</div>
		</div>
	</section>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
