<?php 
session_start();
include './db/dbConnection.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Login Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
  .form-box {
    max-width: 400px;
    margin: 40px auto;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 8px;
    background: #fff;
  }

  .register-link {
    margin-top: 15px;
    text-align: center;
  }

  .invalid-feedback {
    display: none;
    color: #dc3545;
    font-size: 0.875em;
  }

  .is-invalid {
    border-color: #dc3545 !important;
  }

  .forgot-password {
    text-align: right;
    margin-bottom: 15px;
  }
  </style>
</head>

<body class="bg-light">
  <div class="form-box shadow">
    <form id="loginForm" method="POST" action="./db/login.php" novalidate>
      <?php 
      if (isset($_SESSION['login_error'])) {
        echo '<div class="alert alert-danger text-center" role="alert">'
        . '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="red" class="bi bi-x-circle me-2" viewBox="0 0 16 16">
        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14z"/>
        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
          </svg>'
        . htmlspecialchars($_SESSION['login_error']) .
        '</div>';
        unset($_SESSION['login_error']);
      }
      if (isset($_SESSION['login_success'])) {
        echo '<div class="alert alert-success text-center" role="alert">'
        . '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="green" class="bi bi-check-circle me-2" viewBox="0 0 16 16">
        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14z"/>
        <path d="M10.97 5.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 9.384a.75.75 0 1 1 1.06-1.06l1.094 1.093 3.492-4.438z"/>
          </svg>'
        . htmlspecialchars($_SESSION['login_success']) .
        '</div>';
        unset($_SESSION['login_success']);
      }
      ?>
      <h1 class="mb-4 text-center">Login Now</h1>

      <div class="mb-3">
        <input type="email" name="email" class="form-control" placeholder="Email" id="email" required
          pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
        <div class="invalid-feedback">Please enter a valid email address</div>
      </div>

      <div class="mb-3">
        <input type="password" name="password" class="form-control" placeholder="Password (min 6 chars)" required
          minlength="6">
        <div class="invalid-feedback">Password must be at least 6 characters</div>
      </div>

      <div class="forgot-password">
        <a href="forgot_password.php">Forgot Password?</a>
      </div>

      <button type="submit" class="btn btn-success w-100">Login</button>
      <div class="register-link">
        <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
      </div>
    </form>
  </div>

  <script src="./js/loginValidation.js">

  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>