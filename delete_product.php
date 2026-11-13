<?php

require_once 'config.php';
require_once 'functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: products.php');
    exit;
}

verify_csrf();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    flash('Invalid product ID.', 'error');
    header('Location: products.php');
    exit;
}

$stmt = $pdo->prepare('DELETE FROM products WHERE id=?');
$stmt->execute([$id]);

flash($stmt->rowCount() ? 'Product deleted.' : 'Product was not found.');
header('Location: products.php');
exit;