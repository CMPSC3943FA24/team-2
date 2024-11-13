<?php
// Database connection
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handling the username change
    if (isset($_POST['username'])) {
        $username = $_POST['username'];
        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        
        if ($count > 0) {
            $error = 'Username already taken.';
        } else {
            $stmt = $mysqli->prepare("UPDATE users SET username = ? WHERE user_id = ?");
            $stmt->bind_param('si', $username, $_SESSION['user_id']);
            $stmt->execute();
            $stmt->close();
            $success = 'Username updated successfully.';
        }
    }

    // Handling the name change
    if (isset($_POST['name'])) {
        $name = $_POST['name'];
        $stmt = $mysqli->prepare("UPDATE users SET name = ? WHERE user_id = ?");
        $stmt->bind_param('si', $name, $_SESSION['user_id']);
        $stmt->execute();
        $stmt->close();
        $success = 'Name updated successfully.';
    }

    // Handling image upload and resizing
    if (isset($_FILES['profile_picture'])) {
        $file = $_FILES['profile_picture'];
        if ($file['error'] == 0) {
            $image = imagecreatefromstring(file_get_contents($file['tmp_name']));
            $resized_image = imagescale($image, 128, 128);
            $new_image_path = '/uploads/' . $_SESSION['user_id'] . '_profile.jpg';
            imagejpeg($resized_image, $new_image_path);
            imagedestroy($image);
            imagedestroy($resized_image);

            $stmt = $mysqli->prepare("UPDATE users SET profile_picture = ? WHERE user_id = ?");
            $stmt->bind_param('si', $new_image_path, $_SESSION['user_id']);
            $stmt->execute();
            $stmt->close();
            $success = 'Profile picture updated successfully.';
        } else {
            $error = 'Error uploading image.';
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
                            <label class="label">Username</label>
                            <div class="control has-icons-left">
                                <input class="input" type="text" name="username" value="<?= htmlspecialchars($current_username) ?>">
                                <span class="icon is-left">
                                    <i class="fas fa-user"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Name Field -->
                        <div class="field">
                            <label class="label">Name</label>
                            <div class="control has-icons-left">
                                <input class="input" type="text" name="name" value="<?= htmlspecialchars($current_name) ?>">
                                <span class="icon is-left">
                                    <i class="fas fa-id-card"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Profile Picture Upload -->
                        <div class="field">
                            <label class="label">Profile Picture</label>
                            <div class="file has-name is-fullwidth">
                                <label class="file-label">
                                    <input class="file-input" type="file" name="profile_picture" accept="image/*">
                                    <span class="file-cta">
                                        <span class="icon">
                                            <i class="fas fa-upload"></i>
                                        </span>
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

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
