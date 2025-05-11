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

// Handle new food item addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_food'])) {
    $fd_name = $_POST['fd_name'] ?? '';
    $fd_desc = $_POST['fd_desc'] ?? '';
    $fd_stat = $_POST['fd_stat'] ?? '';
    $fd_author = $username;

    // Handle image upload
    $fd_img = '';
    if (isset($_FILES['fd_img']) && $_FILES['fd_img']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../img/';
        $tmp_name = $_FILES['fd_img']['tmp_name'];
        $original_name = basename($_FILES['fd_img']['name']);
        $target_file = $upload_dir . $original_name;

        if (move_uploaded_file($tmp_name, $target_file)) {
            $fd_img = $original_name;
        } else {
            $message = "Failed to upload image.";
        }
    }

    if (!empty($fd_name) && !empty($fd_desc) && !empty($fd_stat) && !empty($fd_img)) {
        $stmt = $conn->prepare("INSERT INTO food (FD_NAME, FD_DESC, FD_STAT, FD_AUTHOR, FD_IMG) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $fd_name, $fd_desc, $fd_stat, $fd_author, $fd_img);
        if ($stmt->execute()) {
            $message = "New food item added successfully.";
        } else {
            $message = "Failed to add new food item.";
        }
        $stmt->close();
    } else {
        if (empty($message)) {
            $message = "Please fill all fields and upload an image.";
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
    <title>Add Food Item - <?php echo htmlspecialchars($username); ?></title>
    <link rel="stylesheet" href="../styles.css" />
    <link rel="stylesheet" href="addfood.css" />
</head>
<body>
    <h1>Add New Food Item</h1>
    <?php if ($message): ?>
        <p style="color: green;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="post" action="index.php" enctype="multipart/form-data">
        <label for="fd_name">Food Name:</label><br />
        <input type="text" id="fd_name" name="fd_name" required /><br /><br />

        <label for="fd_desc">Description:</label><br />
        <textarea id="fd_desc" name="fd_desc" rows="4" cols="50" required></textarea><br /><br />

        <label for="fd_stat">Status:</label><br />
        <select id="fd_stat" name="fd_stat"><br /><br />
            <option value="HALAL">Halal</option>
            <option value="HARAM">Haram</option>
        </select>

        <label for="fd_img">Image:</label><br />
        <input type="file" id="fd_img" name="fd_img" accept="image/*" required /><br /><br />

        <button type="submit" name="add_food">Add Food</button>
    </form>

    <p><a href="../profile.php">Back to Dashboard</a></p>
</body>
</html>
