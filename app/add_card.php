<?php
require_once 'config.php';

echo "1: Script started.<br>";

// Start session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
    echo "2: Session started.<br>";
}

// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);
echo "3: Error reporting enabled.<br>";

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "4: User not logged in. Redirecting.<br>";
    header('Location: login.php');
    exit();
}

$error = '';
$success = '';
$image_upload_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "5: Processing POST request.<br>";

    // Validate required fields
    $name = trim($_POST['name'] ?? '');
    $number_owned = filter_var($_POST['number_owned'] ?? '', FILTER_VALIDATE_INT);
    if (!$name || !$number_owned) {
        $error = 'Please provide a valid card name and number owned.';
        echo "6: Validation error: $error<br>";
    }

    if (!$error) {
        echo "7: Validation passed.<br>";

        // Optional fields
        $mana_cost = $_POST['mana_cost'] ?? null;
        $mana_value = $_POST['mana_value'] ?? null;
        $mana_type = $_POST['mana_type'] ?? null;
        $power = $_POST['power'] ?? null;
        $toughness = $_POST['toughness'] ?? null;
        $expansion = $_POST['expansion'] ?? null;
        $rarity = $_POST['rarity'] ?? null;
        $card_number = $_POST['card_number'] ?? null;
        $artist = $_POST['artist'] ?? null;
        $owner = $_SESSION['user_id'];
        $set_id = 1; // Assuming a default set_id

        echo "8: Optional fields initialized.<br>";

        // Handle image upload
        if (isset($_FILES['card_image'])) {
            echo "9: Handling image upload.<br>";
            $file = $_FILES['card_image'];

            if ($file['error'] === UPLOAD_ERR_OK) {
                echo "10: File upload successful.<br>";
                $allowed_types = ['image/jpeg', 'image/pjpeg'];
                $max_size = 8 * 1024 * 1024;

                if (!in_array($file['type'], $allowed_types)) {
                    $error = 'Invalid file type. Only JPEG images are allowed.';
                    echo "11: $error<br>";
                } elseif ($file['size'] > $max_size) {
                    $error = 'File size exceeds the 8MB limit.';
                    echo "12: $error<br>";
                } else {
                    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/cards/';
                    if (!is_dir($upload_dir)) {
                        if (!mkdir($upload_dir, 0755, true)) {
                            $error = 'Failed to create upload directory.';
                            echo "13: $error<br>";
                        }
                    }

                    if (!$error) {
                        echo "14: Upload directory confirmed.<br>";
                        $temp_id = uniqid();
                        $new_image_path = $upload_dir . $temp_id . '.jpg';
                        $correct_image_path = "/uploads/cards/" . $temp_id . '.jpg';

                        if (!move_uploaded_file($file['tmp_name'], $new_image_path)) {
                            $error = 'Failed to move the uploaded file.';
                            echo "15: $error<br>";
                        } else {
                            $image = imagecreatefromjpeg($new_image_path);
                            if ($image) {
                                $resized_image = imagescale($image, 490, 684);
                                if ($resized_image) {
                                    imagejpeg($resized_image, $new_image_path);
                                    imagedestroy($image);
                                    imagedestroy($resized_image);
                                    $image_upload_success = true;
                                    echo "16: Image resized and saved.<br>";
                                } else {
                                    $error = 'Failed to resize the uploaded image.';
                                    echo "17: $error<br>";
                                }
                            } else {
                                $error = 'Failed to process the uploaded image.';
                                echo "18: $error<br>";
                            }
                        }
                    }
                }
            } else {
                $error = 'Error uploading image. Error code: ' . $file['error'];
                echo "19: $error<br>";
            }
        }

        // Insert data into database if no errors
        if (!$error && $image_upload_success) {
            try {
                echo "20: Preparing database transaction.<br>";
                $conn->begin_transaction();

                // Insert into `cards` table
                $stmt = $conn->prepare("INSERT INTO cards (name, set_id, owner, number_owned) VALUES (?, ?, ?, ?)");
                if (!$stmt) {
                    throw new mysqli_sql_exception("Prepare statement failed: " . $conn->error);
                }
                $stmt->bind_param('siii', $name, $set_id, $owner, $number_owned);
                $stmt->execute();
                $card_id = $stmt->insert_id;
                $stmt->close();
                echo "21: Card inserted with ID $card_id.<br>";

                // Insert into `magic_criteria` table
                $stmt = $conn->prepare("INSERT INTO magic_criteria (name_of_card, card_id, mana_cost, mana_type, mana_value, power, toughness, expansion, rarity, card_number, artist) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                if (!$stmt) {
                    throw new mysqli_sql_exception("Prepare statement failed: " . $conn->error);
                }
                $stmt->bind_param('siisiiissis', $name, $card_id, $mana_cost, $mana_type, $mana_value, $power, $toughness, $expansion, $rarity, $card_number, $artist);
                $stmt->execute();
                $stmt->close();
                echo "22: Magic criteria inserted.<br>";

                // Update image path in database
                $stmt = $conn->prepare("UPDATE cards SET images = ? WHERE card_id = ?");
                if (!$stmt) {
                    throw new mysqli_sql_exception("Prepare statement failed: " . $conn->error);
                }
                $stmt->bind_param('si', $correct_image_path, $card_id);
                $stmt->execute();
                $stmt->close();
                echo "23: Image path updated in database.<br>";

                $conn->commit();
                $success = 'Card added successfully!';
                echo "24: Transaction committed.<br>";
            } catch (mysqli_sql_exception $e) {
                $conn->rollback();
                $error = 'Database error: ' . $e->getMessage();
                echo "25: Transaction rolled back. $error<br>";
                if (file_exists($new_image_path)) {
                    unlink($new_image_path);
                }
            }
        }
    }
}
echo "26: Script finished.<br>";
?>
