<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handling the username change
    if (isset($_POST['username']) && trim($_POST['username']) != "") {
        $username = trim($_POST['username']);
        
        // Check if the username is already taken
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        
        if ($count > 0) {
            $error = 'Username already taken.';
        } else {
            // Update the username
            if (isset($_SESSION['user_id'])) {
                $stmt = $conn->prepare("UPDATE users SET username = ? WHERE user_id = ?");
                $stmt->bind_param('si', $username, $_SESSION['user_id']);
                $stmt->execute();
                $stmt->close();
                $success = 'Username updated successfully.';
            } else {
                $error = 'User is not logged in.';
            }
        }
    }

    // Handling the name change
    if (isset($_POST['name']) && trim($_POST['name']) != "") {
        $name = trim($_POST['name']);
        
        // Update the name
        if (isset($_SESSION['user_id'])) {
            $stmt = $conn->prepare("UPDATE users SET name = ? WHERE user_id = ?");
            $stmt->bind_param('si', $name, $_SESSION['user_id']);
            $stmt->execute();
            $stmt->close();
            $success = 'Name updated successfully.';
        } else {
            $error = 'User is not logged in.';
        }
    }

    // Handling image upload with enhanced error handling
    if (isset($_FILES['profile_picture'])) {
        $file = $_FILES['profile_picture'];
        
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
                $max_size = 2 * 1024 * 1024; // 2MB
                if ($file['size'] > $max_size) {
                    $error = 'File size exceeds the 2MB limit.';
                } else {
                    // Set the target directory and file path
                    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    $new_image_path = $upload_dir . $_SESSION['user_id'] . '_profile.jpg';
                    $correct_image_path = "/uploads/" . $_SESSION['user_id'] . '_profile.jpg';

                    // Move the uploaded file
                    if (!move_uploaded_file($file['tmp_name'], $new_image_path)) {
                        $error = 'Failed to move the uploaded file.';
                    } else {
                        // Resize the image to 128x128
                        $image = imagecreatefromstring(file_get_contents($new_image_path));
                        if ($image !== false) {
                            $resized_image = imagescale($image, 128, 128);
                            imagejpeg($resized_image, $new_image_path);
                            imagedestroy($image);
                            imagedestroy($resized_image);

                            // Update the database with the new image path
                            if (isset($_SESSION['user_id'])) {
                                $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE user_id = ?");
                                $stmt->bind_param('si', $correct_image_path, $_SESSION['user_id']);
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

<?php include "../templates/navbar.php"; ?>

<!-- HTML Form with the file input and error display -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-half">
                <h1 class="title is-3 has-text-centered">Edit Profile</h1>

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

                <!-- Profile Edit Form -->
                <div class="box">
                    <form action="edit_profile.php" method="post" enctype="multipart/form-data">
                        
                        <!-- Username Field -->
                        <div class="field">
                            <label class="label">New Username</label>
                            <div class="control">
                                <input class="input" type="text" name="username">
                            </div>
                        </div>

                        <!-- Name Field -->
                        <div class="field">
                            <label class="label">New Name</label>
                            <div class="control">
                                <input class="input" type="text" name="name">
                            </div>
                        </div>

                        <!-- Profile Picture Upload -->
                        <div class="field">
                            <label class="label">Profile Picture</label>
                            <div class="file has-name is-fullwidth">
                                <label class="file-label">
                                    <input class="file-input" type="file" name="profile_picture" accept="image/*" id="profilePictureInput" required>
                                    <span class="file-cta">
                                        <span>Choose a file…</span>
                                    </span>
                                    <span class="file-name" id="fileNameDisplay">No file uploaded</span>
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="field">
                            <div class="control">
                                <button class="button is-primary is-fullwidth" type="submit">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Password Reset Link -->
                <div class="field has-text-centered">
                    <a href="/app/account_recovery.php" class="button is-link is-light">Reset Password</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Display selected file name
        document.getElementById('profilePictureInput').addEventListener('change', function() {
            const fileName = this.files.length ? this.files[0].name : 'No file uploaded';
            document.getElementById('fileNameDisplay').textContent = fileName;
        });
    </script>
</body>
</html>
