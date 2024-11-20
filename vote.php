<?php
session_start();


if (!isset($_SESSION['user_id'])) {
    echo "You must log in to vote.";
    exit();
}

$userId = $_SESSION['user_id']; 


$message = '';


$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "voting_system";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $candidateId = $_POST['candidate_id'];

    
    if (!filter_var($candidateId, FILTER_VALIDATE_INT)) {
        $message = "Invalid candidate ID.";
    } else {
        
        $sql = "SELECT position FROM candidates WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $candidateId);
            $stmt->execute();
            $stmt->bind_result($position);
            $stmt->fetch();
            $stmt->close();

            
            $voteCheckSql = "SELECT * FROM votes WHERE user_id = ? AND position = ?";
            if ($stmt = $conn->prepare($voteCheckSql)) {
                $stmt->bind_param("is", $userId, $position);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $message = "You have already voted for this position.";
                } else {
                    
                    $insertSql = "INSERT INTO votes (user_id, candidate_id, position) VALUES (?, ?, ?)";
                    if ($stmt = $conn->prepare($insertSql)) {
                        $stmt->bind_param("iis", $userId, $candidateId, $position);
                        if ($stmt->execute()) {
                            $message = "Your vote has been successfully recorded.";
                        } else {
                            $message = "Error recording vote.";
                        }
                    } else {
                        $message = "Error preparing SQL insert statement: " . $conn->error;
                    }
                }
                $stmt->close();
            } else {
                $message = "Error preparing SQL select statement: " . $conn->error;
            }
        } else {
            $message = "Error fetching candidate position: " . $conn->error;
        }
    }
}


$sql = "SELECT * FROM candidates ORDER BY position ASC";
$result = $conn->query($sql);

if ($result === false) {
    $message = "Error fetching candidates: " . $conn->error;
    $conn->close();
    exit();
}


$voteCheckSql = "SELECT position FROM votes WHERE user_id = ?";
if ($stmt = $conn->prepare($voteCheckSql)) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($votedPosition);
    while ($stmt->fetch()) {
        $userVotes[] = $votedPosition;
    }
    $stmt->close();
} else {
    $message = "Error preparing SQL select statement: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote</title>
    <link rel="stylesheet" type="text/css" href="./css/vote.css">
</head>
<body>
    <nav class="side-menu">
        <h3>Menu</h3>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="vote.php">Vote</a></li>
            <li><a href="results.php">View Results</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="main-content">
        <?php
        // Display message if set
        if ($message) {
            echo "<div class='message'>$message</div>";
        }
        ?>
        <div class="card-container">
            <?php
            $currentTitle = ''; 
            if ($result->num_rows > 0) {
                echo '<div class="card-container">';
                while ($row = $result->fetch_assoc()) {
                    
                    if ($currentTitle != $row['position']) {
                        // Close the previous section (if any)
                        if ($currentTitle != '') {

                        }

                        
                        echo "<div class='position-divider'><h2>" . htmlspecialchars($row['position']) . "</h2></div>";
                        echo "<div class='card-position-group'>";
                        $currentTitle = $row['position']; 
                    }
                    ?>
                    <!-- Candidate Card -->
                    <div class="card">
                        <img src="<?php echo htmlspecialchars($row['photo']); ?>" alt="Candidate Photo" class="candidate-photo">
                        <div class="card-body">
                            <h2 class="candidate-name"><?php echo htmlspecialchars($row['name']); ?></h2>
                            <p class="candidate-party">Party: <?php echo htmlspecialchars($row['party']); ?></p>
                            
                            <?php
                            if (in_array($row['position'], $userVotes)) {
                                echo "<button class='vote-btn' disabled>Already Voted</button>";
                            } else {
                                ?>
                                <form method="POST" action="vote.php">
                                    <input type="hidden" name="candidate_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                    <button class="vote-btn" type="submit">Vote</button>
                                </form>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
                echo "</div>"; 
                echo "</div>"; 
            } else {
                echo "No candidates found.";
            }
            ?>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
