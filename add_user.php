<?php

require_once 'config.php';
require_once 'functions.php';
require_admin();

$error = '';
$user = [
    'username' => '',
    'email'    => '',
    'role'     => 'user'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $user['username'] = trim($_POST['username'] ?? '');
    $user['email']    = trim($_POST['email'] ?? '');
    $user['role']     = $_POST['role'] ?? 'user';
    $password         = $_POST['password'] ?? '';

    if (strlen($user['username']) < 3 || strlen($user['username']) > 30) {
        $error = 'Username must be between 3 and 30 characters.';
    }
    elseif (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter a valid email address.';
    }
    elseif (!in_array($user['role'], ['admin', 'user'], true)) {
        $error = 'Invalid access level.';
    }
    elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    }
    else {
        try {
            $stmt = $pdo->prepare('INSERT INTO users(username,email,password,role) VALUES(?,?,?,?)');

            $stmt->execute([
                $user['username'],
                $user['email'],
                password_hash($password, PASSWORD_DEFAULT),
                $user['role']
            ]);

            flash('User account created.');
            header('Location: users.php');
            exit;
        }
        catch (PDOException $ex) {
            $error = $ex->getCode() === '23000' ? 'Username or email is already in use.' : 'Could not create the account.';
        }
    }
}

page_start('Add user');

if ($error) {
    echo '<div class="alert">' . e($error) . '</div>';
}

echo '<section class="panel form-panel-card"><p class="muted-copy">Create a new account and choose its access level.</p><form method="post">' . csrf_field();

$includePassword = true;
$buttonLabel = 'Create account';
include 'user_form.php';

echo '</form></section>';

page_end();