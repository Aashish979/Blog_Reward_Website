<?php
session_start();
require_once '../includes/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Fetch all users to update points and badges
$userQuery = "SELECT id, name FROM users";
$userResult = $conn->query($userQuery);

// Loop through each user to update points and badges
while ($user = $userResult->fetch_assoc()) {
    $userId = $user['id'];

    // Calculate total words written by the user
    $wordQuery = "SELECT SUM(CHAR_LENGTH(content) - CHAR_LENGTH(REPLACE(content, ' ', '')) + 1) AS total_words FROM blogs WHERE user_id = ?";
    $wordStmt = $conn->prepare($wordQuery);
    $wordStmt->bind_param("i", $userId);
    $wordStmt->execute();
    $wordResult = $wordStmt->get_result();
    $wordRow = $wordResult->fetch_assoc();
    $totalWords = (int) $wordRow['total_words'];

    // Get the appropriate badge based on the total words (using threshold_points)
    $badgeQuery = "SELECT badge_name FROM badge_thresholds WHERE threshold_points <= ? ORDER BY threshold_points DESC LIMIT 1";
    $badgeStmt = $conn->prepare($badgeQuery);
    $badgeStmt->bind_param("i", $totalWords);
    $badgeStmt->execute();
    $badgeResult = $badgeStmt->get_result();

    if ($badgeResult->num_rows > 0) {
        $badgeRow = $badgeResult->fetch_assoc();
        $badgeName = $badgeRow['badge_name'];
    } else {
        $badgeName = ''; // Leave blank if no badge threshold is met
    }

    // Update the user's badge and points in the database
    $updateStmt = $conn->prepare("UPDATE users SET badge = ?, points = ? WHERE id = ?");
    $updateStmt->bind_param("sii", $badgeName, $totalWords, $userId);
    $updateStmt->execute();
}

// Now fetch updated data for display
$sql = "SELECT name, points, badge FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fc;
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .navbar {
            background-color: #28a745; /* Light green navbar */
        }

        .navbar-brand {
            font-weight: 600;
        }

        .container {
            margin-top: 50px;
        }

        .table {
            animation: slideIn 1s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .table th, .table td {
            text-align: center;
            padding: 15px;
        }

        .table th {
            background-color: #4b6cb7;
            color: white;
        }

        .table-hover tbody tr:hover {
            background-color: #e2e6ea;
            cursor: pointer;
            transform: scale(1.02);
            transition: all 0.3s ease-in-out;
        }

        .table-hover tbody tr {
            transition: all 0.3s ease-in-out;
        }

        .btn-primary {
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #5cb85c; /* Green hover effect */
        }

        .btn-light {
            color: #333;
            transition: color 0.3s ease;
        }

        .btn-light:hover {
            color: #fff;
            background-color: #333;
        }

        .float-end {
            margin-top: 20px;
        }

        .navbar-nav {
            position: absolute;
            right: 0;
            margin-right: 30px; /* Larger gap from the right side */
        }

        .navbar-nav .nav-item {
            margin-left: 20px;
        }

        .navbar-nav i {
            font-size: 1.5rem; /* Increase icon size */
            color: #fff;
        }

        .navbar-nav i:hover {
            color: #f1f1f1; /* Hover effect to change icon color */
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <span class="navbar-brand">Admin Dashboard</span>
        <div class="navbar-nav ms-auto">
            <a href="settings.php" class="nav-item nav-link">
                <i class="fas fa-cogs"></i> <!-- Settings Icon -->
            </a>
            <a href="logout.php" class="nav-item nav-link">
                <i class="fas fa-sign-out-alt"></i> <!-- Logout Icon -->
            </a>
        </div>
    </div>
</nav>

<div class="container">
    <h2 class="mb-4 text-center">Users Overview</h2>
    
    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th>Name</th>
            <th>Points</th>
            <th>Badge</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= $row['points'] ?></td>
                <td><?= htmlspecialchars($row['badge'] ?? 'No Badge') ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
