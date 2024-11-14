<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $set_id = $_POST['set_id'];
    $owner = $_POST['owner'];
    $number_owned = $_POST['number_owned'];
    $mana_cost = $_POST['mana_cost'] ?? null;
    $mana_type = $_POST['mana_type'] ?? null;
    $power = $_POST['power'] ?? null;
    $toughness = $_POST['toughness'] ?? null;
    $expansion = $_POST['expansion'] ?? null;
    $rarity = $_POST['rarity'] ?? null;

    // Insert into `cards` table
    $stmt = $conn->prepare("INSERT INTO cards (name, set_id, owner, number_owned) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('siii', $name, $set_id, $owner, $number_owned);
    $stmt->execute();
    $card_id = $stmt->insert_id; // Get the ID of the inserted card
    $stmt->close();

    // Insert into `magic_criteria` table
    $stmt = $conn->prepare("INSERT INTO magic_criteria (name_of_card, card_id, mana_cost, mana_type, power, toughness, expansion, rarity) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('siisssss', $name, $card_id, $mana_cost, $mana_type, $power, $toughness, $expansion, $rarity);
    $stmt->execute();
    $stmt->close();

    $success = 'Card and criteria added successfully!';
        
    // Handling image upload with enhanced error handling
    //what a freakin code block eh?
    if (isset($_FILES['card_picture'])) {
        $file = $_FILES['card_picture'];
        
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
            // Check if file type is valid
            $allowed_types = ['image/jpeg'];
            if (!in_array($file['type'], $allowed_types)) {
                $error = 'Invalid file type. Only JPG are allowed.';
            } else {
                // Check file size (for example, limit to 2MB)
                $max_size = 2 * 4096 * 4096; // 8MBish??
                if ($file['size'] > $max_size) {
                    $error = 'File size exceeds the 8MB limit.';
                } else {
                    // Set the target directory and file path
                    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/cards/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    $new_image_path = $upload_dir . $card_id . '.jpg';
                    $correct_image_path = "/uploads/card/" . $card_id . '.jpg';

                    // Move the uploaded file
                    if (!move_uploaded_file($file['tmp_name'], $new_image_path)) {
                        $error = 'Failed to move the uploaded file.';
                    } else {
                        // Resize the image to 490 × 684
                        $image = imagecreatefromstring(file_get_contents($new_image_path));
                        if ($image !== false) {
                            $resized_image = imagescale($image, 490, 684);
                            imagejpeg($resized_image, $new_image_path);
                            imagedestroy($image);
                            imagedestroy($resized_image);

                            // Update the database with the new image path
                            if (isset($_SESSION['user_id'])) {
                                $stmt = $conn->prepare("UPDATE cards SET images = ? WHERE card_id = ?");
                                $stmt->bind_param('si', $correct_image_path, $card_id);
                                $stmt->execute();
                                $stmt->close();
                                $success = 'Profile picture updated successfully.';
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

                        <!-- Owner -->
                        <div class="field">
                            <label class="label">Owner</label>
                            <div class="control">
                                <input class="input" type="number" name="owner" required>
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

                        <!-- Profile Picture Upload -->
                        <div class="field">
                            <label class="label">Card Picture</label>
                            <div class="file has-name is-fullwidth">
                                <label class="file-label">
                                    <input class="file-input" type="file" name="card_picture" accept="image/*" id="cardPictureInput" required>
                                    <span class="file-cta">
                                        <span>Choose a file…</span>
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


                        <!-- Submit Button -->
                        <div class="field">
                            <div class="control">
                                <button class="button is-primary is-fullwidth" type="submit">Add Card</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!--Preview Card Image-->
            <div class="column is-half">
                <img class="image" id="cardImage" style="max-width: 100%; height: auto;">
            </div>
            <script>
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
    </div>
</body>
</html>
