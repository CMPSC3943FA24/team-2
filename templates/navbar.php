<?php
session_start();
echo 'Current directory: ' . getcwd(); 
require __DIR__ . '/../app/config.php';

$profilePicture = '/images/account.png'; //default account image

//Check if user is logged in
if (isset($_SESSION['user_id'])){
	$user_id = $_SESSION['user_id'];

	//query to get profile picture link
	$userQuery = "SELECT profile_picture FROM users WHERE user_id = ?";
	$stmtUser = $conn->prepare($userQuery);
	$stmtUser->bind_param("i", $user_id);
	$stmtUser->execute();
	$userResult = $stmtUser->get_result();

	if (userResult->num_rows > 0){
		$userData = $userResult->fetch_assoc();
		$profilePicture = htmlspecialchars($userData['profile_picture']);
		$_SESSION['profile_picture'] = $profilePicture; // Set session variable
	}
}
?>

<!-- Navbar Section -->
<nav class="navbar is-light">
    <div class="navbar-brand">
        <a href="/" class="navbar-item" id="main">
            <strong>CARDSTOCK</strong>
        </a>
        <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarBasicExample">
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
        </a>
    </div>

    <div id="navbarBasicExample" class="navbar-menu">
        <div class="navbar-start">
            <a href="/app/inventory.php" class="navbar-item">Inventory</a>
            <a href="/app/decks.php" class="navbar-item">Decks</a>
            <a href="/app/insert_form.php" class="navbar-item">Card Input Form</a>
            <a href="/app/print_card.php" class="navbar-item">Card Print</a>
        </div>
        <!-- Search Box -->
        <form action="/app/search_page.php" method="GET" class="navbar-item">
            <div class="field has-addons">
                <div class="control">
                    <input class="input" type="text" name="search_term" placeholder="Search..." required>
                </div>
                <div class="control">
                    <button class="button is-info" type="submit">Search</button>
                </div>
            </div>
        </form>

		<div class="navbar-item">
            <a href="/app/account_page.php" class="navbar-item">
                <img src="<?php echo $profilePicture; ?>" width="55px" height="40px" alt="Account">
            </a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/app/account_page.php">Account</a>
                <a href="/Scripts/logout.php">Log Out</a>
            <?php else: ?>
                <a href="/app/login.php">Log In</a>
            <?php endif; ?>
        </div>

    </div>
</nav>

</body>
</html>
