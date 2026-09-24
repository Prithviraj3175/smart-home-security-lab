<?php
require 'config.php';
require 'auth.php';
requireLogin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Method not allowed'); }
if (!isset($_POST['csrf_token']) || !hash_equals(csrfToken(), (string)$_POST['csrf_token'])) { http_response_code(403); exit('Invalid request token. Refresh the page and try again.'); }
$deviceId = filter_input(INPUT_POST, 'device_id', FILTER_VALIDATE_INT);
if (!$deviceId || $deviceId < 1) { http_response_code(400); exit('Invalid device ID.'); }
$returnTo = (string)($_POST['return_to'] ?? 'index.php');
if (!in_array($returnTo, ['index.php', 'devices.php'], true)) { $returnTo = 'index.php'; }
try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare('SELECT id, device_name, device_type, status FROM devices WHERE id = ? LIMIT 1');
    $stmt->execute([$deviceId]);
    $device = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$device) { $pdo->rollBack(); http_response_code(404); exit('Device not found.'); }
    $currentStatus = strtoupper(trim((string)$device['status']));
    $deviceType = strtolower((string)$device['device_type']);
    if ($deviceType === 'sensor') { $pdo->rollBack(); http_response_code(400); exit('Temperature sensors cannot be toggled.'); }
    if ($deviceType === 'door') {
        $newStatus = $currentStatus === 'LOCKED' ? 'UNLOCKED' : 'LOCKED';
        $action = $device['device_name'] . ($newStatus === 'LOCKED' ? ' locked' : ' unlocked');
    } else {
        $newStatus = $currentStatus === 'ON' ? 'OFF' : 'ON';
        $action = $device['device_name'] . ' turned ' . $newStatus;
    }
    $update = $pdo->prepare('UPDATE devices SET status = ? WHERE id = ?');
    $update->execute([$newStatus, $deviceId]);
    $log = $pdo->prepare('INSERT INTO activity_logs (username, device_name, action) VALUES (?, ?, ?)');
    $log->execute([currentUser(), $device['device_name'], $action]);
    $pdo->commit();
    header('Location: ' . $returnTo);
    exit;
} catch (Throwable $e) {
    if ($pdo->inTransaction()) { $pdo->rollBack(); }
    error_log('Device action failed: ' . $e->getMessage());
    http_response_code(500);
    exit('The device action could not be completed. Check the application log.');
}