<?php

require_once 'config.php';
require_once 'functions.php';
require_admin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(400);
    exit('Invalid product ID.');
}

$stmt = $pdo->prepare('SELECT * FROM products WHERE id=?');
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    exit('Product not found.');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    foreach (['name', 'sku', 'category', 'description'] as $k) {
        $product[$k] = trim($_POST[$k] ?? '');
    }

    $product['quantity'] = $_POST['quantity'] ?? '';
    $product['price']    = $_POST['price'] ?? '';

    if ($product['name'] === '') {
        $error = 'Product name is required.';
    }
    elseif (filter_var($product['quantity'], FILTER_VALIDATE_INT) === false || (int)$product['quantity'] < 0) {
        $error = 'Quantity must be a whole number of 0 or more.';
    }
    elseif (!is_numeric($product['price']) || (float)$product['price'] < 0) {
        $error = 'Enter a valid non-negative price.';
    }
    else {
        try {
            $update = $pdo->prepare('UPDATE products SET name=?,sku=?,category=?,quantity=?,price=?,description=? WHERE id=?');

            $update->execute([
                $product['name'],
                $product['sku'] ?: null,
                $product['category'] ?: null,
                (int)$product['quantity'],
                (float)$product['price'],
                $product['description'] ?: null,
                $id
            ]);

            flash('Product updated successfully.');
            header('Location: products.php');
            exit;
        }
        catch (PDOException $ex) {
            $error = $ex->getCode() === '23000' ? 'That SKU is already in use.' : 'Could not update the product.';
        }
    }
}

page_start('Edit product');

if ($error) {
    echo '<div class="alert">' . e($error) . '</div>';
}

echo '<section class="panel form-panel-card"><p class="muted-copy">Update the information for ' . e($product['name']) . '.</p><form method="post">' . csrf_field();

$buttonLabel = 'Save changes';
include 'product_form.php';

echo '</form></section>';

page_end();