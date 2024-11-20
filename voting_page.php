<?php
session_start();


if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'voter') {
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


$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT has_voted FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($has_voted);
$stmt->fetch();
$stmt->close();

if ($has_voted) {
    $message = "You have already voted and cannot vote again.";
} else {
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $candidate = $_POST['candidate'];

        
        $stmt = $conn->prepare("INSERT INTO votes (candidate, timestamp) VALUES (?, NOW())");
        $stmt->bind_param("s", $candidate);
        $stmt->execute();
        $stmt->close();

        
        $stmt = $conn->prepare("UPDATE users SET has_voted = TRUE WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        $message = "Vote cast successfully!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Voting Page</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <h3>Cast Your Vote</h3>
        <?php if (isset($message)) { echo "<p class='success'>$message</p>"; } ?>
        <?php if (!$has_voted): ?>
        <form method="post" action="">
            <label for="candidate">Candidate:</label>
            <input type="text" id="candidate" name="candidate" required><br><br>
            <input type="submit" value="Vote">
        </form>
        <?php endif; ?>
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>