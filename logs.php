<?php
require 'config.php';
require 'auth.php';
requireLogin();
$logs = $pdo->query('SELECT username, device_name, action, log_time FROM activity_logs ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
$pageTitle = 'Activity Logs'; $activePage = 'logs';
require 'layout_top.php';
?>
<section class="page-intro"><span class="eyebrow">AUDIT TRAIL</span><h2>Activity logs</h2><p>Recorded login and device activity from the existing activity_logs table.</p></section>
<section class="panel"><div class="panel-heading"><div><span class="eyebrow">DATABASE RECORDS</span><h2>All activity</h2><p>Username, device/action, and recorded timestamp.</p></div><span class="count-badge"><?= count($logs) ?> EVENTS</span></div>
<div class="table-wrap"><table><thead><tr><th>#</th><th>Username</th><th>Device</th><th>Action</th><th>Timestamp</th></tr></thead><tbody>
<?php foreach ($logs as $i => $log): ?><tr><td><?= $i + 1 ?></td><td><?= htmlspecialchars($log['username']) ?></td><td><?= htmlspecialchars((string)$log['device_name']) ?></td><td><?= htmlspecialchars($log['action']) ?></td><td><?= htmlspecialchars(date('d M Y, H:i:s', strtotime((string)$log['log_time']))) ?></td></tr><?php endforeach; ?>
<?php if (!$logs): ?><tr><td colspan="5" class="empty-cell">No activity recorded yet.</td></tr><?php endif; ?>
</tbody></table></div></section>
<?php require 'layout_bottom.php'; ?>
