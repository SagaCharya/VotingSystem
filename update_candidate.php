<?php
session_start();


if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}


$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "voting_system";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $age = $_POST['age'];
    $party = $_POST['party'];
    $position = $_POST['position'];
    $photo = $_FILES['photo']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($photo);

    
    if ($photo) {
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
            $stmt = $conn->prepare("UPDATE candidates SET name=?, age=?, photo=?, party=?, position=? WHERE id=?");
            $stmt->bind_param("sisssi", $name, $age, $target_file, $party, $position, $id);
        } else {
            $error = "Failed to upload photo.";
        }
    } else {
        $stmt = $conn->prepare("UPDATE candidates SET name=?, age=?, party=?, position=? WHERE id=?");
        $stmt->bind_param("sissi", $name, $age, $party, $position, $id);
    }

    if (!isset($error)) {
        $stmt->execute();
        $stmt->close();
        $success = "Candidate updated successfully!";
    }
}


if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM candidates WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $candidate = $result->fetch_assoc();
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Candidate</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
</head>
<body>
    <div class="admin-container">
        <div class="side-menu">
            <h3>Admin Menu</h3>
            <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="manage_candidates.php">Manage Candidates</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <h2>Update Candidate</h2>
            <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
            <?php if (isset($success)) { echo "<p class='success'>$success</p>"; } ?>
            <form method="post" action="" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($candidate['id']); ?>">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($candidate['name']); ?>" required><br><br>
                <label for="age">Age:</label>
                <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($candidate['age']); ?>" required><br><br>
                <label for="party">Party:</label>
                <input type="text" id="party" name="party" value="<?php echo htmlspecialchars($candidate['party']); ?>" required><br><br>
                <label for="position">Position:</label>
                <input type="text" id="position" name="position" value="<?php echo htmlspecialchars($candidate['position']); ?>" required><br><br>
                <label for="photo">Photo:</label>
                <input type="file" id="photo" name="photo" accept="image/*"><br><br>
                <input type="submit" class="update-candidate" value="Update Candidate">
            </form>
        </div>
    </div>
</body>
</html>