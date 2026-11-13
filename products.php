<?php

require_once 'config.php';
require_once 'functions.php';
require_login();

$q = trim($_GET['q'] ?? '');

if ($q !== '') {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE name LIKE ? OR category LIKE ? OR sku LIKE ? ORDER BY name');
    $like = '%' . $q . '%';
    $stmt->execute([$like, $like, $like]);
    $products = $stmt->fetchAll();
}
else {
    $products = $pdo->query('SELECT * FROM products ORDER BY name')->fetchAll();
}

page_start('Inventory');
show_flash();

echo '<div class="page-actions"><div><p class="muted-copy">Browse and keep track of your products.</p></div>' . (is_admin() ? '<a class="button" href="add_product.php">＋ Add product</a>' : '<span class="role-chip">VIEW ONLY ACCESS</span>') . '</div>';

echo '<section class="panel"><form class="search-form" method="get"><input type="search" name="q" value="' . e($q) . '" placeholder="Search name, category, or SKU..."><button class="button secondary-button" type="submit">Search</button>' . ($q !== '' ? '<a class="text-link" href="products.php">Clear</a>' : '') . '</form>';

if (!$products) {
    echo '<div class="empty-state"><div class="empty-icon">▦</div><h3>No products found</h3><p>' . ($q !== '' ? 'Try a different search term.' : 'There are no products in the inventory yet.') . '</p></div>';
}
else {
    echo '<div class="table-scroll"><table><thead><tr><th>Product</th><th>SKU</th><th>Category</th><th>Quantity</th><th>Price</th>' . (is_admin() ? '<th>Actions</th>' : '') . '</tr></thead><tbody>';

    foreach ($products as $p) {
        echo '<tr><td><strong>' . e($p['name']) . '</strong><small class="cell-sub">Added ' . e(date('M j, Y', strtotime($p['created_at']))) . '</small></td><td>' . e($p['sku'] ?: '—') . '</td><td>' . e($p['category'] ?: 'Uncategorized') . '</td><td><span class="quantity-pill ' . ((int)$p['quantity'] <= 5 ? 'low-stock' : '') . '">' . (int)$p['quantity'] . '</span></td><td>₱' . number_format((float)$p['price'], 2) . '</td>';

        if (is_admin()) {
            echo '<td><div class="action-links"><a href="edit_product.php?id=' . (int)$p['id'] . '">Edit</a><form method="post" action="delete_product.php" onsubmit="return confirm(\'Delete this product?\')">' . csrf_field() . '<input type="hidden" name="id" value="' . (int)$p['id'] . '"><button class="danger-link" type="submit">Delete</button></form></div></td>';
        }

        echo '</tr>';
    }

    echo '</tbody></table></div>';
}

echo '<p class="table-note">' . count($products) . ' product(s) shown' . ($q !== '' ? ' for “' . e($q) . '”' : '') . '.</p></section>';

page_end();