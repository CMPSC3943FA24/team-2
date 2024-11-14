<?php
// Start session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Enable debugging mode
define('DEBUG_MODE', true);

// Include the database connection
require 'config.php';

$card = null;
$error_message = "";

try {
    // Check if card_id is set and is a valid number
    if (isset($_GET['card_id']) && is_numeric($_GET['card_id'])) {
        $card_id = $_GET['card_id'];

        // Prepare and execute the SQL statement to fetch MTG card details
        $stmt = $conn->prepare("
            SELECT c.card_id, c.name AS card_name, m.mana_cost, m.mana_type, m.mana_value, 
                   m.power, m.toughness, m.expansion, m.rarity, m.card_number, m.artist, 
                   c.images AS image_path
            FROM cards c
            JOIN magic_criteria m ON c.card_id = m.card_id
            WHERE c.card_id = ?
        ");

        // Check if statement preparation was successful
        if (!$stmt) {
            throw new Exception("Failed to prepare the statement: " . $conn->error);
        }

        $stmt->bind_param("i", $card_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if we have a result
        if ($result->num_rows > 0) {
            $card = $result->fetch_assoc();
        } else {
            $error_message = "Card not found.";
        }
    } else {
        $error_message = "Invalid card ID.";
    }
} catch (Exception $e) {
    $error_message = "An error occurred while retrieving the card details.";
    if (DEBUG_MODE) {
        $error_message .= " Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo isset($card) ? htmlspecialchars($card['card_name']) . " - MTG Card" : "Error - MTG Card"; ?></title>
</head>
<body>

<?php include "../templates/navbar.php"; ?>

<section class="section">
    <div class="container card-info-wrapper">
        <?php if ($error_message): ?>
            <!-- Display error message -->
            <div class="notification is-danger">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php else: ?>
            <div class="columns">
                <!-- Card Image -->
                <div class="column is-one-third">
                    <div class="card-image">
                        <img src="<?php echo htmlspecialchars($card['image_path']); ?>" alt="Card Image">
                    </div>
                </div>
                <!-- Card Details -->
                <div class="column">
                    <h1 class="title"><?php echo htmlspecialchars($card['card_name']); ?></h1>
                    <table class="table is-bordered is-striped is-narrow is-fullwidth">
                        <tbody>
                            <tr>
                                <th>Mana Cost</th>
                                <td><?php echo htmlspecialchars($card['mana_cost']); ?></td>
                            </tr>
                            <tr>
                                <th>Mana Type</th>
                                <td><?php echo htmlspecialchars($card['mana_type']); ?></td>
                            </tr>
                            <tr>
                                <th>Mana Value</th>
                                <td><?php echo htmlspecialchars($card['mana_value']); ?></td>
                            </tr>
                            <tr>
                                <th>Power</th>
                                <td><?php echo htmlspecialchars($card['power']); ?></td>
                            </tr>
                            <tr>
                                <th>Toughness</th>
                                <td><?php echo htmlspecialchars($card['toughness']); ?></td>
                            </tr>
                            <tr>
                                <th>Expansion</th>
                                <td><?php echo htmlspecialchars($card['expansion']); ?></td>
                            </tr>
                            <tr>
                                <th>Rarity</th>
                                <td><?php echo htmlspecialchars($card['rarity']); ?></td>
                            </tr>
                            <tr>
                                <th>Card Number</th>
                                <td><?php echo htmlspecialchars($card['card_number']); ?></td>
                            </tr>
                            <tr>
                                <th>Artist</th>
                                <td><?php echo htmlspecialchars($card['artist']); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
</body>
</html>
