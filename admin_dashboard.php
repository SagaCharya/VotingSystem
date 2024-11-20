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


$votes_result = $conn->query("SELECT candidate, COUNT(*) as vote_count FROM votes GROUP BY candidate");


$candidates_result = $conn->query("SELECT * FROM candidates");

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
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
            <h2>Admin Dashboard</h2>
         
            <h3>Candidates</h3>
            <table>
                <tr>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Photo</th>
                    <th>Party</th>
                    <th>Position</th>
                </tr>
                <?php
                if ($candidates_result->num_rows > 0) {
                    while ($row = $candidates_result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['name']) . "</td>
                                <td>" . htmlspecialchars($row['age']) . "</td>
                                <td><img src='" . htmlspecialchars($row['photo']) . "' alt='Photo' width='50'></td>
                                <td>" . htmlspecialchars($row['party']) . "</td>
                                <td>" . htmlspecialchars($row['position']) . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No candidates found</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>
</body>
</html>