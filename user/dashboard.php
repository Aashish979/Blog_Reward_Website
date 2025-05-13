<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: auth/login.php");
  exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Total word count
$result = $conn->query("SELECT SUM(word_count) AS total_words FROM blogs WHERE user_id = $user_id");
$total_words = (int) $result->fetch_assoc()['total_words'];

// Handle blog post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['content'])) {
  $title = trim($_POST['title']);
  $content = trim($_POST['content']);
  $word_count = str_word_count($content);
  $points = $word_count;

  $stmt = $conn->prepare("INSERT INTO blogs (user_id, title, content, word_count, points) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("issii", $user_id, $title, $content, $word_count, $points);
  $stmt->execute();
  $stmt->close();

  $conn->query("UPDATE users SET points = points + $points WHERE id = $user_id");

  header("Location: " . $_SERVER['PHP_SELF']);
  exit();
}

// Fetch blogs
$blogs = $conn->query("SELECT * FROM blogs WHERE user_id = $user_id ORDER BY created_at DESC");

// User points
$result = $conn->query("SELECT points FROM users WHERE id = $user_id");
$user_points = (int) $result->fetch_assoc()['points'];

// Badge logic
$badge = 'None';
$badgeIcon = '🏅';

$badgeQuery = $conn->query("SELECT badge_name, icon FROM badge_thresholds WHERE threshold_points <= $user_points ORDER BY threshold_points DESC LIMIT 1");
if ($badgeQuery && $badgeQuery->num_rows > 0) {
  $badgeData = $badgeQuery->fetch_assoc();
  $badge = $badgeData['badge_name'];
  $badgeIcon = $badgeData['icon'];

  $updateBadge = $conn->prepare("UPDATE users SET badge = ? WHERE id = ?");
  $updateBadge->bind_param("si", $badge, $user_id);
  $updateBadge->execute();
  $updateBadge->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
      display: flex;
      margin: 0;
      background-color: #f8f9fa;
      overflow-x: hidden;
    }

    .sidebar {
      width: 250px;
      background: linear-gradient(180deg, #198754 0%, #145c46 100%);
      color: white;
      min-height: 100vh;
      position: fixed;
      transition: all 0.3s ease-in-out;
      box-shadow: 3px 0 10px rgba(0,0,0,0.1);
      z-index: 1000;
    }

    .sidebar h4 {
      font-weight: 600;
      font-size: 1.4rem;
      border-bottom: 1px solid rgba(255,255,255,0.2);
    }

    .sidebar a {
      color: white;
      padding: 15px 20px;
      display: flex;
      align-items: center;
      font-weight: 500;
      text-decoration: none;
      transition: all 0.3s;
    }

    .sidebar a i {
      margin-right: 10px;
      transition: transform 0.3s ease-in-out;
    }

    .sidebar a:hover {
      background-color: #157347;
      padding-left: 25px;
    }

    .sidebar a:hover i {
      transform: scale(1.2);
    }

    .content {
      margin-left: 250px;
      flex-grow: 1;
      padding: 30px;
      transition: margin-left 0.3s ease-in-out;
    }

    h3, h4 {
      font-weight: 600;
    }

    .btn-success {
      font-weight: 500;
      letter-spacing: 0.5px;
    }

    textarea, input {
      border-radius: 0.5rem !important;
    }

    .card-title {
      font-weight: 600;
    }
  </style>
</head>
<body>

  <div class="sidebar">
    <h4 class="text-center py-3 border-bottom">Hi, <?php echo htmlspecialchars($user_name); ?></h4>
    <a href="#createBlog"><i class="fas fa-pen"></i> Create Blog</a>
    <a href="#yourBlogs"><i class="fas fa-book"></i> Your Blogs</a>
    <a href="#yourReward"><i class="fas fa-award"></i> Your Reward</a>
    <a href="auth/login.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <div class="content">
    <!-- Create Blog -->
    <section id="createBlog">
      <h3>Create a Blog</h3>
      <form method="POST" action="">
        <div class="mb-3">
          <label class="form-label">Title</label>
          <input type="text" name="title" class="form-control" required />
        </div>
        <div class="mb-3">
          <label class="form-label">Content</label>
          <textarea name="content" class="form-control" rows="6" required></textarea>
        </div>
        <div class="text-center">
          <button type="submit" class="btn btn-success">Publish</button>
        </div>
      </form>
    </section>

    <!-- Your Blogs -->
    <section id="yourBlogs" class="mt-5">
      <h4>Your Blogs</h4>
      <?php while ($blog = $blogs->fetch_assoc()): ?>
        <div class="card mt-3">
          <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($blog['title']); ?></h5>
            <p class="card-text">
              <?php echo nl2br(htmlspecialchars(substr($blog['content'], 0, 200))); ?>...
              <a href="view_blog.php?id=<?php echo $blog['id']; ?>" class="text-success text-decoration-none fw-semibold">Read More</a>
            </p>
            <p class="text-muted">Words: <?php echo $blog['word_count']; ?> | Points: <?php echo $blog['points']; ?></p>
          </div>
        </div>
      <?php endwhile; ?>
    </section>

    <!-- Reward -->
    <section id="yourReward" class="mt-5">
      <?php if ($badge !== 'None'): ?>
        <div class="card p-4 text-center border-0 shadow">
          <p class="fw-bold text-success" style="font-size: 1.25rem;">
            Total Points: <?php echo $user_points; ?>
          </p>
          <div style="font-size: 4rem;"><?php echo $badgeIcon; ?></div>
          <p class="fw-semibold">
            You've earned the 
            <span class="text-primary fw-bold" style="font-size: 1.5rem;"><?php echo $badge; ?></span> badge!
          </p>
          <div class="d-flex justify-content-center gap-3 mt-4">
            <a href="https://twitter.com/intent/tweet?text=I%20earned%20the%20<?php echo urlencode($badge); ?>%20badge!&url=http://yourwebsite.com" target="_blank" style="font-size: 2rem; color: #1DA1F2;">
              <i class="fab fa-twitter"></i>
            </a>
            <a href="https://www.facebook.com/sharer/sharer.php?u=http://yourwebsite.com" target="_blank" style="font-size: 2rem; color: #1877F2;">
              <i class="fab fa-facebook"></i>
            </a>
            <a href="https://www.linkedin.com/sharing/share-offsite/?url=http://yourwebsite.com" target="_blank" style="font-size: 2rem; color: #0077B5;">
              <i class="fab fa-linkedin"></i>
            </a>
            <a href="https://www.youtube.com/share?url=http://yourwebsite.com" target="_blank" style="font-size: 2rem; color: #FF0000;">
              <i class="fab fa-youtube"></i>
            </a>
          </div>
        </div>
      <?php else: ?>
        <p class="fw-semibold text-center mt-5">
          <span class="text-primary fw-bold" style="font-size: 1.5rem;">You haven't earned any badge yet!</span>
        </p>
      <?php endif; ?>
    </section>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
