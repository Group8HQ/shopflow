<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}
// $_SESSION['user_id'] = 1;

require __DIR__ . '/profile_db.php';

$user_id = $_SESSION['user_id'];
$user    = getUserById($conn, $user_id);

$success_msg = '';
$error_msg   = '';

// Handle Edit Information form
if (isset($_POST['update_info'])) {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);

    if (empty($name) || empty($email)) {
        $error_msg = "Name and email cannot be empty.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "Please enter a valid email address.";
    } else {
        $result = updateUserInfo($conn, $user_id, $name, $email);
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
    <title>Profile – ShopFlow</title>
    <link rel="stylesheet" href="../dashboard/style.css">
    <link rel="stylesheet" href="profile.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<?php
$base_path = '../';
$current_page = 'profile';
include '../core/navigation.php';
?>

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
                        <label class="field-label">Full Name</label>
                        <p class="field-value"><?php echo htmlspecialchars($user['name']); ?></p>
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
                        <label class="field-label" for="name">Full Name</label>
                        <input class="input" type="text" id="name" name="name"
                            value="<?php echo htmlspecialchars($user['name']); ?>" required>
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

        <!-- Logout -->
        <div class="logout-section">
            <a href="../logout.php" class="btn btn--logout">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </div>
    </div><!-- /.content -->
</main>

<script src="profile.js"></script>
</body>
</html>
