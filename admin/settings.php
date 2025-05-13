<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Handle POST update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['thresholds'] as $badge => $value) {
        $badge = $conn->real_escape_string($badge);
        $value = (int)$value;
        $conn->query("UPDATE badge_thresholds SET threshold_points = $value WHERE badge_name = '$badge'");
    }
    $success = true;
}

// Fetch current values
$thresholds = [];
$res = $conn->query("SELECT * FROM badge_thresholds");
while ($row = $res->fetch_assoc()) {
    $thresholds[$row['badge_name']] = $row['threshold_points'];
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f6f9;
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

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #28a745;
            color: #fff;
            font-weight: 600;
            text-align: center;
            padding: 15px;
        }

        .card-body {
            background-color: #fff;
            padding: 25px;
        }

        .form-label {
            font-weight: 600;
        }

        .form-control {
            border-radius: 5px;
        }

        .btn-success {
            background-color: #28a745;
            border: none;
            transition: background-color 0.3s;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .alert {
            margin-bottom: 20px;
            font-weight: 600;
        }

        .badge-icon {
            font-size: 1.3rem;
            margin-right: 10px;
        }

        .badge-setting {
            display: flex;
            align-items: center;
        }

        .badge-setting span {
            font-weight: 600;
        }

        .badge-setting input {
            width: 100px;
            margin-left: 15px;
        }

        .float-end {
            margin-top: 20px;
        }

        .navbar-nav .nav-item {
            margin-left: 20px;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <span class="navbar-brand">Admin Settings</span>
            <a href="dashboard.php" class="btn btn-light">Dashboard</a>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h4>Badge Threshold Settings</h4>
            </div>
            <div class="card-body">
                <?php if (isset($success)): ?>
                    <div class="alert alert-success">Thresholds updated successfully.</div>
                <?php endif; ?>
                <form method="POST">
                    <?php foreach ($thresholds as $badge => $points): ?>
                        <div class="badge-setting mb-3">
                            <i class="fas fa-trophy badge-icon"></i>
                            <span><?= htmlspecialchars($badge) ?> Threshold</span>
                            <input type="number" name="thresholds[<?= $badge ?>]" class="form-control" value="<?= $points ?>" required>
                        </div>
                    <?php endforeach; ?>
                        <button type="submit" class="btn btn-success float-end">Update</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>






