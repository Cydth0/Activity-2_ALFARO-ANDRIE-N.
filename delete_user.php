<?php

require_once 'config.php';
require_once 'functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: users.php');
    exit;
}

verify_csrf();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || (int)$id === (int)$_SESSION['user_id']) {
    flash('You cannot delete your own account.', 'error');
    header('Location: users.php');
    exit;
}

$stmt = $pdo->prepare('DELETE FROM users WHERE id=?');
$stmt->execute([$id]);

flash($stmt->rowCount() ? 'User account deleted.' : 'User account was not found.');
header('Location: users.php');
exit;