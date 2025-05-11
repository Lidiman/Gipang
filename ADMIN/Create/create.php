<?php
$host = 'localhost';
$db = 'gipang'; 
$user = 'root'; 
$pass = ''; 

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (isset($_POST["create"])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $id = $_POST["status"];
    $img_id = NULL; 

    if (!empty($_FILES['image']['name'])) {
        $file_name = basename($_FILES['image']['name']);
        $tempname = $_FILES['image']['tmp_name'];
        $upload_dir = '../img/profile/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $target_file = $upload_dir . uniqid() . '_' . $file_name;

        if (move_uploaded_file($tempname, $target_file)) {
            $img_dir = 'img/profile/' . basename($target_file);
            $stmt = $conn->prepare("INSERT INTO images (IMG_DIR) VALUES (?)");
            if (!$stmt) {
                die("Error preparing image insert: " . $conn->error);
            }
            $stmt->bind_param("s", $img_dir);
            $stmt->execute();
            $img_id = $conn->insert_id;
            $stmt->close();
        } else {
            die("<p style='color:red;'>Error uploading image.</p>");
        }
    }


    $stmt = $conn->prepare("INSERT INTO user (US_NAME, US_EMAIL, US_PW, US_STATUS, IMG) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Error preparing user insert: " . $conn->error);
    }

    $stmt->bind_param("ssssi", $name, $email, $password, $id, $img_id);
    echo "Status: " . $id; // Debugging output
    if ($stmt->execute()) {
        echo "<p style='color:green;'>Record successfully created</p>";
    } else {
        echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../crud.css"> 
</head>
<body>
    <h1>CRUD Panel</h1>
    <h2>Add New Record</h2>
    <form action="create.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Enter Name" required>
        <input type="text" name="email" placeholder="Enter Email" required>
        <input type="text" name="password" placeholder="Enter Password" required>
        <input type="file" name="image"> 
        <select id="status" name="status">
            <option value="Admin">Admin</option>
            <option value="User">User</option>
        </select>
        <button type="submit" name="create">Create</button>
    </form>
    <form action="../index.php" method="POST">
        <button type="submit" name="Back">Back</button>
    </form>
</body>
</html>
