<aside class="sidebar">
    <div class="brand">
        <h1>Shopflow</h1>
        <span>SME Management</span>
    </div>
    <nav class="nav-menu">
        <a href="<?php echo $base_path; ?>dashboard/dashboard.php" class="nav-item <?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>">
            <i class="fa-solid fa-gauge"></i> Dashboard
        </a>

        <div class="nav-section">
            <span class="nav-section-title">Forms</span>
            <a href="<?php echo $base_path; ?>item-form/item-form.html" class="nav-item <?php echo ($current_page == 'item-form') ? 'active' : ''; ?>">
                <i class="fa-solid fa-plus"></i> New Item
            </a>
            <a href="<?php echo $base_path; ?>purchase-form/purchase.html" class="nav-item <?php echo ($current_page == 'purchase-form') ? 'active' : ''; ?>">
                <i class="fa-solid fa-cart-shopping"></i> Record Purchase
            </a>
            <a href="<?php echo $base_path; ?>sale-form/sale-form.php" class="nav-item <?php echo ($current_page == 'sale-form') ? 'active' : ''; ?>">
                <i class="fa-solid fa-receipt"></i> Record Sale
            </a>
        </div>

        <div class="nav-section">
            <span class="nav-section-title">Lists</span>
            <a href="<?php echo $base_path; ?>item-list/item-list.php" class="nav-item <?php echo ($current_page == 'item-list') ? 'active' : ''; ?>">
                <i class="fa-solid fa-boxes-stacked"></i> All Items
            </a>
            <a href="<?php echo $base_path; ?>purchase-list/purchases.php" class="nav-item <?php echo ($current_page == 'purchase-list') ? 'active' : ''; ?>">
                <i class="fa-solid fa-file-invoice"></i> Purchases
            </a>
            <a href="<?php echo $base_path; ?>sale-list/sale-list.php" class="nav-item <?php echo ($current_page == 'sale-list') ? 'active' : ''; ?>">
                <i class="fa-solid fa-chart-line"></i> Sales
            </a>
        </div>
    </nav>

    <a href="<?php echo $base_path; ?>profile/profile.php" class="nav-item profile-link <?php echo ($current_page == 'profile') ? 'active' : ''; ?>">
        <i class="fa-solid fa-user"></i> Profile
    </a>
</aside>


<style>
.sidebar {
    width: 230px;
    background-color: #F7F7FA;
    border-right: 1px solid #e8e8ed;
    padding: 28px 0;
    position: fixed;
    height: 100vh;
    display: flex;
    flex-direction: column;
}

.brand {
    padding: 0 24px 28px;
    border-bottom: 1px solid #e8e8ed;
}

.brand h1 {
    font-size: 24px;
    font-weight: 700;
    color: #ED673A;
    letter-spacing: -0.5px;
}

.brand span {
    font-size: 12px;
    color: #6b7280;
    margin-top: 2px;
    display: block;
}

/* Navigation */
.nav-menu {
    padding: 20px 14px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    flex: 1;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 11px 14px;
    text-decoration: none;
    color: #4b5563;
    font-size: 14px;
    font-weight: 500;
    border-radius: 10px;
    transition: all 0.15s ease;
}

.nav-item:hover {
    background-color: #ededf2;
    color: #1f2937;
}

.nav-item.active {
    background-color: #ED673A;
    color: #fff;
    box-shadow: 0 2px 8px rgba(237, 103, 58, 0.3);
}

.nav-item i {
    font-size: 15px;
    width: 20px;
    text-align: center;
}

.nav-item i.fa-gauge { color: #ED673A; }
.nav-item i.fa-plus { color: #10b981; }
.nav-item i.fa-cart-shopping { color: #3b82f6; }
.nav-item i.fa-receipt { color: #8b5cf6; }
.nav-item i.fa-boxes-stacked { color: #f59e0b; }
.nav-item i.fa-file-invoice { color: #06b6d4; }
.nav-item i.fa-chart-line { color: #ec4899; }
.nav-item i.fa-user { color: #6366f1; }

.nav-item.active i,
.nav-item:hover i {
    color: inherit;
}

/* Nav Sections */
.nav-section {
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid #e8e8ed;
}

.nav-section-title {
    display: block;
    font-size: 11px;
    font-weight: 600;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 0 14px 10px;
}

.profile-link {
    margin: 14px;
    border-top: 1px solid #e8e8ed;
    padding-top: 14px;
}
</style>