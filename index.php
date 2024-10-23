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
//Load config file
require 'app/config.php';

// Fetch total cards
$totalCardsQuery = "SELECT COUNT(*) AS total FROM cards WHERE owner = ?";
$stmt = $conn->prepare($totalCardsQuery);
$stmt->bind_param('i', $userId);
if (!isset($_SESSION['user_id'])) {
	$userId = *;
}else{
	$userId = $_SESSION['user_id'];
}
$stmt->execute();
$result = $stmt->get_result();
$totalCards = $result->fetch_assoc()['total'];
$stmt->close();

// Fetch unique cards
$uniqueCardsQuery = "SELECT COUNT(DISTINCT card_name) AS unique_count FROM cards WHERE owner = ?";
$stmt = $conn->prepare($uniqueCardsQuery);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
$uniqueCards = $result->fetch_assoc()['unique_count'];
$stmt->close();

// Fetch total decks
$totalDecksQuery = "SELECT COUNT(*) AS total FROM decks WHERE owner = ?";
$stmt = $conn->prepare($totalDecksQuery);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
$totalDecks = $result->fetch_assoc()['total'];
$stmt->close();

// Fetch active decks (you can define 'active' as needed)
$activeDecksQuery = "SELECT COUNT(*) AS active FROM decks WHERE owner = ? AND is_active = 1";
$stmt = $conn->prepare($activeDecksQuery);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
$activeDecks = $result->fetch_assoc()['active'];
$stmt->close();

// Fetch recent cards
$recentCardsQuery = "SELECT card_name AS name, created_at, card_type AS type FROM cards WHERE owner = ? ORDER BY created_at DESC LIMIT 5";
$stmt = $conn->prepare($recentCardsQuery);
$stmt->bind_param('i', $userId);
$stmt->execute();
$recentCards = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

//Fetch recent decks
$recentDecksQuery = "
    SELECT d.deck_name AS name, COUNT(dc.card_id) AS card_count, d.created_at
    FROM decks d
    LEFT JOIN deck_cards dc ON d.deck_id = dc.deck_id
    WHERE d.owner_id = ?
    GROUP BY d.deck_id
    ORDER BY d.created_at DESC
    LIMIT 5";
    
$stmt = $conn->prepare($recentDecksQuery);
$stmt->bind_param('i', $userId);
$stmt->execute();
$recentDecks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
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

<!-- Header Section -->
<section class="hero is-primary">
  <div class="hero-body">
    <div class="container">
      <h1 class="title">Welcome back, <?php htmlspecialchars($_SESSION['username']) ?></h1>
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
                    <td><?= $card['name']; ?></td>
                    <td><?= $card['date_added']; ?></td>
                    <td><?= $card['type']; ?></td>
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
                    <td><?= $deck['date_created']; ?></td>
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
