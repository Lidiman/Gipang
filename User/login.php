<?php
session_start();

// Redirect logged in users to appropriate pages
if (isset($_SESSION["logged"]) && $_SESSION["logged"] === true && isset($_SESSION['username'])) {
    if ($_SESSION["status"] === 'Admin') {
        header("Location: ../ADMIN/index.php");
    } else {
        header("Location: ../index.php");
    }
    exit();
}

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
    $password = $_POST['pw'] ?? '';

    $host = "localhost";
    $user = "root";
    $pass = "";
    $dbname = "gipang";

    $conn = new mysqli($host, $user, $pass, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT US_NAME, US_PW, US_STATUS FROM user WHERE US_NAME = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Use password_verify if passwords are hashed, else fallback to plain comparison
        if (password_verify($password, $row['US_PW']) || $password === $row['US_PW']) {
            $_SESSION['username'] = $username;
            $_SESSION["logged"] = true;
            $_SESSION["status"] = $row['US_STATUS'];

            if ($row['US_STATUS'] === 'Admin') {
                header('Location: ../ADMIN/index.php');
                exit();
            } else {
                header('Location: ../index.php');
                exit();
            }
        } else {
            $error_message = "Invalid password. Please try again.";
        }
    } else {
        $error_message = "No user found with that username. Please register.";
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
<link rel="stylesheet" href="styles.css">
<link rel="stylesheet" href="login.css">
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
                    <a href="../Register/Reg.php" class="link-d">Register</a>
                </div>
                <div class="masuk">
                    <button type="submit" class="btn">Sign in</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
