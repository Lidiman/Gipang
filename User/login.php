<?php
session_start();

// If already logged in and is admin, redirect to crud panel
if (isset($_SESSION["logged"]) && $_SESSION["logged"] === true && isset($_SESSION['username'])) {
    header("Location: ../ADMIN/index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
    $passw = $_POST['pw']; 

    $host = "localhost";
    $user = "root";
    $pass = "";
    $dbname = "gipang";
    $conn = mysqli_connect($host, $user, $pass, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT * FROM user WHERE US_NAME = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($passw === $row['US_PW']) {
            $_SESSION['username'] = $username;
            $_SESSION["logged"] = true;
            $_SESSION["status"] = $row['US_STATUS'];
            
            if ($row['US_STATUS'] === 'Admin') {
                header('Location: ../ADMIN/index.php');
                exit();
            } else if ($row['US_STATUS'] === 'User') {
                echo "<p style='color:green;'>Login as user completed</p>";
            }
        } else {
            echo "<p style='color:red;'>Invalid password. Please try again.</p>";
        }
    } else {
        echo "<p style='color:red;'>No user found with that username. Please register.</p>";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<link rel="stylesheet" href="FIRMAN.css">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<body>
    <video autoplay muted loop id="myVideo">
        <source src="Car.mp4" type="video/mp4">
    </video>
    <div class="main">
        <h1>Login</h1>
        <div class="boxin">
            <form action="login.php" method="post">
                <div class="input-box">
                    <input type="text" id="username" name="username" placeholder="Username" required>
                    <i class='bx bxs-envelope'></i>
                </div>
                <div class="input-box">
                    <input type="password" id="pw" name="pw" placeholder="Password" required>
                    <box-icon type='solid' name='lock-alt'></box-icon>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <div class="coco">
                    <label><input type="checkbox" class="check">Remember me</label>
                    <a href="main.html" class="link-d">Register</a>
                </div>
                <div class="masuk">
                    <button type="submit" class="btn">Sign in</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
