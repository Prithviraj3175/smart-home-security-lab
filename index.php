<?php
require 'config.php';
require 'auth.php';
requireLogin();

$devices = $pdo->query('SELECT id, device_name, device_type, status FROM devices ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
$activities = $pdo->query('SELECT username, device_name, action, log_time FROM activity_logs ORDER BY id DESC LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);
$pageTitle = 'Dashboard';
$activePage = 'dashboard';
function dashboardDeviceIcon(string $type): string {
    return match (strtolower($type)) { 'light' => '◉', 'fan' => '✣', 'door' => '▣', 'sensor' => '⌁', default => '◇' };
}
require 'layout_top.php';
?>
<section class="hero">
    <div class="hero-content">
        <span class="eyebrow">SMART HOME SECURITY LAB</span>
        <h2>WELCOME BACK,<br><strong><?= htmlspecialchars(strtoupper(currentUser())) ?></strong></h2>
        <p>Monitor and control your smart home devices from one secure dashboard.</p>
        <div class="secure-pill"><span class="status-dot"></span>SYSTEM SECURE</div>
    </div>
</section>
<section class="section-box" aria-labelledby="devices-heading">
    <div class="section-top"><div class="section-title"><span class="section-mark">◉</span><div><h2 id="devices-heading">SMART DEVICES</h2><p>Live state from your connected devices</p></div></div><a class="text-link" href="devices.php">View all devices <span aria-hidden="true">→</span></a></div>
    <div class="device-grid">
    <?php foreach ($devices as $device): $type = strtolower((string)$device['device_type']); $status = strtoupper(trim((string)$device['status'])); $active = in_array($status, ['ON', 'UNLOCKED'], true); ?>
        <article class="device-card<?= $active ? ' is-active' : '' ?>">
            <div class="device-card-top"><span class="device-round-icon" aria-hidden="true"><?= dashboardDeviceIcon($type) ?></span><span class="device-live-dot" aria-label="Device reported"></span></div>
            <h3><?= htmlspecialchars($device['device_name']) ?></h3><p class="device-description"><?= htmlspecialchars(strtoupper($type)) ?></p>
            <div class="device-card-bottom">
                <?php if ($type === 'sensor'): ?>
                    <span class="temperature"><strong><?= htmlspecialchars($device['status']) ?></strong><small>Sensor status</small></span>
                <?php elseif ($type === 'door'): ?>
                    <span class="door-status"><?= htmlspecialchars($status) ?></span>
                    <form method="post" action="device_action.php"><input type="hidden" name="device_id" value="<?= (int)$device['id'] ?>"><input type="hidden" name="return_to" value="index.php"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>"><button class="action-button" type="submit"><?= $status === 'LOCKED' ? 'Unlock' : 'Lock' ?></button></form>
                <?php else: ?>
                    <span class="power-text<?= $active ? ' green' : '' ?>"><?= htmlspecialchars($status) ?></span>
                    <form method="post" action="device_action.php"><input type="hidden" name="device_id" value="<?= (int)$device['id'] ?>"><input type="hidden" name="return_to" value="index.php"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>"><button class="power-toggle<?= $active ? ' active' : '' ?>" type="submit" aria-label="Toggle <?= htmlspecialchars($device['device_name']) ?>"><span></span></button></form>
                <?php endif; ?>
            </div>
        </article>
    <?php endforeach; ?>
    <?php if (!$devices): ?><p class="empty">No devices are registered in the devices table.</p><?php endif; ?>
    </div>
</section>
<div class="bottom-grid">
    <section class="panel" aria-labelledby="security-heading">
        <div class="panel-heading"><div><span class="eyebrow">PROTECTION</span><h2 id="security-heading">SECURITY STATUS</h2><p>Live checks from this session and database</p></div><a class="panel-link" href="security.php" aria-label="Open security details">→</a></div>
        <div class="security-lines">
            <div><span class="check-mark">✓</span><span>Authentication</span><b>Session active</b></div>
            <div><span class="check-mark">✓</span><span>Device access</span><b>Login required</b></div>
            <div><span class="check-mark">✓</span><span>Database</span><b>Connected</b></div>
        </div>
        <a class="text-link" href="security.php">View security details <span aria-hidden="true">→</span></a>
    </section>
    <section class="panel" aria-labelledby="activity-heading">
        <div class="panel-heading"><div><span class="eyebrow">AUDIT TRAIL</span><h2 id="activity-heading">RECENT ACTIVITY</h2><p>Latest recorded database events</p></div><a class="panel-link" href="logs.php" aria-label="Open activity logs">→</a></div>
        <div class="activity-list">
        <?php foreach ($activities as $activity): ?>
            <div class="activity"><span class="activity-icon" aria-hidden="true">·</span><div class="activity-text"><strong><?= htmlspecialchars($activity['action']) ?></strong><small><?= htmlspecialchars($activity['username']) ?> · <?= htmlspecialchars($activity['device_name'] ?? '') ?></small></div><time><?= htmlspecialchars(date('d M, H:i', strtotime((string)$activity['log_time']))) ?></time></div>
        <?php endforeach; ?>
        <?php if (!$activities): ?><p class="empty">No activity recorded yet.</p><?php endif; ?>
        </div>
    </section>
</div>
<?php require 'layout_bottom.php'; ?>
