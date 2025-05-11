<?php
session_start();

if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

$host = "localhost";
$user = "root";
$password = "";
$database = "gipang";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user info with profile picture
$stmt = $conn->prepare("SELECT u.US_NAME, i.IMG_DIR FROM user u LEFT JOIN images i ON u.IMG = i.IMG_ID WHERE u.US_NAME = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$profile_pic = $user_data['IMG_DIR'] ?? null;

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>User Dashboard - <?php echo htmlspecialchars($username); ?></title>
    <link rel="stylesheet" href="styles.css" />
    <style>
        .profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin: 0 auto 20px auto;
            border: 3px solid #388e3c;
        }
    </style>
</head>
<body>
    <?php if ($profile_pic): ?>
        <img src="../ADMIN/<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture" class="profile-pic" />
    <?php else: ?>
        <img src="../../img/default-profile.png" alt="Default Profile Picture" class="profile-pic" />
    <?php endif; ?>

    <h1>Welcome, <?php echo htmlspecialchars($username); ?></h1>

    <section>
        <h2>User Dashboard</h2>
        <div class="dashboard-buttons">
            <a href="EditProfile/index.php" class="btn">Edit Profile (Username/Password)</a>
            <a href="AddFood/index.php" class="btn">Add New Food Item</a>
            <a href="logout.php" class="btn logout-btn">Logout</a>
            <a href="../index.php" class="btn">Back to Home</a>
        </div>
    </section>
</body>
</html>
