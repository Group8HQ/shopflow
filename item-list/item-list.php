<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.html');
    exit;
}

$host = 'localhost';
$username = 'root';
$password = '';
$db = 'shopflow';

$conn = new mysqli($host, $username, $password, $db);

if ($conn->connect_error) {
    echo 'Failed to connect to database';
    exit;
}

$message = '';

if (isset($_GET['delete']) && $_GET['delete'] !== '') {
    $item_id = $_GET['delete'];

    $stmt = $conn->prepare('DELETE FROM items WHERE item_id = ?');
    if ($stmt) {
        $stmt->bind_param('s', $item_id);
        $ok = $stmt->execute();
        $stmt->close();

        if (!$ok) {
            $message = 'Failed to delete item.';
        }
    } else {
        $message = 'Failed to delete item.';
    }

    header('Location: item-list.php' . ($message ? ('?msg=' . urlencode($message)) : ''));
    exit;
}

if (isset($_GET['msg'])) {
    $message = htmlspecialchars($_GET['msg']);
}

$total_items = $conn->query('SELECT COUNT(*) as count FROM items')->fetch_assoc()['count'] ?? 0;
$total_suppliers = $conn->query("SELECT COUNT(DISTINCT supplier) as count FROM items")->fetch_assoc()['count'] ?? 0;

$items = $conn->query('SELECT item_id, name, supplier, description FROM items ORDER BY id DESC');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Items - Shopflow</title>
    <link rel="stylesheet" href="../dashboard/style.css">
    <link rel="stylesheet" href="item-list.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <?php
    $base_path = '../';
    $current_page = 'item-list';
    include '../core/navigation.php';
    ?>

    <main class="main-content">
        <header class="top-bar">
            <h2>All Items</h2>
            <div class="help-icon">?</div>
        </header>

        <section class="overview">
            <div class="kpi-cards kpi-cards--two">
                <div class="kpi-card">
                    <div class="kpi-header">
                        <i class="fa-solid fa-boxes-stacked kpi-icon orange"></i>
                    </div>
                    <span class="kpi-label">TOTAL ITEMS</span>
                    <div class="kpi-value"><?php echo number_format($total_items); ?></div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-header">
                        <i class="fa-solid fa-truck-fast kpi-icon blue"></i>
                    </div>
                    <span class="kpi-label">SUPPLIERS</span>
                    <div class="kpi-value"><?php echo number_format($total_suppliers); ?></div>
                </div>
            </div>
        </section>

        <section class="transaction-section">
            <h3>Item Directory</h3>

            <?php if ($message): ?>
                <div class="list-alert list-alert--error"><?php echo $message; ?></div>
            <?php endif; ?>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ITEM ID</th>
                            <th>NAME</th>
                            <th>SUPPLIER</th>
                            <th>DESCRIPTION</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($items && $items->num_rows > 0): ?>
                            <?php while ($row = $items->fetch_assoc()): ?>
                            <tr>
                                <td><span class="item-id">#<?php echo htmlspecialchars($row['item_id']); ?></span></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['supplier'] ?? ''); ?></td>
                                <td class="desc"><?php echo htmlspecialchars($row['description'] ?? ''); ?></td>
                                <td>
                                    <a href="item-list.php?delete=<?php echo urlencode($row['item_id']); ?>" class="action-link delete" onclick="return confirm('Are you sure you want to delete this item?');">DELETE</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="empty">No items yet</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="pagination-container">
                <span class="pagination-info">Showing 1-100 of <?php echo number_format($total_items); ?> entries</span>
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
