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
    $id = $_GET["edit"];
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $idt = $_POST["status"];
$sql = "UPDATE user SET US_NAME = '$name', US_EMAIL = '$email',US_PW = '$password',US_STATUS = '$idt' WHERE US_ID = '$id';";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p color:green>Record sucessfully made</p>";
    } else {
        echo "Add data error cause of " . $conn->error;
    }
}
if (isset($_POST["Back"])) {
    header("Location:../index.php");
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
    <form enctype="multipart/form-data" method="POST">
        <input type="text" name="name" placeholder="Enter Name" required>
        <input type="text" name="email" placeholder="Enter Email" required>
        <input type="text" name="password" placeholder="Enter Password" required> 
        <select id="status" name="status">
            <option value="Admin">Admin</option>
            <option value="User">User</option>
        </select>
        <button type="submit" name="create">Create</button>
    </form>
    <a href="../index.php">Logout</a>
</body>
</html>