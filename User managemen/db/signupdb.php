<?php
session_start();
include "dbConnection.php";

// Sanitize inputs
$fullname = filter_input(INPUT_POST, 'fullname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$userName = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$specialization = filter_input(INPUT_POST, 'specialization', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$region = filter_input(INPUT_POST, 'region', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$country = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$bio = filter_input(INPUT_POST, 'bio', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$business_name = filter_input(INPUT_POST, 'business_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$years_of_ex = filter_input(INPUT_POST, 'years_of_ex', FILTER_SANITIZE_NUMBER_INT);
$location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// Validate required fields
if (empty($fullname) || empty($userName) || empty($email) || empty($role) || empty($password)) {
    $_SESSION['notregistered'] = "All required fields must be filled";
    header("Location: ../sign.php");
    exit();
}

// Validate password
if (strlen($password) < 6) {
    $_SESSION['notregistered'] = "Password must be at least 6 characters";
    header("Location: ../sign.php");
    exit();
}

if ($password !== $confirm_password) {
    $_SESSION['notregistered'] = "Passwords do not match";
    header("Location: ../sign.php");
    exit();
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['notregistered'] = "Invalid email format";
    header("Location: ../sign.php");
    exit();
}

// Validate username
if (!preg_match('/^[a-zA-Z0-9]{4,}$/', $userName)) {
    $_SESSION['notregistered'] = "Username must be at least 4 alphanumeric characters";
    header("Location: ../sign.php");
    exit();
}

// Validate fullname
if (!preg_match('/^[a-zA-Z ]{3,}$/', $fullname)) {
    $_SESSION['notregistered'] = "Full name must be at least 3 letters";
    header("Location: ../sign.php");
    exit();
}

// Role-specific validation
if ($role === 'customer' && empty($country)) {
    $_SESSION['notregistered'] = "Country is required for customers";
    header("Location: ../sign.php");
    exit();
}

if (($role === 'artisan' || $role === 'storyteller') && (empty($specialization) || empty($region))) {
    $_SESSION['notregistered'] = "Specialization and region are required for artisans/storytellers";
    header("Location: ../sign.php");
    exit();
}

// Handle profile image upload
$profileImage = null;
$hasImage = false;
$profileImagePath = null;

if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['profileImage'];
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $fileType = mime_content_type($file['tmp_name']);
    
    if (!in_array($fileType, $allowedTypes)) {
        $_SESSION['notregistered'] = "Only JPG, PNG, and GIF images are allowed";
        header("Location: ../sign.php");
        exit();
    }
    
    // Check file size (max 2MB)
    if ($file['size'] > 2 * 1024 * 1024) {
        $_SESSION['notregistered'] = "Profile image must be less than 2MB";
        header("Location: ../sign.php");
        exit();
    }
    
    // Generate unique filename
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $ext;
    $uploadDir = '../uploads/profiles/';
    
    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $destination = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        $profileImagePath = 'uploads/profiles/' . $filename;
        $hasImage = true;
    } else {
        $_SESSION['notregistered'] = "Error uploading profile image";
        header("Location: ../sign.php");
        exit();
    }
}

// Check if email or username already exists
$checkSql = "SELECT * FROM users WHERE email = ? OR username = ?";
$checkStmt = $con->prepare($checkSql);
$checkStmt->bind_param("ss", $email, $userName);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows > 0) {
    $_SESSION['notregistered'] = "Email or username already exists";
    header("Location: ../sign.php");
    exit();
}

// Hash password
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Set region based on role
if ($role == 'customer') {
    $region = $country;
} else {
    $region = 'Oromia';
}

// Insert into users table
if ($hasImage) {
    $sql = "INSERT INTO users (fullname, username, email, country, password, role, region, specialization, profileImage) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("sssssssss", $fullname, $userName, $email, $country, $passwordHash, $role, $region, $specialization, $profileImagePath);
} else {
    $sql = "INSERT INTO users (fullname, username, email, country, password, role, region, specialization) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ssssssss", $fullname, $userName, $email, $country, $passwordHash, $role, $region, $specialization);
}

if ($stmt->execute()) {
    $user_id = $stmt->insert_id;
    
    $pending ="pending";
    // Insert into role-specific tables
    if ($role == 'artisan' ) {

    
        $artisanSql = "INSERT INTO artisans (user_id, business_name, bio,verification_status ) 
                      VALUES (?, ?, ?, ?)";
        $artisanStmt = $con->prepare($artisanSql);
        $artisanStmt->bind_param("issssiss", $user_id, $business_name, $bio,  $years_of_ex, $pending);
        $artisanStmt->execute();
    } elseif ($role === 'storyteller') {
        $storytellerSql = "INSERT INTO storytellers (user_id, artistic_name, bio, location, specialization, verification_status, profile_image_url) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)";
        $storytellerStmt = $con->prepare($storytellerSql);
        $verification_status = 'pending';
        $storytellerStmt->bind_param("issssss", $user_id, $fullname, $bio, $location, $specialization, $verification_status, $profileImagePath);
        $storytellerStmt->execute();
    }
    
    $_SESSION['registered'] = "You have successfully registered";
    header("Location: ../login.php");
    exit();
} else {
    $_SESSION['notregistered'] = "Registration failed: " . $stmt->error;
    header("Location: ../sign.php");
    exit();
}