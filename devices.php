<?php
require 'config.php';
require 'auth.php';
requireLogin();
$devices = $pdo->query('SELECT id, device_name, device_type, status FROM devices ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
$pageTitle = 'Smart Devices'; $activePage = 'devices';
function devicePageIcon(string $type): string { return match (strtolower($type)) { 'light' => '◉', 'fan' => '✣', 'door' => '▣', 'sensor' => '⌁', default => '◇' }; }
require 'layout_top.php';
?>
<section class="page-intro"><span class="eyebrow">CONTROL & MONITORING</span><h2>Connected devices</h2><p>Current device values and controls from the existing devices table.</p></section>
<section class="section-box"><div class="section-top"><div class="section-title"><span class="section-mark">◉</span><div><h2>ALL DEVICES</h2><p><?= count($devices) ?> registered device<?= count($devices) === 1 ? '' : 's' ?></p></div></div></div>
<div class="device-grid">
<?php foreach ($devices as $device): $type = strtolower((string)$device['device_type']); $status = strtoupper(trim((string)$device['status'])); $active = in_array($status, ['ON','UNLOCKED'], true); ?>
<article class="device-card<?= $active ? ' is-active' : '' ?>"><div class="device-card-top"><span class="device-round-icon" aria-hidden="true"><?= devicePageIcon($type) ?></span><span class="device-type"><?= htmlspecialchars(strtoupper($type)) ?></span></div><h3><?= htmlspecialchars($device['device_name']) ?></h3><p class="device-description">Device ID <?= (int)$device['id'] ?></p><div class="device-card-bottom">
<?php if ($type === 'sensor'): ?><span class="temperature"><strong><?= htmlspecialchars($device['status']) ?></strong><small>Stored sensor status</small></span>
<?php elseif ($type === 'door'): ?><span class="door-status"><?= htmlspecialchars($status) ?></span><form method="post" action="device_action.php"><input type="hidden" name="device_id" value="<?= (int)$device['id'] ?>"><input type="hidden" name="return_to" value="devices.php"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>"><button class="action-button" type="submit"><?= $status === 'LOCKED' ? 'Unlock door' : 'Lock door' ?></button></form>
<?php else: ?><span class="power-text<?= $active ? ' green' : '' ?>"><?= htmlspecialchars($status) ?></span><form method="post" action="device_action.php"><input type="hidden" name="device_id" value="<?= (int)$device['id'] ?>"><input type="hidden" name="return_to" value="devices.php"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>"><button class="power-toggle<?= $active ? ' active' : '' ?>" type="submit" aria-label="Toggle <?= htmlspecialchars($device['device_name']) ?>"><span></span></button></form><?php endif; ?>
</div></article>
<?php endforeach; ?>
<?php if (!$devices): ?><p class="empty">No devices are registered in the devices table.</p><?php endif; ?>
</div></section>
<?php require 'layout_bottom.php'; ?>
