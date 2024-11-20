<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
</head>
<body>
    <nav class="side-menu">
        <h3>Menu</h3>
        <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="vote.php">Vote</a></li>
            <li><a href="results.php">
                View Results
            </a></li>
            <li><a href="logout.php">Logout</a></li>
           
        </ul>
    </nav>
    <div class="main-content">
        <?php include 'view_candidates.php'; ?>
    </div>
    <div class="button">
        <button id="voteButton" class="center-button">Vote</button>
    </div>
    <script>
        document.getElementById('voteButton').addEventListener('click', function() {
            window.location.href = './vote.php';
        });
    </script>
</body>
</html>