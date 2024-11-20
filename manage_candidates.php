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


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_candidate'])) {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $party = $_POST['party'];
    $position = $_POST['position'];
    $photo = $_FILES['photo']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($photo);

    
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO candidates (name, age, photo, party, position) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sisss", $name, $age, $target_file, $party, $position);
        $stmt->execute();
        $stmt->close();

        $success = "Candidate added successfully!";
    } else {
        $error = "Failed to upload photo.";
    }
}


$result = $conn->query("SELECT * FROM candidates");

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Candidates</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
</head>
<body>
    <div class="admin-container">
        <div class="side-menu">
            <h3>Admin Menu</h3>
            <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="manage_candidates.php">Manage Candidates</a></li>
                <li><a href="manage_users.php">Manage User</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <h2>Manage Candidates</h2>
            <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
            <?php if (isset($success)) { echo "<p class='success'>$success</p>"; } ?>
            <form method="post" action="" enctype="multipart/form-data">
                <input type="hidden" name="add_candidate" value="1">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required><br><br>
                <label for="age">Age:</label>
                <input type="number" id="age" name="age" required><br><br>
                <label for="party">Party:</label>
                <input type="text" id="party" name="party" required><br><br>
                <label for="position">Position:</label>
                <input type="text" id="position" name="position" required><br><br>
                <label for="photo">Photo:</label>
                <input type="file" id="photo" name="photo" accept="image/*" required><br><br>
                <input type="submit" class="add-candidate" value="Add Candidate">
            </form>
            <h3>Candidate List</h3>
            <table>
                <tr>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Photo</th>
                    <th>Party</th>
                    <th>Position</th>
                    <th>Actions</th>
                </tr>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['name']) . "</td>
                                <td>" . htmlspecialchars($row['age']) . "</td>
                                <td><img src='" . htmlspecialchars($row['photo']) . "' alt='Photo' width='50'></td>
                                <td>" . htmlspecialchars($row['party']) . "</td>
                                <td>" . htmlspecialchars($row['position']) . "</td>
                                <td>
                                    <a href='update_candidate.php?id=" . htmlspecialchars($row['id']) . "' class='button'>Update</a>
                                    <a href='delete_candidate.php?id=" . htmlspecialchars($row['id']) . "' class='button delete' onclick='return confirm(\"Are you sure you want to delete this candidate?\")'>Delete</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No candidates found</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>
</body>
</html>