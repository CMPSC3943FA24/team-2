<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start a new session if one is not already started
}
require '../db.php'; // Include your database connection

$error = ''; // Initialize error message

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the username already exists
    $query = $pdo->prepare('SELECT * FROM users WHERE username = :username');
    $query->execute(['username' => $username]);
    $existingUser = $query->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {
        $error = 'Username already exists. Please choose a different one.';
    } else {
        // Hash the password before storing it
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user into the database
        $query = $pdo->prepare('INSERT INTO users (username, password) VALUES (:username, :password)');
        $query->execute(['username' => $username, 'password' => $hashedPassword]);

        // Redirect or show success message
        echo 'Sign-up successful! You can now <a href="login.php">log in</a>.';
        exit(); // Prevent further processing
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Page</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <section class="section">
        <div class="container">
            <div class="columns is-centered">
                <div class="column is-one-third">
                    <div class="box">
                        <h2 class="title has-text-centered">Sign Up</h2>

                        <!-- Display error message if sign-up fails -->
                        <?php if (!empty($error)): ?>
                            <div class="notification is-danger">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
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
                                    <button class="button is-success is-fullwidth" type="submit">Sign Up</button>
                                </div>
                            </div>
                        </form>

                        <div class="has-text-centered">
                            <a href="login.php">Already have an account? Log in</a><br>
                            <a href="/app/account_recovery.php">Forgot your password?</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
