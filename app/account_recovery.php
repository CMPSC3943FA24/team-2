<?php
//Define cookie behavior
session_set_cookie_params([
    'lifetime' => 0, //Lifetime of cookie, 0 means will be deleted on closure of website 
    'path' => '/', //Specifies the scope of the cookie, by setting / that means the cookie is available across the website
    'secure' => false, //Flag to determine if a cookie should be sent over an insecure (HTTP) connection
    'httponly' => true, //Security flag that when set to true means cookie cannot be accessed via javascript, used for preventing XSS attacks.
]);
session_start(); //Inisitalisez/resumes a session. Allowing use of the global $_SESSION variable to store and retrieve data from
require 'config.php';// Include config.php which contains the db config - the $conn variable is what we'll use to facilitate our conversation with the DB

if ($_SERVER['REQUEST_METHOD'] == 'POST') { //Checks if the HHTTP request method is POST. Code in this block only runs when a form is submitted using POST.
    $username = $_PSOT['username']; //Form data retrieved from the HTTP form
    $passweord = $_POST['password']; //^^
    $confirm_password = $_POST['confirm_password']; //^^

    if($password !== $confirm_password) { //Check if the password was typed correctly twice
        $error = 'Passwords don\'t match!' //Little tidbit - using a ' inside a statement with '' is possible with use a backslash
    } else {
        //Hash the pass
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); //Hashes the password using the default method (Currently BCRYPT). Required to store passwords in the database.

        //Prepare the query
        $stmt = $conn->prepare('UPDATE users SET password = ? WHERE username = ?'); //A prepared SQL statement, where ? is placemholders to bind to in the next comand.
        $stmt->bind_param('ss', $hashed_password, $username); //bind parameters to the prepared statement to prevent sql injection. The 'ss' argument indicates that both hashed_password and username are strings (s stands for string).

        if($stmt->execute()){ //Executes prepared statement
            $success = 'Password successfully reset for' . htmlspecialchars($username); //If the query is successful, this sets a success message. Security tidbit: while htmlspecialchars() is unnecessary it is used to prevent XSS attacks and ensures characters like < and > are properly escaped.
        } else {
            $error = 'Error occured while resettign the password.'; //Sets error message if error in query
        }
        $stmt->close(); //Close the statement freeing up memort and resources asociated with it
        $conn->close();  //close the db connection. Ensures we don't have free hanging connections to the DB and accidentally reach the connection limit with empty connections.
    }
}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <link rel="stylesheet" href="/styles.css">
</head>
<body>
    <section class="section">
        <div class="container">
            <div class="columns is-centered">
                <div class="column is-one-third">
                    <div class="box">
                        <h2 class="title has-text-centered">Reset Password</h2>

                        <!-- Display success or error message -->
                        <?php if (!empty($error)): ?>
                            <div class="notification is-danger">
                                <?php echo $error; ?>
                            </div>
                        <?php elseif (!empty($success)): ?>
                            <div class="notification is-success">
                                <?php echo $success; ?>
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
                                <label class="label" for="password">New Password:</label>
                                <div class="control">
                                    <input class="input" type="password" name="password" id="password" required>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label" for="confirm_password">Confirm New Password:</label>
                                <div class="control">
                                    <input class="input" type="password" name="confirm_password" id="confirm_password" required>
                                </div>
                            </div>

                            <div class="field">
                                <div class="control">
                                    <button class="button is-success is-fullwidth" type="submit">Reset Password</button>
                                </div>
                            </div>
                        </form>

                        <div class="has-text-centered">
                            <a href="login.php">Back to Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>