<?php
// Include your database connection file
require "config.php"; // adjust the path to your database connection file

// Start session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // User is not logged in, redirect to the login page
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id']; // Get user ID from the session

// Query to get user details
$userQuery = "SELECT username, profile_picture FROM users WHERE user_id = ?";
$stmtUser = $conn->prepare($userQuery);
$stmtUser->bind_param("i", $user_id);
$stmtUser->execute();
$userResult = $stmtUser->get_result();
$userData = $userResult->fetch_assoc();

// Query to get total cards and decks owned
$totalCardsQuery = "SELECT COUNT(*) AS total_cards FROM cards WHERE owner = ?";
$totalDecksQuery = "SELECT COUNT(*) AS total_decks FROM decks WHERE owner = ?";

$stmtTotalCards = $conn->prepare($totalCardsQuery);
$stmtTotalCards->bind_param("i", $user_id);
$stmtTotalCards->execute();
$totalCardsResult = $stmtTotalCards->get_result();
$totalCards = $totalCardsResult->fetch_assoc()['total_cards'];

$stmtTotalDecks = $conn->prepare($totalDecksQuery);
$stmtTotalDecks->bind_param("i", $user_id);
$stmtTotalDecks->execute();
$totalDecksResult = $stmtTotalDecks->get_result();
$totalDecks = $totalDecksResult->fetch_assoc()['total_decks'];

// Query to get the user's cards
$cardsQuery = "SELECT name, set_id, created_at FROM cards WHERE owner = ? ORDER BY created_at DESC";
$stmtCards = $conn->prepare($cardsQuery);
$stmtCards->bind_param("i", $user_id);
$stmtCards->execute();
$cardsResult = $stmtCards->get_result();

// Query to get the user's decks
$decksQuery = "SELECT deck_name FROM decks WHERE owner = ?";
$stmtDecks = $conn->prepare($decksQuery);
$stmtDecks->bind_param("i", $user_id);
$stmtDecks->execute();
$decksResult = $stmtDecks->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Account Page</title>
</head>
<body>
    <?php include "../templates/navbar.php"; ?>

    <div class="container">
        <div class="columns">
            <!-- User Info Column -->
            <div class="column is-one-third">
                <h1 class="title">User Information</h1>
                <img src="<?php echo htmlspecialchars("../" . $userData['profile_picture']); ?>" alt="Profile Picture" class="image is-128x128">
                <p><strong>Username:</strong> <?php echo htmlspecialchars($userData['username']); ?></p>
                <p><strong>Total Cards:</strong> <?php echo $totalCards; ?></p>
                <p><strong>Total Decks:</strong> <?php echo $totalDecks; ?></p>
                <button class="button is-primary">Edit Profile</button>
            </div>

            <!-- Tabs Column -->
            <div class="column">
                <div class="tabs">
                    <ul>
                        <li class="is-active"><a href="#cards-tab">Cards</a></li>
                        <li><a href="#decks-tab">Decks</a></li>
                    </ul>
                </div>

                <!-- Cards Tab -->
                <div id="cards-tab" class="tab-content">
                    <h2 class="subtitle">My Cards</h2>
                    <table class="table is-bordered is-striped is-fullwidth">
                        <thead>
                            <tr>
                                <th>Card Name</th>
                                <th>Set ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $cardsResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['set_id']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Decks Tab -->
                <div id="decks-tab" class="tab-content" style="display: none;">
                    <h2 class="subtitle">My Decks</h2>
                    <table class="table is-bordered is-striped is-fullwidth">
                        <thead>
                            <tr>
                                <th>Deck Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $decksResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Simple JavaScript to switch between tabs
        const tabs = document.querySelectorAll('.tabs li');
        const contents = document.querySelectorAll('.tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('is-active'));
                contents.forEach(content => content.style.display = 'none');

                tab.classList.add('is-active');
                const target = tab.querySelector('a').getAttribute('href');
                document.querySelector(target).style.display = 'block';
            });
        });
    </script>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
