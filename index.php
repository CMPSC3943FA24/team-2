<?php
session_set_cookie_params([
    'lifetime' => 0,        // Session expires when the browser is closed
    'path' => '/',          // Available across all directories
    'secure' => false,      // Set to true if you're using HTTPS
    'httponly' => true,     // Prevent JavaScript from accessing session cookies
]);

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start a new session if one is not already started
}

// Load config file
require 'app/config.php';

// Determine user ID
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // Use null if no user is logged in

//pull user name
$name = null;
if ($userId !== null) {
    $stmt = $conn->prepare("SELECT name FROM users WHERE user_id = ?");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stmt->bind_result($userName);
    $stmt->fetch();
    $stmt->close();

    // Store user's name in session if needed
    $_SESSION['name'] = $userName;
}

// Fetch total cards
$totalCardsQuery = "SELECT COUNT(*) AS total FROM cards" . ($userId !== null ? " WHERE owner = ?" : "");
$stmt = $conn->prepare($totalCardsQuery);
if ($userId !== null) {
    $stmt->bind_param('i', $userId); // Bind user ID if it exists
}
$stmt->execute();
$result = $stmt->get_result();
$totalCards = $result->fetch_assoc()['total'];
$stmt->close();

// Fetch unique cards
$uniqueCardsQuery = "SELECT COUNT(DISTINCT name) AS unique_count FROM cards" . ($userId !== null ? " WHERE owner = ?" : "");
$stmt = $conn->prepare($uniqueCardsQuery);
if ($userId !== null) {
    $stmt->bind_param('i', $userId); // Bind user ID if it exists
}
$stmt->execute();
$result = $stmt->get_result();
$uniqueCards = $result->fetch_assoc()['unique_count'];
$stmt->close();

// Fetch total decks
$totalDecksQuery = "SELECT COUNT(*) AS total FROM decks" . ($userId !== null ? " WHERE owner = ?" : "");
$stmt = $conn->prepare($totalDecksQuery);
if ($userId !== null) {
    $stmt->bind_param('i', $userId); // Bind user ID if it exists
}
$stmt->execute();
$result = $stmt->get_result();
$totalDecks = $result->fetch_assoc()['total'];
$stmt->close();

// Fetch active decks (you can define 'active' as needed)
$activeDecksQuery = "SELECT COUNT(*) AS active FROM decks" . ($userId !== null ? " WHERE owner = ? AND is_active = 1" : "");
$stmt = $conn->prepare($activeDecksQuery);
if ($userId !== null) {
    $stmt->bind_param('i', $userId); // Bind user ID if it exists
}
$stmt->execute();
$result = $stmt->get_result();
$activeDecks = $result->fetch_assoc()['active'];
$stmt->close();

// Fetch recent cards
$recentCardsQuery = "
    SELECT c.name AS card_name, 
           c.created_at, 
           g.game_name AS game_name 
    FROM cards c
    LEFT JOIN games g ON c.set_id = g.game_id
" . ($userId !== null ? " WHERE c.owner = ?" : "") . "
    ORDER BY c.created_at DESC 
    LIMIT 5
";
$stmt = $conn->prepare($recentCardsQuery);
if ($userId !== null) {
    $stmt->bind_param('i', $userId); // Bind user ID if it exists
}
$stmt->execute();
$recentCards = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch recent decks
$recentDecksQuery = "
    SELECT d.deck_name AS name, COUNT(dc.card_id) AS card_count, d.created_at
    FROM decks d
    LEFT JOIN deck_cards dc ON d.deck_id = dc.deck_id
" . ($userId !== null ? " WHERE d.owner = ?" : "") . "
    GROUP BY d.deck_id
    ORDER BY d.created_at DESC
    LIMIT 5";
    
$stmt = $conn->prepare($recentDecksQuery);
if ($userId !== null) {
    $stmt->bind_param('i', $userId); // Bind user ID if it exists
}
$stmt->execute();
$recentDecks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
//Generate # for background
$bg = rand(1, 9);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>

<style>
  body {
    background-image: url('images/dash/<?php echo $bg; ?>.jpg') !important;
    background-size: cover !important;
    background-position: center !important;
    background-repeat: no-repeat !important;
    background-attachment: fixed !important;
  }
</style>


<?php include "templates/navbar.php"; ?>

<!-- Header Section -->
<section class="hero is-primary">
  <div class="hero-body">
    <div class="container">
      <h1 class="title"> 
        <?php 
          if (isset($_SESSION['name'])) {
            echo "Welcome back, " . htmlspecialchars($name);
          } else {
            echo "Welcome! Log in to see personal stats.";
          }
        ?>
      </h1>
      <h2 class="subtitle">Here’s an overview of your card collection.</h2>
    </div>
  </div>
</section>


<!-- Stats Section -->
<section class="section">
  <div class="container">
    <div class="columns">
      <div class="column is-one-quarter">
        <div class="card">
          <div class="card-content">
            <p class="title">Total Cards</p>
            <p class="subtitle"><?= $totalCards; // PHP variable for total cards ?></p>
          </div>
        </div>
      </div>
      <div class="column is-one-quarter">
        <div class="card">
          <div class="card-content">
            <p class="title">Unique Cards</p>
            <p class="subtitle"><?= $uniqueCards; // PHP variable for unique cards ?></p>
          </div>
        </div>
      </div>
      <div class="column is-one-quarter">
        <div class="card">
          <div class="card-content">
            <p class="title">Total Decks</p>
            <p class="subtitle"><?= $totalDecks; // PHP variable for total decks ?></p>
          </div>
        </div>
      </div>
      <div class="column is-one-quarter">
        <div class="card">
          <div class="card-content">
            <p class="title">Active Decks</p>
            <p class="subtitle"><?= $activeDecks; // PHP variable for active decks ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Recent Cards and Decks Section -->
<section class="section">
  <div class="container">
    <div class="columns">
      <!-- Recent Cards -->
      <div class="column is-half">
        <div class="card">
          <header class="card-header">
            <p class="card-header-title">Recent Cards</p>
          </header>
          <div class="card-content">
            <table class="table is-fullwidth">
              <thead>
                <tr>
                  <th>Card Name</th>
                  <th>Date Added</th>
                  <th>Type</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($recentCards as $card): ?>
                  <tr>
                    <td><?= $card['card_name']; ?></td>
                    <td><?= $card['created_at']; ?></td>
                    <td><?= $card['game_name']; ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Recent Decks -->
      <div class="column is-half">
        <div class="card">
          <header class="card-header">
            <p class="card-header-title">Recent Decks</p>
          </header>
          <div class="card-content">
            <table class="table is-fullwidth">
              <thead>
                <tr>
                  <th>Deck Name</th>
                  <th>Cards</th>
                  <th>Date Created</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($recentDecks as $deck): ?>
                  <tr>
                    <td><?= $deck['name']; ?></td>
                    <td><?= $deck['card_count']; ?></td>
                    <td><?= $deck['created_at']; ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

</body>
</html>
