<?php
//display error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => false,
    'httponly' => true,
]);
session_start();

require 'config.php';

// Check for any database connection errors
if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}

$error = ''; // Initialize error message

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute query to fetch user data
    if ($stmt = $mysqli->prepare('SELECT * FROM users WHERE username = ?')) {
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Verify password
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];

            // Redirect to a protected page
            header('Location: ../index.php');
            exit();
        } else {
            $error = 'Invalid username or password.';
        }

        $stmt->close();
    } else {
        // Handle statement preparation error
        $error = 'Query preparation failed: ' . $mysqli->error;
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <section class="section">
        <div class="container">
            <div class="columns is-centered">
                <div class="column is-one-third">
                    <div class="box">
                        <h2 class="title has-text-centered">Login</h2>

                        <!-- Display error message if login fails -->
                        <?php if (!empty($error)): ?>
                            <div class="notification is-danger">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="field">
                                <label class="label" for="username">Username:</label>
                                <div class="control">
                                    <input class="input" type="text" name="username" id="username" required>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label" for="password">Password:</label>
                                <div class="control">
                                    <input class="input" type="password" name="password" id="password" required>
                                </div>
                            </div>

                            <div class="field">
                                <div class="control">
                                    <button class="button is-success is-fullwidth" type="submit">Login</button>
                                </div>
                            </div>
                        </form>

                        <div class="has-text-centered">
                            <a href="/app/signup.php">Don't have an account? Sign up</a><br>
                            <a href="/app/account_recovery.php">Forgot your password?</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
