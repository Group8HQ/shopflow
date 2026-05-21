<?php

$host = 'localhost';
$username = 'root';
$password = 'password';
$db = "shopflow";

$conn = new mysqli($host, $username, $password, $db);

if ($conn->connect_error) {
    echo "Failed to connect to database";
    exit;
}

$total_items = 0;
$total_purchases = 0;
$total_sales = 0;

$total_sales_revenue = 0;
$total_purchase_cost = 0;

$total_items_query = "SELECT COUNT(*) FROM ITEMS";

?>