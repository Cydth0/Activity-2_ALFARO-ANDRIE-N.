<?php

require_once 'config.php';
require_once 'functions.php';
require_login();

$productCount = (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$totalUnits   = (int)$pdo->query('SELECT COALESCE(SUM(quantity),0) FROM products')->fetchColumn();
$lowStock     = (int)$pdo->query('SELECT COUNT(*) FROM products WHERE quantity <= 5')->fetchColumn();
$userCount    = is_admin() ? (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn() : null;
$recent       = $pdo->query('SELECT id,name,category,quantity,updated_at FROM products ORDER BY updated_at DESC,id DESC LIMIT 5')->fetchAll();

page_start('Overview');
show_flash();

echo '<section class="welcome-row"><div><p class="eyebrow">YOUR WORKSPACE</p><h2>Good to have you, ' . e($_SESSION['username']) . '.</h2><p>Here is what is happening with your inventory today.</p></div><a class="button" href="products.php">View inventory <span>↗</span></a></section>';

echo '<section class="stats-grid"><article class="stat-card"><span class="stat-icon">▦</span><p>Total products</p><strong>' . $productCount . '</strong><small>Different items in your inventory</small></article><article class="stat-card"><span class="stat-icon">▤</span><p>Total units</p><strong>' . $totalUnits . '</strong><small>Combined quantity across products</small></article><article class="stat-card"><span class="stat-icon">!</span><p>Low stock</p><strong>' . $lowStock . '</strong><small>Products with 5 units or fewer</small></article>';

if (is_admin()) {
    echo '<article class="stat-card"><span class="stat-icon">♙</span><p>Registered users</p><strong>' . $userCount . '</strong><small>Accounts in Northstar</small></article>';
}

echo '</section><section class="panel"><div class="panel-heading"><div><p class="eyebrow">LATEST CHANGES</p><h3>Recently updated products</h3></div><a class="text-link" href="products.php">See all products ↗</a></div>';

if (!$recent) {
    echo '<div class="empty-state compact"><p>No products yet. ' . (is_admin() ? 'Add your first product to get started.' : 'Ask an administrator to add products.') . '</p></div>';
}
else {
    echo '<div class="table-scroll"><table><thead><tr><th>Product</th><th>Category</th><th>Quantity</th><th>Last updated</th></tr></thead><tbody>';

    foreach ($recent as $p) {
        echo '<tr><td><strong>' . e($p['name']) . '</strong><small class="cell-sub">#' . (int)$p['id'] . '</small></td><td>' . e($p['category'] ?: 'Uncategorized') . '</td><td><span class="quantity-pill">' . (int)$p['quantity'] . '</span></td><td>' . e(date('M j, Y', strtotime($p['updated_at']))) . '</td></tr>';
    }

    echo '</tbody></table></div>';
}

echo '</section>';

page_end();