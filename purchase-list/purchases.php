<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.html');
    exit;
}

$host    = 'localhost';
$db_name = 'shopflow';
$user    = 'root';
$pass    = '';

// ─── Pagination Settings ──────────────────────────────────────────────────────
$records_per_page = 5;

// ─── Connect to Database ──────────────────────────────────────────────────────
function getDB() {
    global $host, $db_name, $user, $pass;

    static $conn = null;

    if ($conn == null) {
        $conn = mysqli_connect($host, $user, $pass, $db_name);

        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        mysqli_set_charset($conn, 'utf8mb4');
    }

    return $conn;
}

// ─── Fetch Summary Stats ──────────────────────────────────────────────────────
function fetchSummary() {
    $conn = getDB();

    $sql    = "SELECT COALESCE(SUM(total_cost), 0) AS total_purchases, COALESCE(SUM(quantity), 0) AS total_units FROM purchases";
    $result = mysqli_query($conn, $sql);
    $row    = mysqli_fetch_assoc($result);

    return $row;
}

// ─── Fetch Paginated Purchase Records ────────────────────────────────────────
function fetchPurchases($page) {
    global $records_per_page;

    $conn   = getDB();
    $limit  = $records_per_page;
    $offset = ($page - 1) * $limit;

    $sql  = "SELECT p.id, p.date, p.item_id, p.quantity, p.unit_cost, p.total_cost FROM purchases p ORDER BY p.date DESC LIMIT ? OFFSET ?";
    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, 'ii', $limit, $offset);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $purchases = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $purchases[] = $row;
    }

    return $purchases;
}

// ─── Count Total Records (for pagination) ────────────────────────────────────
function fetchTotalRecords() {
    $conn = getDB();

    $sql    = "SELECT COUNT(*) AS total FROM purchases";
    $result = mysqli_query($conn, $sql);
    $row    = mysqli_fetch_assoc($result);

    return (int) $row['total'];
}

// ─── Handle Delete Action ─────────────────────────────────────────────────────
function deletePurchase($id) {
    $conn = getDB();

    $sql  = "DELETE FROM purchases WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
}

// ─── Process POST Actions ─────────────────────────────────────────────────────
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['delete_id']) && is_numeric($_POST['delete_id'])) {
        deletePurchase((int) $_POST['delete_id']);
        $message = 'Purchase record deleted successfully.';
    }

    $redirectPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    header("Location: purchases.php?page=$redirectPage&msg=" . urlencode($message));
    exit;
}

// ─── Gather Data ─────────────────────────────────────────────────────────────
$currentPage = 1;
if (isset($_GET['page']) && is_numeric($_GET['page'])) {
    $currentPage = (int) $_GET['page'];
    if ($currentPage < 1) {
        $currentPage = 1;
    }
}

$totalRecords = fetchTotalRecords();
$totalPages   = ceil($totalRecords / $records_per_page);

if ($totalPages < 1) {
    $totalPages = 1;
}

if ($currentPage > $totalPages) {
    $currentPage = $totalPages;
}

$purchases    = fetchPurchases($currentPage);
$summary      = fetchSummary();
$flashMessage = '';

if (isset($_GET['msg'])) {
    $flashMessage = htmlspecialchars($_GET['msg']);
}

