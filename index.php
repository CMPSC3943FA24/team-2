<?php
session_start();
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" href="styles.css">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Cardstock</title>
</head>
<body>
	
	<?php include "./topmenu.php"; ?>
	
	<!-- Spacer -->
	<section class="section">
		<div class="container">
			<!-- Game Selection Section -->
			<div class="columns is-centered">
				<div class="column is-one-third has-text-centered">
					<a href="templates/mtgcardpg.php" class="button is-fullwidth is-primary is-outlined">Magic</a>
				</div>
				<div class="column is-one-third has-text-centered">
					<a href="templates/pokemoncardpg.php" class="button is-fullwidth is-warning is-outlined">Pok&eacute;mon</a>
				</div>
				<div class="column is-one-third has-text-centered">
					<a href="templates/yugiohcardpg.php" class="button is-fullwidth is-danger is-outlined">Yu-Gi-Oh!</a>
				</div>
			</div>
		</div>
	</section>

	<!-- Continue Last Deck Section -->
	<section class="section">
		<div class="container has-text-centered">
			<a href="#" class="button is-large is-success">Continue Last Deck</a>
		</div>
	</section>

</body>
</html>
