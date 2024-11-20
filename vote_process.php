<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to vote.";
    exit();
}

$userId = $_SESSION['user_id'];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $candidateId = $_POST['candidate_id'];
    $title = $_POST['title'];

   
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "voting_system";
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    
    $sql = "SELECT * FROM votes WHERE user_id = ? AND title = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("is", $userId, $title);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "You have already voted for this position.";
        } else {
           
            $insertSql = "INSERT INTO votes (user_id, candidate_id, title) VALUES (?, ?, ?)";
            if ($stmt = $conn->prepare($insertSql)) {
                $stmt->bind_param("iis", $userId, $candidateId, $title);

                if ($stmt->execute()) {
                    echo "Vote successfully recorded.";
                } else {
                    echo "Error recording vote: " . $stmt->error;
                }
            } else {
                echo "Error preparing SQL insert statement: " . $conn->error;
            }
        }

        $stmt->close();
    } else {
        echo "Error preparing SQL select statement: " . $conn->error;
    }

    $conn->close();
} else {
    echo "Invalid request.";
}
