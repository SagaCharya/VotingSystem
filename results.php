<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "voting_system";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$positions = [];


$sql = "SELECT 
            c.position, 
            c.name AS candidate_name, 
            COUNT(v.id) AS vote_count
        FROM 
            candidates c
        LEFT JOIN 
            votes v ON c.id = v.candidate_id
        GROUP BY 
            c.position, c.id
        ORDER BY 
            c.position ASC, vote_count DESC";

$result = $conn->query($sql);

if ($result === false) {
    echo "Error executing query: " . $conn->error;
} else {
    // Prepare data for Chart.js
    while ($row = $result->fetch_assoc()) {
        if (!isset($positions[$row['position']])) {
            $positions[$row['position']] = [];
        }
        $positions[$row['position']][] = [
            'name' => $row['candidate_name'],
            'votes' => $row['vote_count']
        ];
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote Results</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" type="text/css" href="./css/vote.css">
    <link rel="stylesheet" type="text/css" href="./css/results.css">
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
        <h1>Vote Results</h1>
        <div id="charts-container">
            <?php if (empty($positions)): ?>
                <p>No results available.</p>
            <?php else: ?>
                <?php foreach ($positions as $position => $candidates): ?>
                    <h2><?php echo htmlspecialchars($position); ?></h2>
                    <div class="chart-wrapper">
                        <canvas id="chart-<?php echo htmlspecialchars(strtolower(str_replace(' ', '-', $position))); ?>"></canvas>
                    </div>
                    <script>
                        var ctx = document.getElementById('chart-<?php echo htmlspecialchars(strtolower(str_replace(' ', '-', $position))); ?>').getContext('2d');
                        var chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: <?php echo json_encode(array_column($candidates, 'name')); ?>,
                                datasets: [{
                                    label: 'Votes',
                                    data: <?php echo json_encode(array_column($candidates, 'votes')); ?>,
                                    backgroundColor: 'rgba(54, 162, 235, 0.7)', 
                                    borderColor: 'rgba(54, 162, 235, 1)',    
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: false, 
                                maintainAspectRatio: false,
                                scales: {
                                    x: {
                                        beginAtZero: true
                                    },
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            min: 1, 
                                            stepSize: 1 
                                        }
                                    }
                                }
                            }
                        });
                    </script>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
