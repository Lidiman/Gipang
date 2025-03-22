<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <form action="input.php" method="post">
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
                    <select name="status" id="status">
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                </div>
                <div class="mass">
                    <button type="submit" class="btn">Register</button>
                </div>
            </div>    
            </form>
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
        $password = $_POST['password'];

        $host = "localhost";
        $user = "root";
        $pass = "";
        $dbname = "gipang";
        $conn = mysqli_connect($host, $user, $pass, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user into the database
        $stmt = $conn->prepare("INSERT INTO user (US_NAME, US_PW) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashed_password);
        if ($stmt->execute()) {
            echo "<p style='color:green;'>Registration successful!</p>";
        } else {
            echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
        $conn->close();
    }
    ?>
