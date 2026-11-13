<?php

require_once 'config.php';
require_once 'functions.php';
require_admin();

$error = '';
$product = [
    'name'        => '',
    'sku'         => '',
    'category'    => '',
    'quantity'    => 0,
    'price'       => '0.00',
    'description' => ''
];

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
            $stmt = $pdo->prepare('INSERT INTO products(name,sku,category,quantity,price,description) VALUES(?,?,?,?,?,?)');

            $stmt->execute([
                $product['name'],
                $product['sku'] ?: null,
                $product['category'] ?: null,
                (int)$product['quantity'],
                (float)$product['price'],
                $product['description'] ?: null
            ]);

            flash('Product added successfully.');
            header('Location: products.php');
            exit;
        }
        catch (PDOException $ex) {
            $error = $ex->getCode() === '23000' ? 'That SKU is already in use.' : 'Could not save the product.';
        }
    }
}

page_start('Add product');

if ($error) {
    echo '<div class="alert">' . e($error) . '</div>';
}

echo '<section class="panel form-panel-card"><p class="muted-copy">Enter the details for the new inventory item.</p><form method="post">' . csrf_field();

$buttonLabel = 'Save product';
include 'product_form.php';

echo '</form></section>';

page_end();