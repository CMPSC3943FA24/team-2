<?php
// Database connection
require_once 'config.php';

$error = $success = '';  // Initialize error and success messages

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handling the username change
    if (isset($_POST['username'])) {
        $username = $_POST['username'];
        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
        
        if ($stmt->execute()) {
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();
            
            if ($count > 0) {
                $error = 'Username already taken.';
            } else {
                $stmt = $mysqli->prepare("UPDATE users SET username = ? WHERE user_id = ?");
                $stmt->bind_param('si', $username, $_SESSION['user_id']);
                if ($stmt->execute()) {
                    $success = 'Username updated successfully.';
                } else {
                    $error = 'Error updating username.';
                }
                $stmt->close();
            }
        } else {
            $error = 'Error checking username availability.';
        }
    }

    // Handling the name change
    if (isset($_POST['name'])) {
        $name = $_POST['name'];
        $stmt = $mysqli->prepare("UPDATE users SET name = ? WHERE user_id = ?");
        $stmt->bind_param('si', $name, $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            $success = 'Name updated successfully.';
        } else {
            $error = 'Error updating name.';
        }
        $stmt->close();
    }

    // Handling image upload and resizing
    if (isset($_FILES['profile_picture'])) {
        $file = $_FILES['profile_picture'];

        // Validate file size and type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if ($file['error'] == 0 && in_array($file['type'], $allowedTypes) && $file['size'] <= 2 * 1024 * 1024) {
            $image = imagecreatefromstring(file_get_contents($file['tmp_name']));
            if ($image) {
                // Resize and save the image
                $resized_image = imagescale($image, 128, 128);
                $new_image_path = $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $_SESSION['user_id'] . '_profile.jpg';
                
                if (imagejpeg($resized_image, $new_image_path)) {
                    imagedestroy($image);
                    imagedestroy($resized_image);
                    
                    // Update the database with the new profile picture path
                    $relative_path = '/uploads/' . $_SESSION['user_id'] . '_profile.jpg';
                    $stmt = $mysqli->prepare("UPDATE users SET profile_picture = ? WHERE user_id = ?");
                    $stmt->bind_param('si', $relative_path, $_SESSION['user_id']);
                    
                    if ($stmt->execute()) {
                        $success = 'Profile picture updated successfully.';
                    } else {
                        $error = 'Error updating profile picture in database.';
                    }
                    $stmt->close();
                } else {
                    $error = 'Error saving resized image.';
                }
            } else {
                $error = 'Error processing image file.';
            }
        } else {
            $error = 'Invalid file type or file too large (max 2MB).';
        }
    }
}
?>

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

                <?php if ($error): ?>
                    <div class="notification is-danger is-light">
                        <button class="delete"></button>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
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
                            <label class="label">Username</label>
                            <div class="control">
                                <input class="input" type="text" name="username" value="<?= htmlspecialchars($current_username) ?>">
                            </div>
                        </div>

                        <!-- Name Field -->
                        <div class="field">
                            <label class="label">Name</label>
                            <div class="control">
                                <input class="input" type="text" name="name" value="<?= htmlspecialchars($current_name) ?>">
                            </div>
                        </div>

                        <!-- Profile Picture Upload -->
                        <div class="field">
                            <label class="label">Profile Picture</label>
                            <div class="file has-name is-fullwidth">
                                <label class="file-label">
                                    <input class="file-input" type="file" name="profile_picture" accept="image/*">
                                    <span class="file-cta">
                                        <span>Choose a file…</span>
                                    </span>
                                    <span class="file-name">
                                        <?= isset($file['name']) ? htmlspecialchars($file['name']) : 'No file uploaded' ?>
                                    </span>
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
</body>
</html>
