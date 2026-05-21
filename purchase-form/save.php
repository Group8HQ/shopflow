
<?php
$conn = mysqli_connect("localhost", "root", "", "shopflow");

$purchase_id = $_POST['purchase_id'];
$date        = $_POST['date'];
$item_id     = $_POST['item_id'];
$quantity    = (int)   $_POST['quantity'];
$unit_cost   = (float) $_POST['unit_cost'];
$total_cost  = (float) ($_POST['quantity'] * $_POST['unit_cost']);

$query = "INSERT INTO purchases(purchase_id, date, item_id, quantity, unit_cost, total_cost)
          VALUES('$purchase_id', '$date', '$item_id', '$quantity', '$unit_cost', '$total_cost')";


$querry = mysqli_query($conn, $query);

header("Location: ./purchase.html");

mysqli_close($conn);
?>