<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}
// $_SESSION['user_id'] = 1;

require_once 'profile_db.php';

$user_id = $_SESSION['user_id'];
$user    = getUserById($conn, $user_id);

$success_msg = '';
$error_msg   = '';

// Handle Edit Information form
if (isset($_POST['update_info'])) {
    $full_name = trim($_POST['full_name']);
    $email     = trim($_POST['email']);

    if (empty($full_name) || empty($email)) {
        $error_msg = "Name and email cannot be empty.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "Please enter a valid email address.";
    } else {
        $result = updateUserInfo($conn, $user_id, $full_name, $email);
        if ($result === true) {
            $success_msg = "Profile updated successfully.";
            $user = getUserById($conn, $user_id); // refresh
        } else {
            $error_msg = $result; // error string from function
        }
    }
}

// Handle Update Password form
if (isset($_POST['update_password'])) {
    $current_password  = $_POST['current_password'];
    $new_password      = $_POST['new_password'];
    $confirm_password  = $_POST['confirm_password'];

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error_msg = "All password fields are required.";
    } elseif (strlen($new_password) < 12) {
        $error_msg = "New password must be at least 12 characters long.";
    } elseif ($new_password !== $confirm_password) {
        $error_msg = "New passwords do not match.";
    } elseif (!password_verify($current_password, $user['password'])) {
        $error_msg = "Current password is incorrect.";
    } else {
        $result = updateUserPassword($conn, $user_id, $new_password);
        if ($result === true) {
            $success_msg = "Password updated successfully.";
        } else {
            $error_msg = $result;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile – ShopFlow</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>

<!-- ========== SIDEBAR ========== -->
<aside class="sidebar">
    <div class="sidebar__brand">
        <span class="brand-name">Shopflow</span>
        <span class="brand-sub">ENTERPRISE MANAGEMENT</span>
    </div>

    <nav class="sidebar__nav">
        <a href="../dashboard/dashboard.php" class="nav-item">
            <span class="nav-icon">&#9783;</span> Dashboard
        </a>
        <a href="../item-form/item-form.php" class="nav-item">
            <span class="nav-icon">&#43;</span> New Item
        </a>
        <a href="../purchase-form/purchase-form.php" class="nav-item">
            <span class="nav-icon">&#128722;</span> Record Purchase
        </a>
        <a href="../sale-form/sale-form.php" class="nav-item">
            <span class="nav-icon">&#128179;</span> Record Sale
        </a>
        <a href="../item-list/item-list.php" class="nav-item">
            <span class="nav-icon">&#9776;</span> All Items
        </a>
        <a href="../purchase-list/purchase-list.php" class="nav-item">
            <span class="nav-icon">&#128203;</span> Purchases
        </a>
        <a href="../sale-list/sale-list.php" class="nav-item">
            <span class="nav-icon">&#128200;</span> Sales
        </a>
    </nav>
</aside>

<!-- ========== MAIN ========== -->
<main class="main">
    <header class="page-header">
        <h2 class="page-header__title">User Profile</h2>
    </header>

    <div class="content">

        <?php if ($success_msg): ?>
            <div class="alert alert--success"><?php echo htmlspecialchars($success_msg); ?></div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="alert alert--error"><?php echo htmlspecialchars($error_msg); ?></div>
        <?php endif; ?>

        <p class="section-label">ACCOUNT MANAGEMENT</p>
        <h1 class="section-title">Settings &amp; Security</h1>

        <div class="cards-row">

            <!-- ── ACCOUNT INFORMATION CARD ── -->
            <div class="card" id="info-card">
                <div class="card__header">
                    <span class="card__icon card__icon--account">&#128100;</span>
                    <h3 class="card__title">Account Information</h3>
                </div>

                <!-- View mode -->
                <div id="info-view">
                    <div class="field-group">
                        <label class="field-label">Full Legal Name</label>
                        <p class="field-value"><?php echo htmlspecialchars($user['full_name']); ?></p>
                    </div>
                    <div class="field-group">
                        <label class="field-label">Primary Email</label>
                        <p class="field-value"><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>

                    <button class="btn btn--secondary" id="edit-btn" type="button">
                        &#9998; Edit Information
                    </button>
                </div>

                <!-- Edit mode (hidden by default) -->
                <form id="info-form" method="POST" action="profile.php" class="hidden">
                    <div class="field-group">
                        <label class="field-label" for="full_name">Full Legal Name</label>
                        <input class="input" type="text" id="full_name" name="full_name"
                            value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    </div>
                    <div class="field-group">
                        <label class="field-label" for="email">Primary Email</label>
                        <input class="input" type="email" id="email" name="email"
                            value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="btn-row">
                        <button class="btn btn--primary" type="submit" name="update_info">Save Changes</button>
                        <button class="btn btn--secondary" type="button" id="cancel-btn">Cancel</button>
                    </div>
                </form>
            </div>

            <!-- ── SECURITY CARD ── -->
            <div class="card" id="security-card">
                <div class="card__header">
                    <span class="card__icon card__icon--security">&#128274;</span>
                    <h3 class="card__title">Security &amp; Authentication</h3>
                </div>

                <p class="card__desc">
                    Manage your secure access credentials. Ensure your password is at least
                    12 characters long with a mix of symbols and numbers for optimal
                    enterprise-grade security.
                </p>

                <form method="POST" action="profile.php">
                    <div class="field-group">
                        <label class="field-label" for="current_password">Current Password</label>
                        <div class="input-wrap">
                            <input class="input" type="password" id="current_password"
                                name="current_password" placeholder="••••••••••••">
                            <button class="toggle-pw" type="button" data-target="current_password"
                                    aria-label="Toggle password visibility">&#128065;</button>
                        </div>
                    </div>

                    <div class="field-row">
                        <div class="field-group">
                            <label class="field-label" for="new_password">New Password</label>
                            <input class="input" type="password" id="new_password"
                                name="new_password" placeholder="Minimum 12 chars">
                        </div>
                        <div class="field-group">
                            <label class="field-label" for="confirm_password">Confirm New Password</label>
                            <input class="input" type="password" id="confirm_password"
                                name="confirm_password" placeholder="Repeat new password">
                        </div>
                    </div>

                    <button class="btn btn--primary btn--orange" type="submit" name="update_password">
                        Update Password
                    </button>
                </form>
            </div>

        </div><!-- /.cards-row -->
    </div><!-- /.content -->
</main>

<script src="profile.js"></script>
</body>
</html>
