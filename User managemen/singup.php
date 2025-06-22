<?php 
session_start();
include './db/dbConnection.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Registration Page</title>
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

  .profile-preview {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    margin: 0 auto 15px;
    display: block;
    border: 2px solid #ddd;
  }

  .profile-upload {
    text-align: center;
    margin-bottom: 20px;
  }

  .profile-upload label {
    cursor: pointer;
    display: inline-block;
    padding: 6px 12px;
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
    border-radius: 4px;
  }

  .profile-upload label:hover {
    background-color: #e9ecef;
  }

  #profileInput {
    display: none;
  }

  .invalid-feedback {
    display: none;
    color: #dc3545;
    font-size: 0.875em;
  }

  .is-invalid {
    border-color: #dc3545 !important;
  }
  </style>
</head>

<body class="bg-light">
  <div class="form-box shadow">
    <form id="registrationForm" method="POST" action="./db/signupdb.php" enctype="multipart/form-data" novalidate>
      <?php 
      if (isset($_SESSION['registered'])) {
        echo '<div class="alert alert-success text-center" role="alert">'
        . '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="green" class="bi bi-check-circle me-2" viewBox="0 0 16 16">
        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14z"/>
        <path d="M10.97 5.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 9.384a.75.75 0 1 1 1.06-1.06l1.094 1.093 3.492-4.438z"/>
          </svg>'
        . htmlspecialchars($_SESSION['registered']) .
        '</div>';
        unset($_SESSION['registered']);
      } elseif (isset($_SESSION['notregistered'])) {
        echo '<div class="alert alert-danger text-center" role="alert">'
        . '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="red" class="bi bi-x-circle me-2" viewBox="0 0 16 16">
        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14z"/>
        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
          </svg>'
        . htmlspecialchars($_SESSION['notregistered']) .
        '</div>';
        unset($_SESSION['notregistered']);
      }
      ?>
      <h1 class="mb-4 text-center">Register</h1>

      <div class="profile-upload">
        <img id="profilePreview" src="https://via.placeholder.com/100" alt="Profile Preview" class="profile-preview">
        <label for="profileInput">Choose Profile Image</label>
        <input type="file" name="profileImage" id="profileInput" accept="image/*">
        <div class="invalid-feedback" id="profileImageError">Please select a valid image file (JPG, PNG, GIF)</div>
      </div>

      <div class="mb-3">
        <input type="text" name="fullname" class="form-control" placeholder="Enter your FullName" required
          pattern="[A-Za-z ]{3,}" title="Full name must be at least 3 characters and contain only letters">
        <div class="invalid-feedback">Please enter a valid full name (at least 3 characters, letters only)</div>
      </div>
      <div class="mb-3">
        <input type="text" name="username" class="form-control" placeholder="Username" required
          pattern="[A-Za-z0-9]{4,}" title="Username must be at least 4 characters (letters and numbers only)">
        <div class="invalid-feedback">Username must be at least 4 characters (letters and numbers only)</div>
      </div>
      <div class="mb-3">
        <input type="email" name="email" class="form-control" placeholder="Email" required>
        <div class="invalid-feedback">Please enter a valid email address</div>
      </div>
      <div class="mb-3">
        <select name="role" class="form-select" id="roleSelect" required>
          <option value="" disabled selected>Select Role</option>
          <option value="customer">Customer</option>
          <option value="artisan">Artisan/Storyteller</option>
          <option value="storyteller">Storyteller</option>
        </select>
        <div class="invalid-feedback">Please select a role</div>
      </div>

      <!-- Customer country -->
      <div class="mb-3" id="customerFields" style="display:none;">
        <input type="text" name="country" class="form-control" placeholder="Country" pattern="[A-Za-z ]{2,}"
          title="Country must be at least 2 characters">
        <div class="invalid-feedback">Please enter a valid country name</div>
      </div>

      <!-- Artisan/Storyteller fields -->
      <div id="artisanFields" style="display:none;">
        <div class="mb-3">
          <input type="text" name="specialization" class="form-control" placeholder="Specialization"
            pattern="[A-Za-z ]{3,}" title="Specialization must be at least 3 characters">
          <div class="invalid-feedback">Please enter a valid specialization</div>
        </div>
        <div class="mb-3">
          <input type="text" name="region" class="form-control" placeholder="Region in Oromia" pattern="[A-Za-z ]{3,}"
            title="Region must be at least 3 characters">
          <div class="invalid-feedback">Please enter a valid region</div>
        </div>
      </div>

      <div class="mb-3">
        <input type="password" name="password" id="password" class="form-control" placeholder="Password (min 6 chars)"
          required minlength="6">
        <div class="invalid-feedback">Password must be at least 6 characters</div>
      </div>
      <div class="mb-3">
        <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
        <div class="invalid-feedback">Passwords must match</div>
      </div>
      <button type="submit" class="btn btn-success w-100">Register</button>
      <div class="register-link">
        <p>Already have an account? <a href="login.php">Login</a></p>
      </div>
    </form>
  </div>

  <script src="./js/signupValidation.js">

  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>