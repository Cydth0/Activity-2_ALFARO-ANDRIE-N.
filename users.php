<?php

require_once 'config.php';
require_once 'functions.php';
require_admin();

$users = $pdo->query('SELECT id,username,email,role,created_at FROM users ORDER BY created_at DESC')->fetchAll();

page_start('User management');
show_flash();

echo '<div class="page-actions"><p class="muted-copy">Create accounts, update access levels, and manage registered users.</p><a class="button" href="add_user.php">＋ Add user</a></div><section class="panel">';

if (!$users) {
    echo '<div class="empty-state"><h3>No users found</h3></div>';
}
else {
    echo '<div class="table-scroll"><table><thead><tr><th>Account</th><th>Email</th><th>Role</th><th>Joined</th><th>Actions</th></tr></thead><tbody>';

    foreach ($users as $u) {
        echo '<tr><td><strong>' . e($u['username']) . '</strong>' . ((int)$u['id'] === (int)$_SESSION['user_id'] ? '<small class="cell-sub">You</small>' : '') . '</td><td>' . e($u['email']) . '</td><td><span class="role-chip">' . strtoupper(e($u['role'])) . '</span></td><td>' . e(date('M j, Y', strtotime($u['created_at']))) . '</td><td><div class="action-links"><a href="edit_user.php?id=' . (int)$u['id'] . '">Edit</a>';

        if ((int)$u['id'] !== (int)$_SESSION['user_id']) {
            echo '<form method="post" action="delete_user.php" onsubmit="return confirm(\'Delete this account?\')">' . csrf_field() . '<input type="hidden" name="id" value="' . (int)$u['id'] . '"><button class="danger-link" type="submit">Delete</button></form>';
        }
        else {
            echo '<span class="muted-copy">Current account</span>';
        }

        echo '</div></td></tr>';
    }

    echo '</tbody></table></div>';
}

echo '<p class="table-note">' . count($users) . ' account(s). Public registration always creates regular user accounts.</p></section>';

page_end();