<?php
session_start();
require_once '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Email already registered!'); window.location.href='register.php';</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $password);
        if ($stmt->execute()) {
            echo "<script>alert('Registration successful! Please log in.'); window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('Registration failed!'); window.location.href='register.php';</script>";
        }
    }

    $stmt->close();
    $conn->close();
}
?>

<!-- HTML Starts -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Registration</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #e0f7fa, #f1f8e9);
  }
  .card {
      border: none;
      border-radius: 20px;
      overflow: hidden;
  }
  .bg-image {
      background-image: url('https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=800&q=80');
      background-size: cover;
      background-position: center;
  }
  .form-control:focus {
      box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
      border-color: #007bff;
  }
</style>
</head>
<body>

  <div class="container py-5">
    <div class="row justify-content-center align-items-center">
      <div class="col-lg-10">
        <div class="card shadow-lg">
          <div class="row g-0">

            <!-- Form Section -->
            <div class="col-md-6 p-5 bg-white">
              <h3 class="mb-4 text-primary text-center fw-bold">Create Account</h3>
              <form method="POST">
                <div class="mb-3">
                  <label class="form-label fw-semibold">Full Name</label>
                  <input type="text" class="form-control form-control-lg" name="name" required>
              </div>
              <div class="mb-3">
                  <label class="form-label fw-semibold">Email Address</label>
                  <input type="email" class="form-control form-control-lg" name="email" required>
              </div>
              <div class="mb-3">
                  <label class="form-label fw-semibold">Password</label>
                  <input type="password" class="form-control form-control-lg" name="password" required>
              </div>
              <button type="submit" class="btn btn-primary btn-lg w-100">Register Now</button>
          </form>
          <p class="mt-4 text-center">
            Already have an account? <a href="login.php" class="text-decoration-none">Login here</a>
        </p>
        <p class="mt-3 text-center">
          <a href="../../index.php" class="text-decoration-none text-primary">Back to Main Page</a>
        </p>
    </div>

    <!-- Image & Message Section -->
    <div class="col-md-6 bg-image d-none d-md-flex flex-column justify-content-center text-white text-center p-4">
      <h2 class="fw-bold">Welcome to Your Blogging Journey!</h2>
      <p class="lead mt-3">Share your thoughts, earn points, and unlock achievements through writing. Every word counts!</p>
      <p class="mt-3 fst-italic">"Start writing today and let your voice be heard."</p>
  </div>
</div>
</div>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
