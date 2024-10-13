<?php
// Enable error reporting for PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection file
require 'db_mysqli.php'; // Ensure the correct path and filename

// Include the navbar (assuming your navbar is in a file like 'navbar.php')
include "../templates/navbar.php"; 

$search_results = [];
$search_term = '';

try {
    if (isset($_GET['search_term'])) {
        $search_term = $conn->real_escape_string($_GET['search_term']);
        
        // Query the database
        $query = "
            SELECT * FROM cards
            WHERE name LIKE '%$search_term%'
        ";
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

    <!-- Your navbar is already included -->

    <section class="section">
        <div class="container">
            <h1 class="title">Search Results</h1>

            <?php if (!empty($search_results)): ?>
                <table class="table is-striped is-fullwidth">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($search_results as $result): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($result['id']); ?></td>
                                <td><?php echo htmlspecialchars($result['name']); ?></td>
                                <td><?php echo htmlspecialchars($result['description']); ?></td>
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
