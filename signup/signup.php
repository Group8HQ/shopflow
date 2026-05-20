<?php

$host   = 'localhost';
$dbname = 'shopflow';
$user   = 'root';
$pass   = '';
 

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: signup.html');
    exit;
}
 

$full_name        = trim($_POST['full_name']        ?? '');
$email            = trim($_POST['email']            ?? '');
$password         = trim($_POST['password']         ?? '');
$confirm_password = trim($_POST['confirm_password'] ?? '');
$agree            = isset($_POST['agree']);
 

if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
    echo "<script>alert('All fields are required.'); window.history.back();</script>";
    exit;
}
 
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Please enter a valid email address.'); window.history.back();</script>";
    exit;
}
 
if ($password !== $confirm_password) {
    echo "<script>alert('Passwords do not match.'); window.history.back();</script>";
    exit;
}
 
if (strlen($password) < 8) {
    echo "<script>alert('Password must be at least 8 characters.'); window.history.back();</script>";
    exit;
}
 
if (!$agree) {
    echo "<script>alert('You must agree to the Terms of Service and Privacy Policy.'); window.history.back();</script>";
    exit;
}
 

$conn = new mysqli($host, $user, $pass, $dbname);
 
if ($conn->connect_error) {
    echo "<script>alert('DB connection failed: " . addslashes($conn->connect_error) . "'); window.history.back();</script>";
    exit;
}
 

$check = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$check->bind_param('s', $email);
$check->execute();
$check->store_result();
 
if ($check->num_rows > 0) {
    echo "<script>alert('An account with that email already exists.'); window.history.back();</script>";
    $check->close();
    $conn->close();
    exit;
}
$check->close();
 

$hashed_password = password_hash($password, PASSWORD_BCRYPT);
 

$stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param('sss', $full_name, $email, $hashed_password);
 
if ($stmt->execute()) {
    $msg = urlencode('✅ Account for ' . $full_name . ' created successfully!');
    header('Location: signup.html?msg=' . $msg);
} else {
    echo "<script>alert('Registration failed: " . addslashes($stmt->error) . "'); window.history.back();</script>";
}
 
$stmt->close();
$conn->close();
?>