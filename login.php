<?php

require_once 'config.php';
require_once 'functions.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT id, username, email, password, role FROM users WHERE email=? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);

        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];

        header('Location: dashboard.php');
        exit;
    }

    $error = 'Email or password is incorrect.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in — Northstar</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="auth-shell">

        <section class="visual-panel">
            <a class="brand" href="login.php"><span class="brand-mark">N</span> NORTHSTAR</a>

            <div class="visual-copy">
                <p class="eyebrow">YOUR WORKSPACE AWAITS</p>
                <h1>Keep things<br>moving forward.</h1>
                <p class="visual-description">A clearer view of your inventory, your team, and the work ahead.</p>
            </div>

            <div class="visual-bottom">
                <span class="orbit"></span>
                <span>YOUR SPACE. YOUR PACE.</span>
            </div>
        </section>

        <section class="form-panel">
            <div class="form-wrap">
                <p class="mobile-brand">NORTHSTAR ✳</p>
                <p class="eyebrow">MEMBER ACCESS</p>
                <h2>Welcome back.</h2>
                <p class="subheading">Sign in to open your workspace.</p>

                <?php if($error): ?>
                    <div class="alert"><?=e($error)?></div>
                <?php endif; ?>

                <form method="post">
                    <?=csrf_field()?>

                    <label for="email">Email address</label>
                    <input id="email" name="email" type="email" placeholder="you@example.com" value="<?=e($email)?>" required>

                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" placeholder="Enter your password" required>

                    <button class="primary-button" type="submit">Sign in <span>↗</span></button>
                </form>

                <p class="switch-link">New around here? <a href="register.php">Create an account</a></p>
                <p class="fine-print">Northstar inventory workspace</p>
            </div>
        </section>

    </main>
</body>
</html>