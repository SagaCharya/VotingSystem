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


if (isset($_GET['delete_user'])) {
    $user_id = intval($_GET['delete_user']);
    $delete_query = "DELETE FROM users WHERE id = $user_id";
    if ($conn->query($delete_query)) {
        echo "<p>User deleted successfully.</p>";
    } else {
        echo "<p>Error deleting user.</p>";
    }
}


if (isset($_POST['update_role'])) {
    $user_id = intval($_POST['user_id']);
    $new_role = $conn->real_escape_string($_POST['role']);
    $update_query = "UPDATE users SET role = '$new_role' WHERE id = $user_id";
    if ($conn->query($update_query)) {
        echo "<p>User role updated successfully.</p>";
    } else {
        echo "<p>Error updating user role.</p>";
    }
}


$users_result = $conn->query("SELECT id, username, role FROM users");

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
</head>
<body>
    <div class="admin-container">
        <div class="side-menu">
            <h3>Admin Menu</h3>
            <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="manage_candidates.php">Manage Candidates</a></li>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <h2>Manage Users</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
                <?php
                if ($users_result->num_rows > 0) {
                    while ($row = $users_result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['id']) . "</td>
                                <td>" . htmlspecialchars($row['username']) . "</td>
                                <td>
                                    <form action='manage_users.php' method='post'>
                                        <input type='hidden' name='user_id' value='" . htmlspecialchars($row['id']) . "'>
                                        <select name='role'>
                                            <option value='user' " . ($row['role'] == 'user' ? 'selected' : '') . ">User</option>
                                            <option value='admin' " . ($row['role'] == 'admin' ? 'selected' : '') . ">Admin</option>
                                        </select>
                                        <button type='submit' name='update_role'>Update</button>
                                    </form>
                                </td>
                                <td>
                                    <a href='manage_users.php?delete_user=" . htmlspecialchars($row['id']) . "' onclick=\"return confirm('Are you sure you want to delete this user?');\">Delete</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No users found</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>
</body>
</html>
