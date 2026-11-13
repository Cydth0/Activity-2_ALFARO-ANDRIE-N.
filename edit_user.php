<?php

require_once 'config.php';
require_once 'functions.php';
require_admin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(400);
    exit('Invalid user ID.');
}

$stmt = $pdo->prepare('SELECT id,username,email,role FROM users WHERE id=?');
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    http_response_code(404);
    exit('User not found.');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $user['username'] = trim($_POST['username'] ?? '');
    $user['email']    = trim($_POST['email'] ?? '');
    $newRole          = $_POST['role'] ?? 'user';

    if (strlen($user['username']) < 3 || strlen($user['username']) > 30) {
        $error = 'Username must be between 3 and 30 characters.';
    }
    elseif (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter a valid email address.';
    }
    elseif (!in_array($newRole, ['admin', 'user'], true)) {
        $error = 'Invalid access level.';
    }
    elseif ((int)$id === (int)$_SESSION['user_id'] && $newRole !== 'admin') {
        $error = 'You cannot remove your own administrator access.';
    }
    else {
        try {
            $update = $pdo->prepare('UPDATE users SET username=?,email=?,role=? WHERE id=?');

            $update->execute([
                $user['username'],
                $user['email'],
                $newRole,
                $id
            ]);

            if ((int)$id === (int)$_SESSION['user_id']) {
                $_SESSION['username'] = $user['username'];
                $_SESSION['role']     = $newRole;
            }

            flash('User account updated.');
            header('Location: users.php');
            exit;
        }
        catch (PDOException $ex) {
            $error = $ex->getCode() === '23000' ? 'Username or email is already in use.' : 'Could not update the account.';
        }
    }
}

page_start('Edit user');

if ($error) {
    echo '<div class="alert">' . e($error) . '</div>';
}

echo '<section class="panel form-panel-card"><p class="muted-copy">Change account details and permissions for ' . e($user['username']) . '.</p><form method="post">' . csrf_field();

$includePassword = false;
$buttonLabel = 'Save changes';
include 'user_form.php';

echo '</form></section>';

page_end();