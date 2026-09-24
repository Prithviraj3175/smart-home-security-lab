<?php
require 'config.php';
require 'auth.php';
requireLogin();
$deviceCount = (int)$pdo->query('SELECT COUNT(*) FROM devices')->fetchColumn();
$userCount = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$eventCount = (int)$pdo->query('SELECT COUNT(*) FROM activity_logs')->fetchColumn();
$failedStmt = $pdo->query("SELECT username, device_name, action, log_time FROM activity_logs WHERE LOWER(action) LIKE '%fail%' OR LOWER(action) LIKE '%invalid%' ORDER BY id DESC LIMIT 10");
$failedEvents = $failedStmt->fetchAll(PDO::FETCH_ASSOC);
$recent = $pdo->query('SELECT username, device_name, action, log_time FROM activity_logs ORDER BY id DESC LIMIT 8')->fetchAll(PDO::FETCH_ASSOC);
$pageTitle = 'Security'; $activePage = 'security';
require 'layout_top.php';
?>
<section class="page-intro"><span class="eyebrow">LIVE SECURITY OVERVIEW</span><h2>Security monitoring</h2><p>Indicators below reflect the active session, application access controls, database connection, and recorded audit data.</p></section>
<section class="security-grid">
    <article class="panel security-card"><span class="security-symbol">◈</span><div><span class="eyebrow">AUTHENTICATION</span><h2>Session active</h2><p>Signed in as <?= htmlspecialchars(currentUser()) ?> (<?= htmlspecialchars((string)($_SESSION['role'] ?? 'user')) ?>).</p></div><span class="state-good">ACTIVE</span></article>
    <article class="panel security-card"><span class="security-symbol">▣</span><div><span class="eyebrow">DEVICE ACCESS</span><h2>Login protected</h2><p>Device controls require an authenticated session.</p></div><span class="state-good">GUARDED</span></article>
    <article class="panel security-card"><span class="security-symbol">⌁</span><div><span class="eyebrow">NETWORK / DATABASE</span><h2>Local database connected</h2><p>Application is connected to the configured MySQL database.</p></div><span class="state-good">ONLINE</span></article>
</section>
<section class="stats-grid"><div class="stat-card"><strong><?= $deviceCount ?></strong><span>Devices</span></div><div class="stat-card"><strong><?= $userCount ?></strong><span>Registered users</span></div><div class="stat-card"><strong><?= $eventCount ?></strong><span>Audit records</span></div><div class="stat-card"><strong><?= count($failedEvents) ?></strong><span>Matching failed/invalid events</span></div></section>
<section class="panel"><div class="panel-heading"><div><span class="eyebrow">AUDIT DATA</span><h2>Recent security events</h2><p>Events are shown from activity_logs. Failed-login details appear only when recorded in that table.</p></div><a class="text-link" href="logs.php">All logs →</a></div>
<?php if ($failedEvents): ?><div class="table-wrap"><table><thead><tr><th>User</th><th>Event</th><th>Details</th><th>Time</th></tr></thead><tbody><?php foreach ($failedEvents as $event): ?><tr><td><?= htmlspecialchars($event['username']) ?></td><td><?= htmlspecialchars($event['action']) ?></td><td><?= htmlspecialchars((string)$event['device_name']) ?></td><td><?= htmlspecialchars(date('d M Y, H:i:s', strtotime((string)$event['log_time']))) ?></td></tr><?php endforeach; ?></tbody></table></div><?php else: ?><p class="empty">No failed or invalid events are recorded in activity_logs.</p><?php endif; ?>
<h3 class="subheading">Latest recorded events</h3>
<?php if ($recent): ?><div class="activity-list"><?php foreach ($recent as $event): ?><div class="activity"><span class="activity-icon" aria-hidden="true">·</span><div class="activity-text"><strong><?= htmlspecialchars($event['action']) ?></strong><small><?= htmlspecialchars($event['username']) ?> · <?= htmlspecialchars((string)$event['device_name']) ?></small></div><time><?= htmlspecialchars(date('d M Y, H:i', strtotime((string)$event['log_time']))) ?></time></div><?php endforeach; ?></div><?php else: ?><p class="empty">No security activity recorded yet.</p><?php endif; ?>
</section>
<?php require 'layout_bottom.php'; ?>
