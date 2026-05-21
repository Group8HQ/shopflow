<?php
// profile_db.php — Database connection + profile queries

$host   = "localhost";
$user   = "root";
$pass   = "";
$dbname = "shopflow";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

/**
 * Fetch a single user by ID.
 *
 * @param  mysqli $conn
 * @param  int    $user_id
 * @return array|null  Associative row or null if not found.
 */
function getUserById($conn, $user_id) {
    $stmt = mysqli_prepare($conn, "SELECT id, name, email, password FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row    = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $row;
}

/**
 * Update a user's full name and email.
 *
 * @return true|string  true on success, error message string on failure.
 */
function updateUserInfo($conn, $user_id, $name, $email) {
    // Check email uniqueness (exclude current user)
    $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? AND id != ?");
    mysqli_stmt_bind_param($check, "si", $email, $user_id);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);

    if (mysqli_stmt_num_rows($check) > 0) {
        mysqli_stmt_close($check);
        return "That email is already in use by another account.";
    }
    mysqli_stmt_close($check);

    $stmt = mysqli_prepare($conn, "UPDATE users SET name = ?, email = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ssi", $name, $email, $user_id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $ok ? true : "Failed to update profile. Please try again.";
}

/**
 * Update a user's password (hashed).
 *
 * @return true|string
 */
function updateUserPassword($conn, $user_id, $new_password) {
    $hashed = password_hash($new_password, PASSWORD_BCRYPT);
    $stmt   = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $hashed, $user_id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $ok ? true : "Failed to update password. Please try again.";
}
