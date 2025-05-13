<?php
session_start();
require_once '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, name FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $hashed_password, $name);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            header("Location: ../dashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "Incorrect password.";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Email not registered.";
        header("Location: login.php");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>

<!-- HTML Login Form -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>User Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f4f7fc;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .login-card {
      border-radius: 15px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      background-color: #ffffff;
      padding: 40px;
      width: 100%;
      max-width: 450px;
    }
    .login-card h3 {
      font-family: 'Arial', sans-serif;
      font-weight: bold;
      color: #333;
    }
    .form-control:focus {
      border-color: #6f42c1;
      box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.25);
    }
    .btn-custom {
      background-color: #6f42c1;
      border: none;
      color: white;
      padding: 12px;
      font-size: 16px;
      width: 100%;
      border-radius: 5px;
      transition: background-color 0.3s;
    }
    .btn-custom:hover {
      background-color: #5a2c9c;
    }
    .alert-custom {
      background-color: #f8d7da;
      border-color: #f5c6cb;
      color: #721c24;
    }
    .text-center a {
      color: #6f42c1;
      font-weight: bold;
      text-decoration: none;
    }
    .text-center a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="login-card">
    <h3 class="text-center mb-4">Login to Your Account</h3>

    <!-- Display session-based error if any -->
    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-custom text-center">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
      </div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <input type="email" class="form-control" name="email" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" name="password" required>
      </div>
      <button type="submit" class="btn-custom">Login</button>
    </form>

    <div class="text-center mt-3">
      <p>Don't have an account? <a href="Register.php">Register here</a></p>
    </div>
     <p class="mt-3 text-center">
  <a href="index.php" class="text-decoration-none text-secondary">Back to Main Page</a>
</p>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
