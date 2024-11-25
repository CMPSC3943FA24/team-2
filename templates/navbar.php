<!-- Navbar Section -->
<nav class="navbar" role="navigation" aria-label="main navigation">
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
            <a href="/app/add_card.php" class="navbar-item">Add Card</a>
        </div>

        <!-- Search Box -->
        <div class="navbar-item">
            <form action="/app/search_page.php" method="GET" class="field has-addons">
                <div class="control">
                    <input class="input" type="text" name="search_term" placeholder="Search" required>
                </div>
                <div class="control">
                    <button class="button is-info" type="submit">Search</button>
                </div>
            </form>
        </div>

        <div class="navbar-end">
            <!-- Profile Picture -->
            <div class="navbar-item">
                <a href="/app/account_page.php" style="display: flex; align-items: center; justify-content: center;">
                    <?php
                    if (isset($_SESSION['profile_picture']) && !empty($_SESSION['profile_picture'])) {
                        $profilePicture = htmlspecialchars($_SESSION['profile_picture']);
                        echo '<img src="' . $profilePicture . '?' . time() . '" alt="Profile Picture" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; object-position: center;">';
                    } else {
                        echo '<img src="/images/account.jpg" alt="Default Profile Picture" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; object-position: center;">';
                    }
                    ?>
                </a>
            </div>

            <!-- Logout/Login Button -->
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
