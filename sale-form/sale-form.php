<?php

// DEFAULT VALUES
$total = 0;

// SAVE DATA
if(isset($_POST['submit'])){

    $date = $_POST['date'] ?? '';
    $item_id = $_POST['item_id'] ?? '';
    $quantity = $_POST['quantity'] ?? 0;
    $unit_price = $_POST['unit_price'] ?? 0;

    // CALCULATE TOTAL
    $total = (float)$quantity * (float)$unit_price;

    // DATABASE CONNECTION
    $conn = mysqli_connect("localhost","root","","shopflow");

    // INSERT INTO DATABASE
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $sale_id = 'SAL-' . date('Yis') . '-' . random_int(100, 999);

    $sql = "INSERT INTO sales (sale_id, date, item_id, quantity, unit_price, total_price)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'sssidd', $sale_id, $date, $item_id, $quantity, $unit_price, $total);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    echo "<script>alert('Sale Recorded Successfully');</script>";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="style.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<title>Shopflow - Record Sale</title>

<style>

/* RESET */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, Helvetica, sans-serif;
}

/* BODY */

body{
    background:#f4f4f4;
}

/* MAIN LAYOUT */

.container{
    display:flex;
    min-height:100vh;
}

/* SIDEBAR */

.sidebar{
    width:250px;
    background:#ffffff;
    border-right:1px solid #dddddd;
    padding-top:20px;
}

.logo{
    padding:20px;
}

.logo h2{
    color:#ff5a1f;
    font-size:30px;
}

.logo p{
    font-size:11px;
    color:#999999;
    letter-spacing:2px;
    margin-top:3px;
}

/* MENU */

.menu{
    margin-top:15px;
}

.menu a{
    display:block;
    text-decoration:none;
    color:#444444;
    padding:15px 20px;
    font-size:14px;
    transition:0.3s;
}

.menu a:hover{
    background:#f7f7f7;
}

.menu a.active{
    background:#ff5a1f;
    color:white;
}

/* MAIN CONTENT */

.main{
    margin-left: 230px;
    flex:1;
    padding:30px;
}

.top-title{
    font-size:28px;
    color:#333333;
    margin-bottom:25px;
}

.breadcrumb{
    font-size:12px;
    color:#999999;
    margin-bottom:10px;
}

.page-title{
    font-size:36px;
    color:#222222;
    margin-bottom:5px;
}

.subtitle{
    color:#888888;
    margin-bottom:30px;
}

/* CARD */

.card{
    background:white;
    border-radius:15px;
    padding:35px;
    width:900px;
    box-shadow:0 0 10px rgba(0,0,0,0.05);
}

/* FORM */

.form-row{
    display:flex;
    gap:20px;
    margin-bottom:25px;
}

.form-group{
    flex:1;
}

label{
    display:block;
    margin-bottom:8px;
    font-size:12px;
    color:#666666;
    text-transform:uppercase;
}

input,
select{
    width:100%;
    padding:14px;
    border:1px solid #dddddd;
    border-radius:6px;
    outline:none;
    font-size:14px;
}

/* TOTAL BOX */

.total-box{
    margin-top:10px;
    background:#f7f7f7;
    border:1px solid #e5e5e5;
    border-radius:8px;
    padding:25px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.total-left h4{
    font-size:13px;
    color:#666666;
    margin-bottom:5px;
}

.total-left p{
    font-size:12px;
    color:#999999;
}

.total-right{
    font-size:38px;
    font-weight:bold;
    color:#ff5a1f;
}

/* BUTTONS */

.buttons{
    margin-top:30px;
    display:flex;
    justify-content:flex-end;
    gap:15px;
}

.btn{
    padding:14px 28px;
    border:none;
    border-radius:7px;
    cursor:pointer;
    font-size:13px;
    font-weight:bold;
}

.discard-btn{
    background:#ebebeb;
    color:#555555;
}

.save-btn{
    background:#c94914;
    color:white;
}

.save-btn:hover{
    background:#a83d10;
}

/* RESPONSIVE */

@media(max-width:950px){

    .card{
        width:100%;
    }

    .form-row{
        flex-direction:column;
    }

}

</style>

</head>

<body>

<div class="container">

    <!-- SIDEBAR -->

    <?php
    $base_path = '../';
    $current_page = 'sale-form';
    include '../core/navigation.php';
    ?>

    <!-- MAIN CONTENT -->

    <div class="main">

        <div class="top-title">Record Sale</div>

        <div class="breadcrumb">
            DASHBOARD > NEW SALE ENTRY
        </div>

        <div class="page-title">
            New Sale
        </div>

        <div class="subtitle">
            Submit Sale details to update stock.
        </div>

        <!-- CARD -->

        <div class="card">

            <form method="POST">

                <!-- FIRST ROW -->

                <div class="form-row">

                    <div class="form-group">
                        <label>Date Of Sale</label>
                        <input type="date" name="date" required>
                    </div>

                    <div class="form-group">
                        <label>Item ID</label>

                        <input type="text" name="item_id" placeholder="ITM-001" required>

                    </div>

                </div>

                <!-- SECOND ROW -->

                <div class="form-row">

                    <div class="form-group">
                        <label>Quantity Sold</label>
                        <input type="number" id="quantity" name="quantity" placeholder="0" required>
                    </div>

                    <div class="form-group">
                        <label>Unit Price (UGX)</label>
                        <input type="number" id="cost" name="unit_price" placeholder="UGX 0" required>
                    </div>

                </div>

                <!-- TOTAL BOX -->

                <div class="total-box">

                    <div class="total-left">

                        <h4>TOTAL TRANSACTION VALUE</h4>

                        <p>
                            Calculated based on quantity and unit price.
                        </p>

                    </div>

                    <div class="total-right" id="totalDisplay">
                        UGX 0
                    </div>

                </div>

                <!-- BUTTONS -->

                <div class="buttons">

                    <button type="reset" class="btn discard-btn">
                        DISCARD
                    </button>

                    <button type="submit" name="submit" class="btn save-btn">
                        RECORD TRANSACTION
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<!-- JAVASCRIPT -->

<script>

// GET INPUTS
const quantityInput = document.getElementById("quantity");
const costInput = document.getElementById("cost");

// GET TOTAL DISPLAY
const totalDisplay = document.getElementById("totalDisplay");

// CALCULATE TOTAL FUNCTION
function calculateTotal(){

    let quantity = quantityInput.value;
    let cost = costInput.value;

    // EMPTY VALUES
    if(quantity === ""){
        quantity = 0;
    }

    if(cost === ""){
        cost = 0;
    }

    // TOTAL
    let total = quantity * cost;

    // DISPLAY TOTAL
    totalDisplay.innerHTML = "UGX " + total.toLocaleString();

}

// EVENT LISTENERS
quantityInput.addEventListener("input", calculateTotal);
costInput.addEventListener("input", calculateTotal);

</script>

</body>
</html>