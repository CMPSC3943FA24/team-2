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

$image_upload_success = false;
$error = NULL;

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
                        $temp_id = uniqid();
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
                            <label class="label">Power</label> <!-- CAN BE EMPTY - FIX -->
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
                            <div class="is-flex is-justify-content-center">
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
