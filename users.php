<?php
require 'config.php';
require 'auth.php';
requireLogin();
$users = $pdo->query('SELECT id, username, role FROM users ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
$pageTitle = 'Users'; $activePage = 'users';
require 'layout_top.php';
?>
<section class="page-intro"><span class="eyebrow">ACCOUNT DIRECTORY</span><h2>Registered users</h2><p>Existing accounts and roles. Credential fields are not requested or displayed.</p></section>
<section class="panel"><div class="panel-heading"><div><span class="eyebrow">ACCESS CONTROL</span><h2>Users</h2><p>Account records from the users table.</p></div><span class="count-badge"><?= count($users) ?> USERS</span></div>
<div class="table-wrap"><table><thead><tr><th>#</th><th>Username</th><th>Role</th><th>Account ID</th></tr></thead><tbody>
<?php foreach ($users as $i => $user): ?><tr><td><?= $i + 1 ?></td><td><?= htmlspecialchars($user['username']) ?><?= (int)$user['id'] === (int)($_SESSION['user_id'] ?? 0) ? ' <span class="muted">(current account)</span>' : '' ?></td><td><span class="role-badge"><?= htmlspecialchars(strtoupper((string)$user['role'])) ?></span></td><td><?= (int)$user['id'] ?></td></tr><?php endforeach; ?>
<?php if (!$users): ?><tr><td colspan="4" class="empty-cell">No users found.</td></tr><?php endif; ?>
</tbody></table></div></section>
<?php require 'layout_bottom.php'; ?>
