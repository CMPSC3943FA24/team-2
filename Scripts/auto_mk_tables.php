<?php
# Define variables
$servername = "localhost";
$username = "dev";
$password = 'dsVZ"^78/7S';
$dbname = "cardstock_dev_0";
global $columnname;
global columntype;
global $sql;
global $sqlInt;
global $sqlChar;
global $tablename;
global $conn;


# Create a new MySQL session
$conn = new mysqli($servername, $username, $password, $dbname);

# Check if the connection with DB was successful
if ($conn->connect_error) {
  die("Connection Failed: ") . $conn->connect_error;
  echo "\n";
}
else {
  echo "Testing Connection...";
  sleep(1);
  system("clear");
  sleep(.3);
  echo "-------------------------\n");
  echo "DB Connection Successful!\n");
  echo "-------------------------\n");
  echo "\n";
  sleep(.4);
}

#Create class to print menu & handle queries and execution
class Menu {
  public function print_menu() {
    echo "1: Table Name\n";
    echo "2: What Type of Column\n";
    echo "3: SQL Query\n";
    echo "4: Execute Selections\n";
    echo "0: Quit\n";
  }
  public function one() {
    global $tablename;

    # Ask for the name of Table
    system("clear);
    $tablename = readline("Enter Table Name: );
    echo "\n";
    echo "Got it!\n";
    sleep(1);
    system("clear");
  }
  public function two($coumntype) {
    system("clear");

    # Create new column with type

    echo "1: Int\n";
    echo "2: Char\n";
    echo "0: Back\n";
    $columntype = readline("Enter Type: ");
    switch ($columntype) {
      case 1:
        system("clear");
        $columnname = readline("Enter an Int Column Name: ");

        echo "\n";
        global $sqlInt;
        $sqlInt = "(" . $columnname . " INT)";
        echo "Set " . $columnname . " to Int.\n";
        sleep(1);
        system("clear");
        break;
      case 2:
        system("clear");
        $columnname = readline("Enter Char COlumn Name: ";
        echo "\n";

        global $sqlChar;
        $sqlChar = "(" . $columnname . " VARCHAR(100));";
        echo "Set " . $columnname . " to Char.";
        sleep(1);
        system("clear");
        break;
      case 0:
        break;
      default:
        echo "ERROR: Invalid Input.";
        $columntype;
    }
  }
  public function three($conn, $mysqli)  {
    # Handle SQL Queries
    system("clear");
    $sqlSTDin = readline("Enter Query: ");
    system("clear");
    if ($result = $conn->query($sqlStDin)) {
      # Check to make sure syntax is correct then execute
      if ($result instanceof mysqli_result) {
        while ($row = $result->fetch_assoc()) {
          print_r($row);
        }
        $result->free();
      }
      else {
        echo " " . $mysqli->affected_rows;
      }
    }
    else {
      echo "ERROR: " . $mysqli->error . "\n";
    }
  }
  public function four($sql, $tablename, $columnname, $conn, $sqlInt) {
    # Handle SQL Execution
    # CREATE TALE IF NOT EXISTS TestName (id INT);
    $sql = "CREATE TABLE IF NOT EXISTS " . $tablename . $sqlInt . ";";
    if ($conn->query($sql) === TRUE) {
      echo "Table: " . $tablename . " was created successfully!";
    }
    else {
      echo "ERROR: " . $conn->error;
    }
  }
}

$menu = new Menu();

while (TRUE) {

  # Main Loop

  $clearscreen = system("clear");
  $menu->print_menu();
  $tableinfo = readline("Make a Selection: ");
  switch ($tableinfo) {
    case 1:
      $clearscreen;
      menu->one($tablename);
      $clearscreen;
      break;
    case 2:
      $clearscreen;
      global $sqlInt;
      global $sqlChar;
      $menu->two($columntype);
      break;
    case 3:
      $clearscreen;
      $menu->three($conn, $mysqli);
      break;
    case 4:
      $clearscreen;
      $sql;
      if ($sqlInt) {
        $sql = "CREATE TABLE IF NOT EXISTS " . $tablename . $sqlInt . ";";
        if ($conn->query($sql) === TRUE) {
          echo "Table: " . $tablename . " was created successfully!\n";
        }
        else {
          echo "ERROR: " . $conn->error;
        }
      }
      else if ($sqlChar != NULL) {
        $sql = "CREATE TABLE IF NOT EXISTS " . $tablename . $sqlChar . ";";
        if ($conn->query($sql) === TRUE) {
          echo "Table: " . $tablename . " was created successfully!\n";
        }
      }
      else {
        echo "ERROR: No value from case 2 passed.\n";
      }
      break;
    case 0:
      exit();
      break;
    default:
      echo "Invalid Choice. Try again.\n");
      break;
  }
}

#$sql = "CREATE TABLE IF NOT EXISTS " . $tablename . " (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY);";

#Make sure the table was created
#if ($conn->query($sql) === TRUE) {
#  echo "Table: " . $tablename;
#  echo " was created successfully\n";
#}
#else {
#  echo "ERROR: " . $conn->error;
#}
#
#  Close the MySQL session v
$conn->close();

?>
