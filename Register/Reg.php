<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <form method="post">
        <div class="in">
            <video autoplay muted loop id="myVideo">
                <source src="vid2.mp4" type="video/mp4">
              </video>
                <h1>Register</h1>  
                <div class="Name">
                    <input type="text" placeholder="Nama Lengkap" name="nama" id="nama">
                    <div class="undr"></div>
                </div>
                <div class="email">
                    <input type="email" placeholder="Email" name="email" id="email" required="required">
                    <div class="undr"></div>
                </div>
                <div class="pass">
                    <input type="password" placeholder="Password" name="password" id="password" required="required" minlength="8" maxlength="8">
                    <div class="undr"></div>
                </div>
                <div class="dropdown">
                </div>
                <div class="mass">
                    <button type="submit" class="btn">Register</button>
                </div>
            </div>    
            </form>
    <?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = filter_var(trim($_POST['nama']), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (empty($username) || empty($email) || empty($password)) {
        echo "<p style='color:red;'>All fields are required.</p>";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<p style='color:red;'>Invalid email format.</p>";
        exit;
    }

    if (strlen($password) < 8) {
        echo "<p style='color:red;'>Password must be at least 8 characters.</p>";
        exit;
    }

    $host = "localhost";
    $user = "root";
    $pass = "";
    $dbname = "gipang";
    $conn = mysqli_connect($host, $user, $pass, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $stmt = $conn->prepare("INSERT INTO user (US_NAME,US_EMAIL,US_PW) VALUES (?,?,?)");
    $stmt->bind_param("sss", $username, $email, $password);
    if ($stmt->execute()) {
        header('Location: ../User/login.php');
    } else {
        echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
    $conn->close();
}
?>

