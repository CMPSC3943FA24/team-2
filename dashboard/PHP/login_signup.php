<?php
include '../dbconnection.php';

global $username;
global $password;

class SignUp {
    private $conn;
    public function __construct($conn) {
        $this->conn = $conn;
    }
    public function getuserinfo($conn): void {
        session_start();
        $printAlert = false;
        $printError = false;
        $ifexists = false;
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST["username"];
            $password = $_POST["password"];
            if(strlen($password) < 8) {
                echo "Password must be at least 8 characters long.";
                exit();
            }
            $passhash = password_hash($password, PASSWORD_DEFAULT);
            $statement = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $statement->bind_param("ss", $username, $passhash);
            if($statement->execute()) {
                echo "Account Created!";
            }
            else {
                if($conn->errno == 1062) {
                    echo "User;name already exists. :(";
                }
                else {
                    echo "ERROR: " . $conn->error;
                }
            }
            $statement->close();
            $conn->close();
        }
    }
}

class SignIn {
    private $conn;
    public function __construct($conn) {
        $this->conn = $conn;
    }
    public function pushuserinfo($username, $password): string {
        $username = htmlspecialchars(trim($username));
        $password = htmlspecialchars(trim($password));
        $statement = $this->conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $statement->bind_param("s", $username);
        $statement->execute();
        $result = $statement->get_result();
        if($result->num_rows === 0) {
            $temp = "Username does not exist.";
            return $temp;
        }
        $user = $result->fetch_assoc();
        $statement->close();
        if(password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $temp2 = "Successfully Logged In!";
            return $temp2;
        }
        else {
            $temp3 = "Invalid Password.";
            return $temp3;
        }
    }
    public function isUserLoggedin() {
        session_start();
        return isset($_SESSION['user_id']);
    }
    public function userLogOut() {
        session_start();
        session_unset();
        session_destroy();
        $temp4 = "Successfully Logged Out.";
        return $temp4;
    }
}

?>
