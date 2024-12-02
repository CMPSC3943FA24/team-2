<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start a new session if one is not already started
}
require 'config.php'; // Include your database connection

$error = ''; // Initialize error message

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $name = $_POST['name'];

    // Check if the username already exists
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s',$username);
    $stmt->execute();
    $result = $stmt->get_result();
    $existingUser = $result->fetch_assoc();

    if($existingUser){
        $error = "Username already exists. Please choose a different one.";
    } else {
        //Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        //Insert user into the database
        $query = "INSERT INTO users (username, password, name) VALUES (?,?,?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sss', $username, $hashedPassword, $name);
        $stmt->execute();

        //Set success message in session and redirect to login
        $_SESSION['signup_success'] = 'Sign up successful! You can now log in.';
        header('Location: login.php');
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
                                <label class="label" for="name">Name:</label>
                                <div class="control">
                                    <input class="input" type="text" name="name" id="name" required>
                                </div>
                            </div>
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
