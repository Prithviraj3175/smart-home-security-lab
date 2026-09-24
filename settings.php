<?php
require 'config.php';
require 'auth.php';
requireLogin();
$username = (string)($_SESSION['username'] ?? '');
$role = (string)($_SESSION['role'] ?? 'user');
$sessionName = session_name();
$sessionStatus = session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Not active';
$pageTitle = 'Settings'; $activePage = 'settings';
require 'layout_top.php';
?>
<section class="page-intro"><span class="eyebrow">ACCOUNT & SESSION</span><h2>Profile settings</h2><p>Current account and security details available from your authenticated session.</p></section>
<div class="settings-grid">
<section class="panel"><div class="panel-heading"><div><span class="eyebrow">PROFILE</span><h2>Signed-in account</h2><p>Identity information from the current login session.</p></div></div><dl class="details-list"><div><dt>Username</dt><dd><?= htmlspecialchars($username) ?></dd></div><div><dt>Role</dt><dd><?= htmlspecialchars(strtoupper($role)) ?></dd></div><div><dt>User ID</dt><dd><?= (int)($_SESSION['user_id'] ?? 0) ?></dd></div><div><dt>Session state</dt><dd><span class="state-good"><?= htmlspecialchars($sessionStatus) ?></span></dd></div></dl></section>
<section class="panel"><div class="panel-heading"><div><span class="eyebrow">SESSION SECURITY</span><h2>Authentication</h2><p>Live details provided by PHP's session handler.</p></div></div><dl class="details-list"><div><dt>Session cookie name</dt><dd><?= htmlspecialchars($sessionName) ?></dd></div><div><dt>Login guard</dt><dd>Required for protected pages</dd></div><div><dt>Password storage</dt><dd>Application verifies stored password hashes</dd></div><div><dt>End this session</dt><dd><a class="text-link" href="logout.php">Logout securely →</a></dd></div></dl></section>
</div>
<?php require 'layout_bottom.php'; ?>
