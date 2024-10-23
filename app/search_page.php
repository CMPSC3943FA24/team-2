<?php
// Enable error reporting for PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection file
require 'config.php'; // Ensure the correct path and filename

// Include the navbar (assuming your navbar is in a file like 'navbar.php')
include "../templates/navbar.php"; 

$search_results = [];
$search_term = '';
$game_filter = '';
$name_filter = '';
$games = [];

// Fetch games for filtering
$games_query = "SELECT game_id, game_name FROM games";
$games_result = $conn->query($games_query);
if ($games_result && $games_result->num_rows > 0) {
    while ($game = $games_result->fetch_assoc()) {
        $games[] = $game;
    }
}

try {
    if (isset($_GET['search_term'])) {
        $search_term = $conn->real_escape_string($_GET['search_term']);
        $game_filter = isset($_GET['game_filter']) ? $conn->real_escape_string($_GET['game_filter']) : '';
        $name_filter = isset($_GET['name_filter']) ? $conn->real_escape_string($_GET['name_filter']) : '';

        // Query the database
        $query = "
            SELECT c.card_id, c.images, c.name, g.game_name 
            FROM cards c
            LEFT JOIN games g ON c.set_id = g.game_id
            WHERE 1=1
        ";

        if (!empty($search_term)) {
            $query .= " AND c.name LIKE '%$search_term%'";
        }

        if (!empty($game_filter)) {
            $query .= " AND c.set_id = '$game_filter'";
        }

        if (!empty($name_filter)) {
            $query .= " AND c.name LIKE '%$name_filter%'";
        }
        
        $result = $conn->query($query);

        // Check for query errors
        if (!$result) {
            throw new Exception("Database Query Failed: " . $conn->error);
        }

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $search_results[] = $row;
            }
        }
    }
} catch (Exception $e) {
    // Display error message if query or connection fails
    echo "<div class='notification is-danger'>Error: " . $e->getMessage() . "</div>";
}
echo $search_term;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Page</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <section class="section">
        <div class="container">

        <div class="box" id="searchBox">
            <h2 class="title is-4 has-text-centered">
                <a href="#" id="toggleSearchBox">Search</a>
            </h2>
            <div id="searchContent" style="display: none;">

                <!-- Filtering Options -->
                <form method="GET" action="">
                    <h2 class="title">Search</h2>
                    <section class="section">
                        <div class="container">
                            <div class="columns is-left">
                                <div class="column is-three-fifths">
                                    <!-- Search -->
                                    <div class="field has-addons">
                                        <div class="control is-expanded">
                                            <input class="input" type="text" name="search_term" placeholder="Search" value="<?php echo htmlspecialchars($search_term); ?>">
                                        </div>
                                        <div class="control">
                                            <button class="button is-info">
                                                Search
                                            </button>
                                        </div>
                                    </div>
                                    <!-- Filter By Game Dropdown -->
                                    <div class="field">
                                        <label class="label">Filter by Game:</label>
                                        <div class="control">
                                            <div class="select">
                                                <select name="game_filter">
                                                    <option value="">All Games</option>
                                                    <?php foreach ($games as $game): ?>
                                                        <option value="<?php echo htmlspecialchars($game['game_id']); ?>" <?php echo $game_filter == $game['game_id'] ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($game['game_name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Filter By Card Name -->
                                    <div class="field">
                                        <label class="label">Filter by Card Name:</label>
                                        <div class="control">
                                            <input class="input" type="text" name="name_filter" placeholder="Enter card name" value="<?php echo htmlspecialchars($name_filter); ?>">
                                        </div>
                                    </div>

                                    <!-- Apply Filters Button -->
                                    <div class="field">
                                        <div class="control">
                                            <button class="button is-primary" type="submit">Apply Filters</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </form> 
            </div>
        </div>
            
            <!-- Search Results Table -->
            <h1 class="title">Search Results</h1>
            <?php if (!empty($search_results)): ?>
                <table class="table is-striped is-fullwidth">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Game</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($search_results as $result): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($result['card_id']); ?></td>
                                <td>
                                    <img src="<?php echo htmlspecialchars($result['images']); ?>" alt="Card Image" width="50">
                                </td>
                                <td>
                                    <a href="card_page.php?card_id=<?php echo htmlspecialchars($result['card_id']); ?>">
                                        <?php echo htmlspecialchars($result['name']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($result['game_name']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <?php elseif (isset($_GET['search_term'])): ?>
                <p>No results found for "<?php echo htmlspecialchars($search_term); ?>"</p>
            <?php endif; ?>
        </div>
    </section>

</body>
</html>
