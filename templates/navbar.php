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
        <div class="navbar-item">
            <a href="/app/account_page.php">
                <?php
                // Ensure profile picture is displayed
                $profilePicture = isset($_SESSION['profile_picture']) && !empty($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : '/images/account.jpg';
                echo '<figure class="image is-48x48 is-rounded"><img src="' . $profilePicture . '?' . time() . '" alt="Profile Picture" style="width: 48px; height: 48px;"></figure>';
                ?>
            </a>
        </div>

        <div class="navbar-item"><figure class = "image is-48x48"><img src="/uploads/2_profile.jpg"></img></figure></div>




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
