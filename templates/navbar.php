<?php
require __DIR__ . '/../app/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start a new session if one is not already started
}
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

	if ($userResult->num_rows > 0){
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
            <a href="/app/add_card.php" class="navbar-item">Card Input Form</a>
            <a href="/app/print_card.php" class="navbar-item">Card Print</a>
        </div>
        <!-- Search Box -->
        <form action="/app/search_page.php" method="GET" class="navbar-item">
            <div class="field has-addons">
                <div class="control">
                    <input class="input" type="text" name="search_term" placeholder="Search" required>
                </div>
                <div class="control">
                    <button class="button is-info" type="submit">Search</button>
                </div>
            </div>
        </form>
    </div>

    <div style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; padding-top: 5px;">
    <a href="/app/account_page.php" style="width: 100%; height: 100%; display: block;">
        <?php
        // Check if the session variable is set and not null
        if (isset($_SESSION['profile_picture']) && !empty($_SESSION['profile_picture'])) {
            $profilePicture = htmlspecialchars($_SESSION['profile_picture']);
            echo '<img src="' . htmlspecialchars($correct_image_path) . '?' . time() . '" alt="Profile Picture" style="width: 100%; height: 100%; object-fit: cover; object-position: center;">';

        } else {
            // Default image if no profile picture is found
            echo('<img src="/images/account.jpg" alt="Profile Picture" style="width: 100%; height: 100%; object-fit: cover; object-position: center;">');
        }
        ?>
    </a>
</div>


    <div class="navbar-item">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="/Scripts/logout.php">Log Out</a>
        <?php else: ?>
            <a href="/app/login.php">Log In</a>
        <?php endif; ?>
    </div>
</nav>

</body>
</html>
