<?php
require_once 'config.php';

// Start session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$error = '';
$success = '';
$image_upload_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    $name = trim($_POST['name'] ?? '');
    $number_owned = filter_var($_POST['number_owned'] ?? '', FILTER_VALIDATE_INT);
    if (!$name || !$number_owned) {
        $error = 'Please provide a valid card name and number owned.';
    }

    if (!$error) {
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

        // Handle image upload
        if (isset($_FILES['card_image'])) {
            $file = $_FILES['card_image'];

            if ($file['error'] === UPLOAD_ERR_OK) {
                $allowed_types = ['image/jpeg', 'image/pjpeg'];
                $max_size = 8 * 1024 * 1024;

                if (!in_array($file['type'], $allowed_types)) {
                    $error = 'Invalid file type. Only JPEG images are allowed.';
                } elseif ($file['size'] > $max_size) {
                    $error = 'File size exceeds the 8MB limit.';
                } else {
                    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/cards/';
                    if (!is_dir($upload_dir)) {
                        if (!mkdir($upload_dir, 0755, true)) {
                            $error = 'Failed to create upload directory.';
                        }
                    }

                    if (!$error) {
                        $temp_id = uniqid(); // Use a temporary ID before database insertion
                        $new_image_path = $upload_dir . $temp_id . '.jpg';
                        $correct_image_path = "/uploads/cards/" . $temp_id . '.jpg';

                        if (!move_uploaded_file($file['tmp_name'], $new_image_path)) {
                            $error = 'Failed to move the uploaded file.';
                        } else {
                            $image = imagecreatefromjpeg($new_image_path);
                            if ($image) {
                                $resized_image = imagescale($image, 490, 684);
                                if ($resized_image) {
                                    imagejpeg($resized_image, $new_image_path);
                                    imagedestroy($image);
                                    imagedestroy($resized_image);
                                    $image_upload_success = true;
                                } else {
                                    $error = 'Failed to resize the uploaded image.';
                                }
                            } else {
                                $error = 'Failed to process the uploaded image.';
                            }
                        }
                    }
                }
            } else {
                $error = 'Error uploading image. Error code: ' . $file['error'];
            }
        }

        // Insert data into database if no errors
        if (!$error && $image_upload_success) {
            try {
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

                // Insert into `magic_criteria` table
                $stmt = $conn->prepare("INSERT INTO magic_criteria (name_of_card, card_id, mana_cost, mana_type, mana_value, power, toughness, expansion, rarity, card_number, artist) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                if (!$stmt) {
                    throw new mysqli_sql_exception("Prepare statement failed: " . $conn->error);
                }
                $stmt->bind_param('siisiiissis', $name, $card_id, $mana_cost, $mana_type, $mana_value, $power, $toughness, $expansion, $rarity, $card_number, $artist);
                $stmt->execute();
                $stmt->close();

                // Update image path in database
                $stmt = $conn->prepare("UPDATE cards SET images = ? WHERE card_id = ?");
                if (!$stmt) {
                    throw new mysqli_sql_exception("Prepare statement failed: " . $conn->error);
                }
                $stmt->bind_param('si', $correct_image_path, $card_id);
                $stmt->execute();
                $stmt->close();

                $conn->commit();
                $success = 'Card added successfully!';
            } catch (mysqli_sql_exception $e) {
                $conn->rollback();
                $error = 'Database error: ' . $e->getMessage();
                if (file_exists($new_image_path)) {
                    unlink($new_image_path);
                }
            }
        }
    }
}
?>
