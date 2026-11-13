<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

function is_admin(): bool
{
    return ($_SESSION['role'] ?? '') === 'admin';
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function require_admin(): void
{
    require_login();

    if (!is_admin()) {
        http_response_code(403);
        page_start('Access denied');

        echo '<section class="empty-state"><div class="empty-icon">↗</div><h2>Admin access required</h2><p>Your account does not have permission to open this page.</p><a class="button" href="dashboard.php">Back to dashboard</a></section>';

        page_end();
        exit;
    }
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';

    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(400);
        exit('Invalid request token. Please go back and try again.');
    }
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function page_start(string $title): void
{
    $name = e($_SESSION['username'] ?? 'Guest');
    $role = is_admin() ? 'ADMIN' : 'USER';

    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>' . e($title) . ' — Northstar</title><link rel="stylesheet" href="style.css"></head><body class="app-page">';

    echo '<aside class="sidebar"><a class="brand dark-brand" href="dashboard.php"><span class="brand-mark">N</span> NORTHSTAR</a><div class="side-label">WORKSPACE</div><a class="nav-link" href="dashboard.php">⌂ <span>Overview</span></a><a class="nav-link" href="products.php">▦ <span>Inventory</span></a>';

    if (is_admin()) {
        echo '<div class="side-label">ADMINISTRATION</div><a class="nav-link" href="users.php">♙ <span>User management</span></a>';
    }

    echo '<div class="side-bottom"><div class="profile-mini"><span class="avatar">' . strtoupper(substr($name, 0, 1)) . '</span><div><strong>' . $name . '</strong><small>' . $role . '</small></div></div><a class="logout-link" href="logout.php">Sign out ↗</a></div></aside>';

    echo '<main class="main-content"><header class="topbar"><div><span class="topbar-kicker">NORTHSTAR / WORKSPACE</span><h1>' . e($title) . '</h1></div><div class="top-user">' . $name . ' <span class="role-chip">' . $role . '</span></div></header><div class="content-wrap">';
}

function page_end(): void
{
    echo '</div><footer class="app-footer">NORTHSTAR <span>·</span> YOUR SPACE. YOUR PACE.</footer></main></body></html>';
}

function flash(string $message, string $type = 'success'): void
{
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function show_flash(): void
{
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);

        echo '<div class="notice ' . e($f['type']) . '">' . e($f['message']) . '</div>';
    }
}