<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "shopflow";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_id = mysqli_real_escape_string($conn, $_POST['item_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $supplier = mysqli_real_escape_string($conn, $_POST['supplier']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $sql = "INSERT INTO items (item_id, name, supplier, description)
            VALUES ('$item_id', '$name', '$supplier', '$description')";

    if (mysqli_query($conn, $sql)) {
        echo "<p style='color:green;'>Item saved successfully!</p>";
        header("Location: item-form.html");
    } else {
        echo "<p style='color:red;'>Error: " . mysqli_error($conn) . "</p>";
    }
}

mysqli_close($conn);
?>