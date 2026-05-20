<?php

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'shopflow';

$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}

session_start();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($email === '' || $password === '') {
        $msg = 'Email and password are required.';
        header('Location: login.html?msg=' . urlencode($msg));
        exit;
    }

    $sql = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            header('Location: ../dashboard/dashboard.html');
            exit;
        }
    }

    $msg = 'Invalid email or password.';
    header('Location: login.html?msg=' . urlencode($msg));
    exit;
}
?>
