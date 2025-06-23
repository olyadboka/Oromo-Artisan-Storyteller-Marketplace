<?php
// Make sure this is the VERY FIRST LINE with no whitespace before
ob_start(); // Start output buffering
session_start();
include "dbConnection.php";

function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Validate and sanitize input
$email = cleanInput($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validate inputs
if (empty($email)) {
    $_SESSION['login_error'] = "Email is required";
    header("Location: ../../../login.php"); 
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['login_error'] = "Invalid email format";
    header("Location: ../../../login.php");
    exit();
}

if (empty($password)) {
    $_SESSION['login_error'] = "Password is required";
    header("Location: ../../../login.php");
    exit();
}

// Prepare SQL statement to get user
$sql = "SELECT id, username, email, password, role, status, specialization FROM users WHERE email = ?";
$stmt = $con->prepare($sql);

if (!$stmt) {
    $_SESSION['login_error'] = "Database error";
    header("Location: ../../../login.php");
    exit();
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['login_error'] = "Invalid email or password";
    header("Location: ../../../login.php");
    exit();
}

$user = $result->fetch_assoc();

// Verify password
if (!password_verify($password, $user['password'])) {
    $_SESSION['login_error'] = "Invalid email or password";
    header("Location: ../../../login.php");
    exit();
}

// Set session variables
$_SESSION['user_id'] = $user['id'];
$_SESSION['specialization'] = $user['specialization'];
$_SESSION['username'] = $user['username'];
$_SESSION['email'] = $user['email'];
$_SESSION['role'] = $user['role'];
$_SESSION['logged_in'] = true;

session_regenerate_id(true);

// Redirect based on role
switch ($user['role']) {
    case 'admin':
        header("Location: ../../../../Admin Panel/adminDashboard.php");
        break;
    case 'artisan':
        header("Location: ../../../../Artisan and Story teller/artisan.php");
        break;
    case 'storyteller':
        header("Location: ../../../../Artisan and Story teller/storryteller/storytellers.php");
        break;
    case 'customer':
        header("Location: ../../../../Customer dashboard/customer.php");
        break;
    default:
        header("Location: ../../../../index.php");
}
exit(); 