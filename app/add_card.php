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
    // User is not logged in, redirect to the login page
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $set_id = $_POST['set_id'];
    $owner = $_SESSION['user_id'];
    $number_owned = $_POST['number_owned'];
    $mana_cost = $_POST['mana_cost'] ?? null;
    $mana_value = $_POST['mana_value'] ?? null;
    $mana_type = $_POST['mana_type'] ?? null;
    $power = $_POST['power'] ?? null;
    $toughness = $_POST['toughness'] ?? null;
    $expansion = $_POST['expansion'] ?? null;
    $rarity = $_POST['rarity'] ?? null;
    $card_number = $_POST['card_number'] ?? null;
    $artist = $_POST['artist'] ?? null;

    // Insert into `cards` table
    $stmt = $conn->prepare("INSERT INTO cards (name, set_id, owner, number_owned) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('siii', $name, $set_id, $owner, $number_owned);
    $stmt->execute();
    $card_id = $stmt->insert_id; // Get the ID of the inserted card
    $stmt->close();

    // Insert into `magic_criteria` table
    $stmt = $conn->prepare("INSERT INTO magic_criteria (name_of_card, card_id, mana_cost, mana_type, mana_value power, toughness, expansion, rarity, card_number, artist) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('siisssss', $name, $card_id, $mana_cost, $mana_type, $mana_value, $power, $toughness, $expansion, $rarity, $card_number, $artist);
    $stmt->execute();
    $stmt->close();

    $success = 'Card and criteria added successfully!';
        
  // Handling image upload: now with error handling
    if (isset($_FILES['card_image'])) {
        $file = $_FILES['card_image'];
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            switch ($file['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $error = 'The uploaded file exceeds the maximum allowed size.';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $error = 'The uploaded file was only partially uploaded.';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $error = 'No file was uploaded.';
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $error = 'Missing a temporary folder on the server.';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $error = 'Failed to write the uploaded file to disk.';
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $error = 'A PHP extension stopped the file upload.';
                    break;
                default:
                    $error = 'Unknown error occurred during file upload.';
                    break;
            }
        } else {
            // Validate file type
            $allowed_types = ['image/jpeg', 'image/pjpeg'];
            if (!in_array($file['type'], $allowed_types)) {
                $error = 'Invalid file type. Only JPEG images are allowed.';
            } else {
                // Validate file size (limit to 8MB)
                $max_size = 8 * 1024 * 1024;
                if ($file['size'] > $max_size) {
                    $error = 'File size exceeds the 8MB limit.';
                } else {
                    // Set target directory and file path
                    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/cards/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    $new_image_path = $upload_dir . $card_id . '.jpg';
                    $correct_image_path = "/uploads/cards/" . $card_id . '.jpg';

                    // Move the uploaded file
                    if (!move_uploaded_file($file['tmp_name'], $new_image_path)) {
                        $error = 'Failed to move the uploaded file.';
                    } else {
                        // Resize the image to 490x684
                        $image = imagecreatefromjpeg($new_image_path);
                        if ($image !== false) {
                            $resized_image = imagescale($image, 490, 684);
                            if ($resized_image !== false) {
                                imagejpeg($resized_image, $new_image_path);
                                imagedestroy($image);
                                imagedestroy($resized_image);

                                // Update database with the new image path
                                $stmt = $conn->prepare("UPDATE cards SET images = ? WHERE card_id = ?");
                                $stmt->bind_param('si', $correct_image_path, $card_id);
                                $stmt->execute();
                                $stmt->close();
                                $success = 'Card image updated successfully.';
                            } else {
                                $error = 'Failed to resize the uploaded image.';
                            }
                        } else {
                            $error = 'Failed to process the uploaded image.';
                        }
                    }
                }
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Card</title>
    <link rel="stylesheet" href="../styles.css">
</head>

<?php include "../templates/navbar.php"; ?>

<body>
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-half">
                <h1 class="title is-3 has-text-centered">Add New Card</h1>

                <?php if (isset($error)): ?>
                    <div class="notification is-danger is-light">
                        <button class="delete"></button>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($success)): ?>
                    <div class="notification is-success is-light">
                        <button class="delete"></button>
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <!-- New Card Input Form -->
                <div class="box">
                    <form action="add_card.php" method="post" enctype="multipart/form-data">
                        
                        <!-- Card Name -->
                        <div class="field">
                            <label class="label">Card Name</label>
                            <div class="control">
                                <input class="input" type="text" name="name" required>
                            </div>
                        </div>

                        <!-- Set ID -->
                        <div class="field">
                            <label class="label">Set ID</label>
                            <div class="control">
                                <input class="input" type="number" name="set_id" required>
                            </div>
                        </div>

                        <!-- Number Owned -->
                        <div class="field">
                            <label class="label">Number Owned</label>
                            <div class="control">
                                <input class="input" type="number" name="number_owned" value="1" required>
                            </div>
                        </div>

                        <!-- Mana Cost -->
                        <div class="field">
                            <label class="label">Mana Cost</label>
                            <div class="control">
                                <input class="input" type="number" name="mana_cost">
                            </div>
                        </div>

                        <!-- Mana Type -->
                        <div class="field">
                            <label class="label">Mana Type</label>
                            <div class="control">
                                <input class="input" type="text" name="mana_type">
                            </div>
                        </div>

                        <!-- Mana Value -->
                        <div class="field">
                            <label class="label">Mana Value</label>
                            <div class="control">
                                <input class="input" type="number" name="mana_value">
                            </div>
                        </div>

                        <!-- Power -->
                        <div class="field">
                            <label class="label">Power</label>
                            <div class="control">
                                <input class="input" type="number" name="power">
                            </div>
                        </div>

                        <!-- Toughness -->
                        <div class="field">
                            <label class="label">Toughness</label>
                            <div class="control">
                                <input class="input" type="number" name="toughness">
                            </div>
                        </div>

                        <!-- Expansion -->
                        <div class="field">
                            <label class="label">Expansion</label>
                            <div class="control">
                                <input class="input" type="text" name="expansion">
                            </div>
                        </div>

                        <!-- Rarity -->
                        <div class="field">
                            <label class="label">Rarity</label>
                            <div class="control">
                                <input class="input" type="text" name="rarity">
                            </div>
                        </div>

                        <!-- Card Number -->
                        <div class="field">
                            <label class="label">Card Number</label>
                            <div class="control">
                                <input class="input" type="number" name="card_number">
                            </div>
                        </div>

                        <!-- Artist -->
                        <div class="field">
                            <label class="label">Artist</label>
                            <div class="control">
                                <input class="input" type="text" name="artist">
                            </div>
                        </div>

                        <!-- Image File Upload -->
                        <div class="field">
                            <label class="label">Card Image</label>
                            <div class="file has-name is-fullwidth">
                                <label class="file-label">
                                    <input class="file-input" type="file" name="card_image" accept="image/*" id="cardImageInput" required>
                                    <span class="file-cta">
                                        <span>Choose an image...</span>
                                    </span>
                                    <span class="file-name" id="fileNameDisplay">No file uploaded</span>
                                </label>
                            </div>
                        </div>

                        <script>
                            // Display selected file name
                            document.getElementById('cardPictureInput').addEventListener('change', function() {
                                const fileName = this.files.length ? this.files[0].name : 'No file uploaded';
                                document.getElementById('fileNameDisplay').textContent = fileName;
                            });
                        </script>

                            <!--Preview Card Image-->
                            <div>
                                <img class="image" id="cardImage" style="max-width: 25%; height: auto;">
                            </div>
                            <script>
                                //Display Image Preview
                                document.getElementById('cardImageInput').addEventListener('change', function(event) {
                                    const file = event.target.files[0];
                                    if (file) {
                                        const reader = new FileReader();
                                        
                                        reader.onload = function(e) {
                                            document.getElementById('cardImage').src = e.target.result;
                                        };

                                        reader.readAsDataURL(file);
                                        document.getElementById('fileNameDisplay').textContent = file.name;
                                    }
                                });
                            </script>
                        </div>

                        <!-- Submit Button -->
                        <div class="field">
                            <div class="control">
                                <button class="button is-primary is-fullwidth" type="submit">Add Card</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
    </div>
</body>
</html>
