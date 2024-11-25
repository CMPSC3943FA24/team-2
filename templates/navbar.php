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
		$_SESSION['profile_picture'] = $profilePicture; // Set session variable ####ADD CHECK FOR NULL AND POINT TO DEFAULT IMAGE IF PFP IS NOT SET
	}
}
?>

<!-- Navbar Section -->
<nav class="navbar" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
        <a href="/" class="navbar-item">
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
            <a href="/app/add_card.php" class="navbar-item">Add Card</a>
        </div>

        <!-- Profile Picture and Logout Button -->
        <div class="navbar-item" style="display: flex; align-items: center; justify-content: center;">
            <a href="/app/account_page.php" style="display: block; width: 48px; height: 48px;">
                <?php
                // Check if the session variable is set and not null
                if (isset($_SESSION['profile_picture']) && !empty($_SESSION['profile_picture'])) {
                    $profilePicture = htmlspecialchars($_SESSION['profile_picture']);
                    echo '<img src="' . $profilePicture . '?' . time() . '" alt="Profile Picture" style="object-fit: cover; width: 100%; height: 100%;">';
                } else {
                    // Default image if no profile picture is found
                    echo '<img src="/images/account.jpg" alt="Default Profile Picture" style="object-fit: cover; width: 100%; height: 100%;">';
                }
                ?>
            </a>
        </div>



            <!-- Logout Button -->
            <div class="navbar-item is-hidden-touch">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a class="button is-danger is-small" href="/Scripts/logout.php">Log Out</a>
                <?php else: ?>
                    <a class="button is-primary is-small" href="/app/login.php">Log In</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', () => {
    const burger = document.querySelector('.navbar-burger');
    const menu = document.querySelector('.navbar-menu');

    if (burger && menu) {
        burger.addEventListener('click', () => {
            burger.classList.toggle('is-active');
            menu.classList.toggle('is-active');
        });
    }
    });

</script>

</body>
</html>
