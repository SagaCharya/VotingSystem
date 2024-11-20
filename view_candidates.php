
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
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

$sql = "SELECT * FROM candidates";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Results</title>
    <link rel="stylesheet" type="text/css" href="./css/ds.css">
</head>
<body>
    <div class="admin_container"
    <div class="main-content">
        <h2>All Candidates</h2>
        <?php
        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Name</th><th>Age</th><th>Photo</th><th>Party</th><th>Position</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['age']) . "</td>";
                echo "<td><img src='" . htmlspecialchars($row['photo']) . "' alt='Photo' width='50'></td>";
                echo "<td>" . htmlspecialchars($row['party']) . "</td>";
                echo "<td>" . htmlspecialchars($row['position']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No candidates found";
        }
        $conn->close();
        ?>
    </div>
    </div>
</body>
</html>