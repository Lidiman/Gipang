<?php
session_start();

if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true || !isset($_SESSION['username'])) {
    header("Location: ../User/login.php");
    exit();
}

$host = "localhost";
$user = "root";
$password = "";
$database = "gipang";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $new_username = trim($_POST['new_username'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    $profile_pic = $_FILES['profile_pic'] ?? null;
    if (empty($new_username)) {
        $message = "Username cannot be empty.";
    } else {
        // Check if new username is already taken (and different from current)
        if ($new_username !== $username) {
            $stmt_check = $conn->prepare("SELECT US_NAME FROM user WHERE US_NAME = ?");
            $stmt_check->bind_param("s", $new_username);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            if ($result_check->num_rows > 0) {
                $message = "Username already taken. Please choose another.";
                $stmt_check->close();
            } else {
                $stmt_check->close();
                // Handle profile picture upload
                $img_id = null;
                if ($profile_pic && $profile_pic['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = '../../ADMIN/img/profile/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    $tmp_name = $profile_pic['tmp_name'];
                    $original_name = basename($profile_pic['name']);
                    $target_file = $upload_dir . uniqid() . '_' . $original_name;
                if (move_uploaded_file($tmp_name, $target_file)) {
                    // Delete old image file and record if exists
                    $stmt_old_img = $conn->prepare("SELECT IMG FROM user WHERE US_NAME = ?");
                    $stmt_old_img->bind_param("s", $username);
                    $stmt_old_img->execute();
                    $result_old_img = $stmt_old_img->get_result();
                    $old_img_id = null;
                    if ($row = $result_old_img->fetch_assoc()) {
                        $old_img_id = $row['IMG'];
                    }
                    $stmt_old_img->close();

                    if ($old_img_id) {
                        $stmt_img_path = $conn->prepare("SELECT IMG_DIR FROM images WHERE IMG_ID = ?");
                        $stmt_img_path->bind_param("i", $old_img_id);
                        $stmt_img_path->execute();
                        $result_img_path = $stmt_img_path->get_result();
                        if ($img_row = $result_img_path->fetch_assoc()) {
                            $old_img_path = '../../' . $img_row['IMG_DIR'];
                            if (file_exists($old_img_path)) {
                                error_log("Deleting old profile image: " . $old_img_path);
                                if (!unlink($old_img_path)) {
                                    error_log("Failed to delete old profile image: " . $old_img_path);
                                }
                            } else {
                                error_log("Old profile image file does not exist: " . $old_img_path);
                            }
                        }
                        $stmt_img_path->close();

                        $stmt_del_img = $conn->prepare("DELETE FROM images WHERE IMG_ID = ?");
                        $stmt_del_img->bind_param("i", $old_img_id);
                        if (!$stmt_del_img->execute()) {
                            error_log("Failed to delete old image record with IMG_ID: " . $old_img_id);
                        }
                        $stmt_del_img->close();
                    }

                    // Insert into images table
                    $img_dir = 'ADMIN/img/profile/' . basename($target_file);
                    if (strpos($img_dir, 'ADMIN/img/profile/') !== 0) {
                        $img_dir = '../../ADMIN/img/profile/' . $img_dir;
                    }
                    $stmt_img = $conn->prepare("INSERT INTO images (IMG_DIR) VALUES (?)");
                    $stmt_img->bind_param("s", $img_dir);
                    if ($stmt_img->execute()) {
                        $img_id = $stmt_img->insert_id;
                    }
                    $stmt_img->close();
                }
                }
                // Update username, password, and image if provided
                if (!empty($new_password) && $img_id) {
                    $stmt = $conn->prepare("UPDATE user SET US_NAME = ?, US_PW = ?, IMG = ? WHERE US_NAME = ?");
                    if (!$stmt) {
                        die("Prepare failed: " . $conn->error);
                    }
                    $stmt->bind_param("ssis", $new_username, $new_password, $img_id, $username);
                } else if (!empty($new_password)) {
                    $stmt = $conn->prepare("UPDATE user SET US_NAME = ?, US_PW = ? WHERE US_NAME = ?");
                    if (!$stmt) {
                        die("Prepare failed: " . $conn->error);
                    }
                    $stmt->bind_param("sss", $new_username, $new_password, $username);
                } else if ($img_id) {
                    $stmt = $conn->prepare("UPDATE user SET US_NAME = ?, IMG = ? WHERE US_NAME = ?");
                    if (!$stmt) {
                        die("Prepare failed: " . $conn->error);
                    }
                    $stmt->bind_param("sis", $new_username, $img_id, $username);
                } else {
                    $stmt = $conn->prepare("UPDATE user SET US_NAME = ? WHERE US_NAME = ?");
                    if (!$stmt) {
                        die("Prepare failed: " . $conn->error);
                    }
                    $stmt->bind_param("ss", $new_username, $username);
                }
                if ($stmt->execute()) {
                    $_SESSION['username'] = $new_username;
                    $username = $new_username;
                    $message = "Username updated successfully.";
                } else {
                    $message = "Failed to update username.";
                }
                $stmt->close();
            }
        } else {
            // Username unchanged, update password and/or image if provided
            $img_id = null;
            if ($profile_pic && $profile_pic['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../../ADMIN/IMG/profile/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                $tmp_name = $profile_pic['tmp_name'];
                $original_name = basename($profile_pic['name']);
                $target_file = $upload_dir . uniqid() . '_' . $original_name;
                if (move_uploaded_file($tmp_name, $target_file)) {
                    $img_dir = '/img/profile/' . basename($target_file);
                    $stmt_img = $conn->prepare("INSERT INTO images (IMG_DIR) VALUES (?)");
                    $stmt_img->bind_param("s", $img_dir);
                    if ($stmt_img->execute()) {
                        $img_id = $stmt_img->insert_id;
                    }
                    $stmt_img->close();
                }
            }
            if (!empty($new_password) && $img_id) {
                $stmt = $conn->prepare("UPDATE user SET US_PW = ?, IMG = ? WHERE US_NAME = ?");
                if (!$stmt) {
                    die("Prepare failed: " . $conn->error);
                }
                $stmt->bind_param("sis", $new_password, $img_id, $username);
            } else if (!empty($new_password)) {
                $stmt = $conn->prepare("UPDATE user SET US_PW = ? WHERE US_NAME = ?");
                if (!$stmt) {
                    die("Prepare failed: " . $conn->error);
                }
                $stmt->bind_param("ss", $new_password, $username);
            } else if ($img_id) {
                $stmt = $conn->prepare("UPDATE user SET IMG = ? WHERE US_NAME = ?");
                if (!$stmt) {
                    die("Prepare failed: " . $conn->error);
                }
                $stmt->bind_param("is", $img_id, $username);
            } else {
                $message = "No changes made.";
            }
            if (isset($stmt) && $stmt->execute()) {
                $message = "Profile updated successfully.";
            } else if (isset($stmt)) {
                $message = "Failed to update profile.";
            }
            if (isset($stmt)) {
                $stmt->close();
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Profile - <?php echo htmlspecialchars($username); ?></title>
    <link rel="stylesheet" href="../styles.css" />
</head>
<body>
    <h1>Edit Profile for <?php echo htmlspecialchars($username); ?></h1>
    <?php if ($message): ?>
        <p style="color: green;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

<form method="post" action="index.php" enctype="multipart/form-data">
        <label for="new_username">New Username:</label><br />
        <input type="text" id="new_username" name="new_username" value="<?php echo htmlspecialchars($username); ?>" required /><br /><br />
        <label for="new_password">New Password:</label><br />
        <input type="password" id="new_password" name="new_password" /><br /><br />
        <label for="profile_pic">Profile Picture:</label><br />
        <input type="file" id="profile_pic" name="profile_pic" accept="image/*" /><br /><br />
        <button type="submit" name="update_user">Update Profile</button>
    </form>

    <p><a href="../profile.php">Back to Dashboard</a></p>
</body>
</html>
