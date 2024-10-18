<?php
session_set_cookie_params([
    'lifetime' => 0,        // Session expires when the browser is closed
    'path' => '/',          // Available across all directories
    'secure' => false,      // Set to true if you're using HTTPS
    'httponly' => true,     // Prevent JavaScript from accessing session cookies
]);
session_start();
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

//Load config file
if (!file_exists('config.php')) {
    die('Configuration file not found.');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" href="styles.css">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Cardstock</title>
</head>
<body>
	
	<?php include "templates/navbar.php"; ?>

	<p> <?php echo 'Session ID: ' . session_id(); echo 'User ID: ' . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Not set');
echo 'Username: ' . (isset($_SESSION['username']) ? $_SESSION['username'] : 'Not set');
?> </p>
	
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
