<?php
session_start();
require_once '../includes/db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Initialize the $error variable to prevent the warning
$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 1) {
            $admin = $res->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "Admin user not found.";
        }

        $stmt->close();
    } else {
        $error = "Please enter both username and password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
      background: #f4f7fc;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .login-card {
      background: rgba(255, 255, 255, 0.9);
      border-radius: 20px;
      padding: 40px;
      width: 100%;
      max-width: 420px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
      color: #333;
      animation: fadeIn 0.7s ease-in-out;
    }

    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(-10px);}
      to {opacity: 1; transform: translateY(0);}
    }

    .login-card h2 {
      text-align: center;
      margin-bottom: 30px;
      font-weight: 600;
      color: #4b6cb7;
    }

    .form-control {
      background: #f8f9fc;
      color: #333;
      border: 1px solid #ddd;
      padding-left: 40px;
    }

    .form-control::placeholder {
      color: rgba(0, 0, 0, 0.5);
    }

    .input-group-text {
      background: transparent;
      border: none;
      color: #333;
      position: absolute;
      left: 10px;
      top: 50%;
      transform: translateY(-50%);
    }

    .input-group {
      position: relative;
    }

    .btn-login {
      background-color: #4b6cb7;
      border: none;
      width: 100%;
      font-weight: 500;
      transition: background 0.3s ease;
      color: white;
    }

    .btn-login:hover {
      background-color: #2a5298;
    }

    .alert {
      background-color: rgba(255, 0, 0, 0.1);
      color: #ffb3b3;
      border: 1px solid rgba(255, 0, 0, 0.2);
    }

    .back-link {
      display: block;
      text-align: center;
      margin-top: 20px;
      font-weight: 500;
      color: #4b6cb7;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .back-link:hover {
      color: #2a5298;
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="login-card">
  <h2>Admin Login</h2>

  <?php if ($error): ?>
    <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-4 input-group">
      <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
      <input type="text" class="form-control" name="username" placeholder="Username" required>
    </div>

    <div class="mb-4 input-group">
      <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
      <input type="password" class="form-control" name="password" placeholder="Password" required>
    </div>

    <button type="submit" class="btn btn-login btn-lg">Login</button>
  </form>

  <a href="../index.php" class="back-link">Back to Main Page</a>
</div>

<!-- Bootstrap + Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
