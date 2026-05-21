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

$total_items = $conn->query("SELECT COUNT(*) as count FROM items")->fetch_assoc()['count'] ?? 0;
$total_purchases = $conn->query("SELECT COUNT(*) as count FROM purchases")->fetch_assoc()['count'] ?? 0;
$total_sales = $conn->query("SELECT COUNT(*) as count FROM sales")->fetch_assoc()['count'] ?? 0;
$total_sales_revenue = $conn->query("SELECT COALESCE(SUM(total_price), 0) as total FROM sales")->fetch_assoc()['total'] ?? 0;
$total_purchase_cost = $conn->query("SELECT COALESCE(SUM(total_cost), 0) as total FROM purchases")->fetch_assoc()['total'] ?? 0;


$recent_purchases = $conn->query("
    SELECT purchases.date, purchases.quantity, purchases.unit_cost, purchases.total_cost, items.name 
    FROM purchases
    JOIN items ON purchases.item_id = items.item_id 
    ORDER BY purchases.date DESC, purchases.id DESC 
    LIMIT 5
");

$recent_sales = $conn->query("
    SELECT sales.date, sales.quantity, sales.unit_price, sales.total_price, items.name 
    FROM sales
    JOIN items ON sales.item_id = items.item_id 
    ORDER BY sales.date DESC, sales.id DESC 
    LIMIT 5
");


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Shopflow</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <?php
    $base_path = '../';
    $current_page = 'dashboard';
    include '../core/navigation.php';
    ?>

    <main class="main-content">
        <header class="top-bar">
            <h2>Dashboard</h2>
            <div class="help-icon">?</div>
        </header>

        <section class="overview">
            <div class="overview-header">
                <div>
                    <h3>Overview</h3>
                    <p>Track the performance of your shop.</p>
                </div>
            </div>

            <div class="kpi-cards">
                <div class="kpi-card">
                    <div class="kpi-header">
                        <span class="kpi-label">TOTAL ITEMS</span>
                        <i class="fa-solid fa-boxes-stacked kpi-icon orange"></i>
                    </div>
                    <div class="kpi-value"><?php echo number_format($total_items); ?></div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-header">
                        <span class="kpi-label">TOTAL PURCHASES</span>
                        <i class="fa-solid fa-cart-shopping kpi-icon blue"></i>
                    </div>
                    <div class="kpi-value"><?php echo number_format($total_purchases); ?></div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-header">
                        <span class="kpi-label">TOTAL SALES</span>
                        <i class="fa-solid fa-receipt kpi-icon purple"></i>
                    </div>
                    <div class="kpi-value"><?php echo number_format($total_sales); ?></div>
                </div>
                <div class="kpi-card kpi-revenue">
                    <div class="kpi-header">
                        <span class="kpi-label">SALES REVENUE</span>
                        <i class="fa-solid fa-arrow-trend-up kpi-icon"></i>
                    </div>
                    <div class="kpi-value">Shs <?php echo number_format($total_sales_revenue); ?></div>
                </div>
                <div class="kpi-card kpi-cost">
                    <div class="kpi-header">
                        <span class="kpi-label">PURCHASES COST</span>
                        <i class="fa-solid fa-arrow-trend-down kpi-icon"></i>
                    </div>
                    <div class="kpi-value">Shs <?php echo number_format($total_purchase_cost); ?></div>
                </div>
            </div>
        </section>

        <section class="recent-activity">
            <h3>Recent Activity</h3>
            <div class="activity-tables">
                <div class="activity-table">
                    <div class="table-header">
                        <h4>Recent Purchases</h4>
                        <a href="../purchase-list/purchase-list.html" class="view-all">View All</a>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>PRODUCT</th>
                                <th>QTY | UNIT</th>
                                <th>TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recent_purchases && $recent_purchases->num_rows > 0): ?>
                                <?php while ($row = $recent_purchases->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><span class="qty-badge"><?php echo $row['quantity']; ?> | <?php echo number_format($row['unit_cost']); ?></span></td>
                                    <td>Shs <?php echo number_format($row['total_cost']); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="empty">No purchases yet</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="activity-table">
                    <div class="table-header">
                        <h4>Recent Sales</h4>
                        <a href="../sale-list/sale-list.html" class="view-all">View All</a>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>PRODUCT</th>
                                <th>QTY | UNIT</th>
                                <th>TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recent_sales && $recent_sales->num_rows > 0): ?>
                                <?php while ($row = $recent_sales->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><span class="qty-badge"><?php echo $row['quantity']; ?> | <?php echo number_format($row['unit_price']); ?></span></td>
                                    <td>Shs <?php echo number_format($row['total_price']); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="empty">No sales yet</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
<?php $conn->close(); ?>