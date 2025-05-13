<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Earn Points & Get Reward</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #dfe9f3, #ffffff);
      min-height: 100vh;
      font-family: 'Segoe UI', sans-serif;
      color: #0a1f44;
      text-align: center;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      position: relative;
    }

    .main-content {
      padding-top: 60px;
    }

    .moving-title {
      font-size: 2.5rem;
      font-weight: bold;
      opacity: 0;
      animation: slideDown 2s ease-out forwards, fadeColor 2s ease-out forwards;
    }

    @keyframes slideDown {
      0% {
        transform: translateY(-100px);
        opacity: 0;
      }
      100% {
        transform: translateY(0);
        opacity: 1;
      }
    }

    @keyframes fadeColor {
      0% {
        color: #bbbbbb;
      }
      100% {
        color: #0a1f44;
      }
    }

    .subtitle {
      font-family: 'Poppins', sans-serif;
      font-size: 1.3rem;
      font-weight: 500;
      letter-spacing: 1px;
      margin-top: 1rem;
      background: linear-gradient(to right, #0066ff, #0099cc);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      opacity: 0;
      animation: fadeIn 2s ease-in forwards;
      animation-delay: 2s;
    }

    .tagline {
      margin-top: 2rem;
      font-size: 1rem;
      color: #3c4963;
      animation: fadeIn 2s ease-in forwards;
      animation-delay: 3s;
    }

    @keyframes fadeIn {
      to { opacity: 1 }
    }

    .btn-group {
      margin-top: 2.5rem;
      animation: fadeIn 2s ease-in forwards;
      animation-delay: 4s;
    }

    .btn {
      padding: 0.75rem 1.5rem;
      font-size: 1rem;
      margin: 0.5rem;
      border-radius: 30px;
    }

    .illustration {
      max-width: 300px;
      margin: 2rem auto 1rem;
    }

    footer {
      padding: 1rem;
      font-size: 0.9rem;
      color: #6c757d;
    }

    /* Admin Login Button Styling (Top Right) */
    .admin-login-btn {
      position: absolute;
      top: 20px;
      right: 20px;
      z-index: 1000;
      padding: 0.75rem 1.5rem;
      background-color: #dc3545;
      color: white;
      font-size: 1rem;
      border-radius: 30px;
      text-decoration: none;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .admin-login-btn:hover {
      background-color: #c82333;
      text-decoration: none;
    }
  </style>
</head>
<body>

  <!-- Admin Login Button positioned at top-right -->
  <a href="admin/login.php" class="admin-login-btn">Admin Login</a>

  <div class="main-content">
    <div class="moving-title">Earn Points & Get Reward Website</div>
    <div class="subtitle">
      Earn points by writing blogs and unlock badges like Pro, Expert, and Legend.
    </div>
    <div class="tagline">Motivate your creativity. Build your writing journey. Get recognized!</div>

    <!-- Illustration -->
    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Writing Illustration" class="illustration">

    <div class="btn-group">
      <a href="user/auth/register.php" class="btn btn-primary shadow-sm">Create an Account</a>
      <a href="user/auth/login.php" class="btn btn-outline-primary shadow-sm">Login</a>
    </div>
  </div>

  <footer>
    &copy; <?php echo date("Y"); ?> Earn Points & Get Reward. All rights reserved.
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
