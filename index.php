<?php
session_start();

$host = "localhost";
$user = "root";
$password = "";
$database = "gipang";

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize profile picture variable
$profile_pic = null;
$username = null;

if (isset($_SESSION['logged']) && $_SESSION['logged'] === true && isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $stmt = $conn->prepare("SELECT i.IMG_DIR FROM user u LEFT JOIN images i ON u.IMG = i.IMG_ID WHERE u.US_NAME = ?");
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result_img = $stmt->get_result();
        $user_data = $result_img->fetch_assoc();
        $profile_pic = $user_data['IMG_DIR'] ?? null;
        $stmt->close();
    }
}

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

if ($search !== '') {
    $sql = "SELECT FD_ID, FD_NAME, FD_DESC, FD_STAT, FD_AUTHOR, FD_IMG FROM food WHERE FD_NAME LIKE '%$search%' OR FD_DESC LIKE '%$search%'";
} else {
    $sql = "SELECT FD_ID, FD_NAME, FD_DESC, FD_STAT, FD_AUTHOR, FD_IMG FROM food";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
    <script src="main.js"></script>
</head>
<body>
    <nav>
        <h2>GiPang</h2>
        <ul>
          <li><a href="/UKL/Home/index.html">Home</a></li>
          <li><a href="#">Contact</a></li>
          <?php if ($username): ?>
            <li class="nav-profile" style="margin-left: auto; position: relative;">
                <a href="User/profile.php" class="nav-profile-link">
            <span class="nav-profile-username"><?php echo htmlspecialchars($username); ?></span>
                    <img src="<?php echo $profile_pic ? 'ADMIN/' . htmlspecialchars($profile_pic) : '../../img/default-profile.png'; ?>" alt="Profile Picture" class="nav-profile-pic">
                </a>
                <div class="nav-profile-dropdown">
                     Go to your profile
                </div>
            </li>
          <?php else: ?>
            <li><a href="User/login.php">Login</a></li>
          <?php endif; ?>
        </ul>
    </nav>
    <header>
        <h1>Welcome to GiPang</h1>
        <p>Eat Safe , Eat Happy</p>
        <form method="GET" action="index.php">
            <div class="search">
                <input type="text" name="search" placeholder="Search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit">Search</button>
            </div>
        </form>
    </header>
    <article>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                ?>
                <div class="food-item">
                    <img src="img/<?php echo htmlspecialchars($row['FD_IMG']); ?>" alt="<?php echo htmlspecialchars($row['FD_NAME']); ?>">
                    <h3><?php echo htmlspecialchars($row['FD_NAME']); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars($row['FD_DESC'])); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($row['FD_STAT']); ?></p>
                    <p><strong>Author:</strong> <?php echo htmlspecialchars($row['FD_AUTHOR']); ?></p>
                </div>
                <?php
            }
        } else {
            if (isset($_GET['search']) && $_GET['search'] !== '') {
                echo "<p>Sorry there is no match for your search.</p>";
            } else {
                echo "<p>No food items found.</p>";
            }
        }
        $conn->close();
        ?>
    </article>
</body>
</html>
