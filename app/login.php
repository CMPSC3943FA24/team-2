<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => false,
    'httponly' => true,
]);
session_start();
require '../db.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch user data from the database
    $query = $pdo->prepare('SELECT * FROM users WHERE username = :username');
    $query->execute(['username' => $username]);
    $user = $query->fetch(PDO::FETCH_ASSOC);
    
    // Debug output
    var_dump($user); // Check user data

    // Verify password
    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        // Debug output for session variables
        echo 'Session ID: ' . session_id();
        echo 'User ID: ' . $_SESSION['user_id'];
        echo 'Username: ' . $_SESSION['username'];

        // Redirect to a protected page
        header('Location: ../index.php');
        exit();
    } else {
        echo 'Invalid username or password.';
    }
}
?>
