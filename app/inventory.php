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

// Query to get the total cards, unique cards, and total decks
$totalCardsQuery = "SELECT COUNT(*) AS total_cards FROM cards WHERE owner = ?";
$uniqueCardsQuery = "SELECT COUNT(DISTINCT name) AS unique_cards FROM cards WHERE owner = ?";
$totalDecksQuery = "SELECT COUNT(*) AS total_decks FROM decks WHERE owner = ?";

// Prepare and execute the queries
$stmtTotalCards = $conn->prepare($totalCardsQuery);
if (!$stmtTotalCards) {
    die("Prepare failed for total cards: " . $conn->error);
}
$stmtUniqueCards = $conn->prepare($uniqueCardsQuery);
if (!$stmtUniqueCards) {
    die("Prepare failed for unique cards: " . $conn->error);
}
$stmtTotalDecks = $conn->prepare($totalDecksQuery);
if (!$stmtTotalDecks) {
    die("Prepare failed for total decks: " . $conn->error);
}

$stmtTotalCards->bind_param("i", $user_id);
$stmtUniqueCards->bind_param("i", $user_id);
$stmtTotalDecks->bind_param("i", $user_id);

if (!$stmtTotalCards->execute()) {
    die("Execution failed for total cards: " . $stmtTotalCards->error);
}
$totalCardsResult = $stmtTotalCards->get_result();
$totalCards = $totalCardsResult->fetch_assoc()['total_cards'];
$stmtTotalCards->free_result(); // Free result after fetching

if (!$stmtUniqueCards->execute()) {
    die("Execution failed for unique cards: " . $stmtUniqueCards->error);
}
$uniqueCardsResult = $stmtUniqueCards->get_result();
$uniqueCards = $uniqueCardsResult->fetch_assoc()['unique_cards'];
$stmtUniqueCards->free_result(); // Free result after fetching

if (!$stmtTotalDecks->execute()) {
    die("Execution failed for total decks: " . $stmtTotalDecks->error);
}
$totalDecksResult = $stmtTotalDecks->get_result();
$totalDecks = $totalDecksResult->fetch_assoc()['total_decks'];
$stmtTotalDecks->free_result(); // Free result after fetching

// Query to get the card inventory
$inventoryQuery = "SELECT card_id, name AS card_name, number_owned, set_id AS game, images AS card_image FROM cards WHERE owner = ?";

// Prepare and execute the inventory query
$stmtInventory = $conn->prepare($inventoryQuery);
if (!$stmtInventory) {
    die("Prepare failed for inventory: " . $conn->error);
}

$stmtInventory->bind_param("i", $user_id);
if (!$stmtInventory->execute()) {
    die("Execution failed for inventory: " . $stmtInventory->error);
}

$inventoryResult = $stmtInventory->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    
    <?php include "../templates/navbar.php"; ?>

    <!-- Spacer -->
    <section class="section">
        <div class="container columns is-8">
            <!-- Left table for totals -->
            <div class="column is-narrow">
                <table class="table is-bordered is-striped is-fullwidth">
                    <tbody>
                        <tr>
                            <th>Total Cards</th>
                            <td><?php echo $totalCards; ?></td>
                        </tr>
                        <tr>
                            <th>Unique Cards</th>
                            <td><?php echo $uniqueCards; ?></td>
                        </tr>
                        <tr>
                            <th>Total Decks</th>
                            <td><?php echo $totalDecks; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Right table for inventory -->
            <div class="column">
                <div class="is-size-2">
                    <p>Inventory</p>
                </div>
                <br><br>
                <table class="table is-bordered is-striped is-fullwidth">
                    <tbody>
                        <tr>
                            <th>Card Image</th>
                            <th>Card Name</th>
                            <th>Number Owned</th>
                            <th>Game</th>
                        </tr>
                        
                        <?php while ($row = $inventoryResult->fetch_assoc()): ?>
                        <tr>
                            <td><img src="<?php echo htmlspecialchars($row['card_image']); ?>" alt="Card Image" width="50"></td>
                            <td>
                                <a href="card_page.php?card_id=<?php echo htmlspecialchars($row['card_id']); ?>">
                                    <?php echo htmlspecialchars($row['card_name']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($row['number_owned']); ?></td>
                            <td>
                                <button class="button is-small" onclick="updateCardQuantity(<?php echo $row['card_id']; ?>, -1)">-</button>
                                <span id="quantity-<?php echo $row['card_id']; ?>"><?php echo $row['number_owned']; ?></span>
                                <button class="button is-small" onclick="updateCardQuantity(<?php echo $row['card_id']; ?>, 1)">+</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

</body>
</html>

<script>
    function updateCardQuantity(cardId, change) {
        // Get the current quantity from the span element
        var currentQuantityElement = document.getElementById("quantity-" + cardId);
        var currentQuantity = parseInt(currentQuantityElement.innerText);

        // Update the quantity based on the button clicked
        var newQuantity = currentQuantity + change;

        // Don't allow the number of owned cards to go below zero
        if (newQuantity < 0) {
            alert("You cannot have less than 0 cards.");
            return;
        }

        // Update the quantity in the DOM
        currentQuantityElement.innerText = newQuantity;

        // Send the new quantity to the server via AJAX
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "update_card_quantity.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Optionally handle success or failure
                console.log(xhr.responseText); // You can display a success message if needed
            }
        };
        xhr.send("card_id=" + cardId + "&new_quantity=" + newQuantity);
    }
</script>


<?php
// Close the database connection
$conn->close();
?>
