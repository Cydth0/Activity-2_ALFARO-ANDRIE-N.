<?php // Shared user form fields. Variables: $user, $includePassword, $buttonLabel. ?>

<label for="username">Username</label>
<input id="username" name="username" type="text" minlength="3" maxlength="30" value="<?=e($user['username'] ?? '')?>" required>

<label for="email">Email address</label>
<input id="email" name="email" type="email" maxlength="255" value="<?=e($user['email'] ?? '')?>" required>

<label for="role">Access level</label>
<select id="role" name="role">
    <option value="user" <?=($user['role'] ?? 'user') === 'user' ? 'selected' : ''?>>Regular user</option>
    <option value="admin" <?=($user['role'] ?? '') === 'admin' ? 'selected' : ''?>>Administrator</option>
</select>

<?php if($includePassword): ?>
    <label for="password">Temporary password</label>
    <input id="password" name="password" type="password" minlength="8" placeholder="At least 8 characters" required>
<?php endif; ?>

<div class="form-buttons">
    <button class="button" type="submit"><?=e($buttonLabel)?></button>
    <a class="button ghost-button" href="users.php">Cancel</a>
</div>