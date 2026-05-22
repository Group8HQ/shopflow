<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.html');
    exit;
}

$host = 'localhost';
$username = 'root';
$password = '';
$db = "shopflow";

$conn = new mysqli($host, $username, $password, $db);

if ($conn->connect_error) {
    echo "Failed to connect to database";
    exit;
}

if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $sale_id = $conn->real_escape_string($_GET['delete']);
    $conn->query("DELETE FROM sales WHERE sale_id = '$sale_id'");
    header('Location: sale-list.php');
    exit;
}

$total_revenue = $conn->query("SELECT COALESCE(SUM(total_price), 0) as total FROM sales")->fetch_assoc()['total'] ?? 0;
$total_items_sold = $conn->query("SELECT COALESCE(SUM(quantity), 0) as total FROM sales")->fetch_assoc()['total'] ?? 0;
$total_sales = $conn->query("SELECT COUNT(*) as count FROM sales")->fetch_assoc()['count'] ?? 0;

$sales = $conn->query("
    SELECT sales.sale_id, sales.date, sales.item_id, sales.quantity, sales.unit_price, sales.total_price, items.name 
    FROM sales
    JOIN items ON sales.item_id = items.item_id 
    ORDER BY sales.date DESC, sales.id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales - Shopflow</title>
    <link rel="stylesheet" href="../dashboard/style.css">
    <link rel="stylesheet" href="sale-list.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <?php
    $base_path = '../';
    $current_page = 'sale-list';
    include '../core/navigation.php';
    ?>

    <main class="main-content">
        <header class="top-bar">
            <h2>Sales</h2>
            <div class="help-icon">?</div>
        </header>

        <section class="overview">
            <div class="kpi-cards kpi-cards--two">
                <div class="kpi-card kpi-revenue">
                    <div class="kpi-header">
                        <i class="fa-solid fa-arrow-trend-up kpi-icon"></i>
                        <span class="kpi-badge">+12.5%</span>
                    </div>
                    <span class="kpi-label">REVENUE</span>
                    <div class="kpi-value">UGX <?php echo number_format($total_revenue); ?></div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-header">
                        <i class="fa-solid fa-box kpi-icon blue"></i>
                    </div>
                    <span class="kpi-label">ITEMS SOLD</span>
                    <div class="kpi-value"><?php echo number_format($total_items_sold); ?> Units</div>
                </div>
            </div>
        </section>

        <section class="transaction-section">
            <h3>Transaction History</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>DATE & TIME</th>
                            <th>ITEM ID</th>
                            <th>QUANTITY SOLD</th>
                            <th>SELLING PRICE (UGX)</th>
                            <th>TOTAL PRICE (UGX)</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($sales && $sales->num_rows > 0): ?>
                            <?php while ($row = $sales->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($row['date'])); ?></td>
                                <td><span class="item-id">#<?php echo htmlspecialchars($row['item_id']); ?></span></td>
                                <td class="quantity"><?php echo number_format($row['quantity']); ?></td>
                                <td class="unit-price"><?php echo number_format($row['unit_price']); ?></td>
                                <td class="total-price"><strong><?php echo number_format($row['total_price']); ?></strong></td>
                                <td>
                                    <a href="sale-list.php?delete=<?php echo urlencode($row['sale_id']); ?>" class="action-link delete" onclick="return confirm('Are you sure you want to delete this sale?');">DELETE</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="empty">No sales yet</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="pagination-container">
                <span class="pagination-info">Showing 1-100 of <?php echo number_format($total_sales); ?> entries</span>
                <div class="pagination">
                    <button class="page-btn" disabled><i class="fa-solid fa-chevron-left"></i></button>
                    <button class="page-btn active">1</button>
                    <button class="page-btn"><i class="fa-solid fa-chevron-right"></i></button>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
<?php $conn->close(); ?>
