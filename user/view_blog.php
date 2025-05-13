<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: auth/login.php");
  exit();
}

if (!isset($_GET['id'])) {
  echo "Blog not found.";
  exit();
}

$blog_id = (int) $_GET['id'];
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT title, content, created_at FROM blogs WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $blog_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  echo "Blog not found or you do not have permission.";
  exit();
}

$blog = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($blog['title']); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
  <div class="container">
    <h2><?php echo htmlspecialchars($blog['title']); ?></h2>
    <p class="text-muted">Posted on <?php echo date("d M Y", strtotime($blog['created_at'])); ?></p>
    <hr>
    <p><?php echo nl2br(htmlspecialchars($blog['content'])); ?></p>
    <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
  </div>
</body>
</html>
