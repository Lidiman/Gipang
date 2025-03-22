<?php
session_start();

// Start the session
if (!isset($_SESSION["logged"]) || $_SESSION["logged"] !== true) {
    header("Location: ../User/login.php"); // Redirect unauthorized users
    exit;
}

if (!isset($_SESSION['username'])) {
    header("Location: User/login.php"); // Redirect to login page
    exit;
}

$host = 'localhost';
$db = 'gipang'; 
$user = 'root'; 
$pass = ''; 

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $delete = "DELETE FROM user WHERE US_ID = $id";
    if ($conn->query($delete) === TRUE) {
        $delete2 = "DELETE FROM images WHERE IMG_ID = $id";
            if ($conn->query($delete2) == TRUE){
                header("Location: index.php");
                exit;
            }
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}
?>
<!doctype html>
<html lang="en"> 
<head> 
    <meta charset="UTF-8"> 
    <title>CRUD Operations</title> 
    <link rel="stylesheet" href="./crud.css"> 
</head> 
<body>
    <form method="POST" action="index.php">
        <button type="submit" name="out" id="out">Log Out</button>
    </form>
    <?php
    ?>
    <h1>CRUD Panel</h1>
    <h2><a href="Create/create.php">Add New Record</a></h2>
    <h2>Existing Records</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Password</th>
            <th>Status</th>
            <th>Profile</th>
            <th>Actions</th>
        </tr>
        <?php
$sql = "SELECT user.US_ID, user.US_NAME, user.US_EMAIL, user.US_PW, user.US_STATUS, images.IMG_ID, images.IMG_DIR FROM user INNER JOIN images ON user.IMG = images.IMG_ID;
";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['US_ID']}</td>
                        <td>{$row['US_NAME']}</td>
                        <td>{$row['US_EMAIL']}</td>
                        <td>{$row['US_PW']}</td>             
                        <td>{$row['US_STATUS']}</td>
                        <td><img src='IMG/{$row['IMG_DIR']}' alt='Girl in a jacket' width='200' height='100'></td>
                        <td>
                            <a href='edit/index.php?edit={$row['US_ID']}'>Edit</a>
                            <a href='index.php?delete={$row['US_ID']}' onclick='return confirm(\"Are you sure you want to delete this record?\");'>Delete</a>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No records found</td></tr>";
        }

        if (isset($_POST["out"])) {
            session_unset();
            session_destroy();
            header("Location:../User/login.php");
        }
        if (isset($_POST["delete"])) {
            $id = $_POST["delete"];
        }
        ?>
    </table>
</body>
</html>