// ─── Helper: Format UGX ───────────────────────────────────────────────────────
function formatUGX($amount) {
    return 'UGX ' . number_format($amount, 0, '.', ',');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchases – Shopflow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Sora:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --orange:   #F05A28;
            --orange-h: #D44A1A;
            --dark:     #1A1A2E;
            --mid:      #6B7280;
            --border:   #E5E7EB;
            --bg:       #F9FAFB;
            --white:    #FFFFFF;
            --green:    #10B981;
            --red:      #EF4444;
            --font-head: 'Sora', sans-serif;
            --font-body: 'DM Sans', sans-serif;
        }

        body {
            font-family: var(--font-body);
            background: var(--bg);
            color: var(--dark);
            display: flex;
            min-height: 100vh;
        }

        /* ── Sidebar ─────────────────────────────────── */
        .sidebar {
            width: 210px;
            background: var(--white);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            padding: 28px 0;
            flex-shrink: 0;
        }

        .sidebar-brand {
            padding: 0 24px 28px;
        }
        .sidebar-brand h1 {
            font-family: var(--font-head);
            font-size: 1.4rem;
            color: var(--orange);
        }
        .sidebar-brand p {
            font-size: .72rem;
            color: var(--mid);
            margin-top: 2px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 24px;
            font-size: .875rem;
            font-weight: 500;
            color: var(--mid);
            text-decoration: none;
            transition: color .15s, background .15s;
        }
        .nav-item:hover { color: var(--dark); background: var(--bg); }
        .nav-item.active {
            background: var(--orange);
            color: var(--white);
            border-radius: 0 8px 8px 0;
            margin-right: 16px;
        }
        .nav-item svg { flex-shrink: 0; }

        /* ── Main ────────────────────────────────────── */
        .main {
            flex: 1;
            margin-left: 230px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .topbar {
            background: var(--white);
            border-bottom: 2px solid var(--orange);
            padding: 14px 36px;
            font-size: .9rem;
            font-weight: 600;
            letter-spacing: .5px;
            color: var(--dark);
        }

        .content {
            padding: 32px 36px;
            flex: 1;
            overflow-y: auto;
        }

        /* ── Page Header ─────────────────────────────── */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 28px;
        }
        .page-header h2 {
            font-family: var(--font-head);
            font-size: 1.75rem;
        }
        .page-header p { font-size: .85rem; color: var(--mid); margin-top: 4px; }

        .btn-new {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--orange);
            color: var(--white);
            padding: 10px 18px;
            border-radius: 8px;
            font-size: .85rem;
            font-weight: 600;
            text-decoration: none;
            transition: background .15s;
        }
        .btn-new:hover { background: var(--orange-h); }

        /* ── Summary Cards ───────────────────────────── */
        .stats-row {
            display: flex;
            gap: 20px;
            margin-bottom: 32px;
        }
        .stat-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px 28px;
            min-width: 200px;
        }
        .stat-card label {
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--mid);
            display: block;
            margin-bottom: 8px;
        }
        .stat-card .val {
            font-family: var(--font-head);
            font-size: 1.6rem;
            color: var(--dark);
        }

        /* ── Flash Message ───────────────────────────── */
        .flash {
            background: #D1FAE5;
            border: 1px solid var(--green);
            color: #065F46;
            border-radius: 8px;
            padding: 10px 16px;
            font-size: .85rem;
            margin-bottom: 20px;
        }

        /* ── Table Card ──────────────────────────────── */
        .card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
        }
        .card-title {
            padding: 20px 28px;
            font-weight: 700;
            font-size: 1rem;
            border-bottom: 1px solid var(--border);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: .85rem;
        }
        thead th {
            padding: 12px 20px;
            text-align: left;
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: var(--mid);
            background: var(--bg);
            border-bottom: 1px solid var(--border);
        }
        tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background .1s;
        }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #FFF7F4; }
        td { padding: 14px 20px; vertical-align: middle; }

        .item-id {
            color: var(--orange);
            font-weight: 600;
            text-decoration: none;
        }
        .item-id:hover { text-decoration: underline; }

        .total-cost { font-weight: 700; }

        /* ── Action Buttons ──────────────────────────── */
        .actions { display: flex; gap: 8px; align-items: center; }

        .btn-action {
            padding: 5px 12px;
            border-radius: 6px;
            font-size: .75rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: opacity .15s;
        }
        .btn-action:hover { opacity: .8; }
        .btn-delete { background: #FEE2E2; color: var(--red); }
        .btn-update { background: #FFF3E0; color: var(--orange); }

        /* ── Pagination ──────────────────────────────── */
        .pagination {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 20px 28px;
            border-top: 1px solid var(--border);
        }
        .page-btn {
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: .85rem;
            font-weight: 600;
            text-decoration: none;
            color: var(--dark);
            border: 1px solid var(--border);
            background: var(--white);
            transition: all .15s;
        }
        .page-btn:hover { border-color: var(--orange); color: var(--orange); }
        .page-btn.active { background: var(--orange); color: var(--white); border-color: var(--orange); }
        .page-btn.disabled { opacity: .4; pointer-events: none; }
        .page-ellipsis { color: var(--mid); padding: 0 4px; font-weight: 600; }

        /* ── Empty State ─────────────────────────────── */
        .empty { text-align: center; padding: 52px 20px; color: var(--mid); }
        .empty svg { margin-bottom: 14px; opacity: .3; }
        .empty p { font-size: .9rem; }
    </style>
</head>
<body>

<?php
$base_path = '../';
$current_page = 'purchase-list';
include '../core/navigation.php';
?>

<!-- ── Main ─────────────────────────────────────────────────────────────────── -->
<div class="main">
    <div class="topbar">Purchases</div>

    <div class="content">

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h2>Purchase Records</h2>
                <p>Manage and track purchases</p>
            </div>
            <a href="record-purchase.php" class="btn-new">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                New Purchase
            </a>
        </div>

        <!-- Flash Message -->
        <?php if ($flashMessage): ?>
            <div class="flash"><?= $flashMessage ?></div>
        <?php endif; ?>

        <!-- Summary Stats -->
        <div class="stats-row">
            <div class="stat-card">
                <label>Total Purchases</label>
                <div class="val"><?= formatUGX((float) $summary['total_purchases']) ?></div>
            </div>
            <div class="stat-card">
                <label>Items Procured</label>
                <div class="val"><?= number_format((int) $summary['total_units']) ?> Units</div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="card">
            <div class="card-title">Purchase History</div>

            <?php if (empty($purchases)): ?>
                <div class="empty">
                    <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>
                    <p>No purchase records found.</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Item ID</th>
                            <th>Quantity Purchased</th>
                            <th>Unit Cost (UGX)</th>
                            <th>Total Cost (UGX)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($purchases as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars(date('M d, Y', strtotime($row['date']))) ?></td>
                            <td>
                                <a href="item-detail.php?id=<?= urlencode($row['item_id']) ?>" class="item-id">
                                    <?= htmlspecialchars($row['item_id']) ?>
                                </a>
                            </td>
                            <td><?= number_format((int) $row['quantity']) ?> Units</td>
                            <td><?= number_format((float) $row['unit_cost'], 0, '.', ',') ?></td>
                            <td class="total-cost"><?= number_format((float) $row['total_cost'], 0, '.', ',') ?></td>
                            <td>
                                <div class="actions">
                                    <!-- Delete -->
                                    <form method="POST" style="display:inline"
                                          onsubmit="return confirm('Delete this purchase record?')">
                                        <input type="hidden" name="delete_id" value="<?= (int) $row['id'] ?>">
                                        <button type="submit" class="btn-action btn-delete">DELETE</button>
                                    </form>
                                    <!-- Update -->
                                    <a href="update-purchase.php?id=<?= (int) $row['id'] ?>"
                                       class="btn-action btn-update">UPDATE</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <!-- Prev -->
                    <a href="?page=<?= $currentPage - 1 ?>"
                       class="page-btn <?= $currentPage <= 1 ? 'disabled' : '' ?>">&#8249;</a>

                    <?php
                    // Build page number range (show first, last, and window around current)
                    $window = 2;
                    $pages  = [];
                    for ($i = 1; $i <= $totalPages; $i++) {
                        if ($i === 1 || $i === $totalPages ||
                            ($i >= $currentPage - $window && $i <= $currentPage + $window)) {
                            $pages[] = $i;
                        }
                    }
                    $prev = null;
                    foreach ($pages as $p):
                        if ($prev !== null && $p - $prev > 1):
                            echo '<span class="page-ellipsis">…</span>';
                        endif;
                        $prev = $p;
                    ?>
                        <a href="?page=<?= $p ?>"
                           class="page-btn <?= $p === $currentPage ? 'active' : '' ?>">
                            <?= $p ?>
                        </a>
                    <?php endforeach; ?>

                    <!-- Next -->
                    <a href="?page=<?= $currentPage + 1 ?>"
                       class="page-btn <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">&#8250;</a>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div><!-- .card -->

    </div><!-- .content -->
</div><!-- .main -->

</body>
</html>
