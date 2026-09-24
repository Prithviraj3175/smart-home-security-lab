<?php
$pageTitle = $pageTitle ?? 'Control Center';
$activePage = $activePage ?? '';
$roleLabel = ucfirst((string)($_SESSION['role'] ?? 'user'));
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="dark">
    <title><?= htmlspecialchars($pageTitle) ?> | Smart Home Security Lab</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="dashboard-background" aria-hidden="true">
    <div class="background-base"></div>
    <div class="background-atmosphere"></div>
    <img class="background-ship" src="pirate-ship.png" alt="">
    <img class="background-skeleton" src="pirate-skeleton.png.png" alt="">
</div>
<aside class="sidebar">
    <a class="brand" href="index.php" aria-label="Smart Home Security Lab dashboard">
        <span class="brand-mark" aria-hidden="true">✥</span>
        <span><strong>SMART HOME</strong><small>SECURITY LAB</small></span>
    </a>
    <div class="sidebar-divider"></div>
    <nav aria-label="Main navigation">
        <?php
        $navigation = [
            'index.php' => ['dashboard', '⌂', 'Dashboard'],
            'devices.php' => ['devices', '◉', 'Smart Devices'],
            'security.php' => ['security', '⬡', 'Security'],
            'logs.php' => ['logs', '≡', 'Activity Logs'],
            'users.php' => ['users', '◎', 'Users'],
            'settings.php' => ['settings', '⚙', 'Settings'],
        ];
        foreach ($navigation as $href => [$key, $icon, $label]): ?>
            <a class="nav-item<?= $activePage === $key ? ' active' : '' ?>" href="<?= $href ?>"<?= $activePage === $key ? ' aria-current="page"' : '' ?>>
                <span class="nav-icon" aria-hidden="true"><?= $icon ?></span><span><?= $label ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
    <div class="sidebar-bottom">
        <div class="system-online"><span class="status-dot"></span><div><strong>SYSTEM ONLINE</strong><small>Database connected</small></div></div>
        <a class="logout" href="logout.php"><span aria-hidden="true">↪</span>Logout</a>
    </div>
</aside>
<main class="main">
    <header class="topbar">
        <div class="top-title"><span>SMART HOME SECURITY LAB</span><h1><?= htmlspecialchars(strtoupper($pageTitle)) ?></h1></div>
        <div class="top-actions"><button class="ambient-toggle" id="ambient-toggle" type="button" aria-pressed="false" aria-label="Turn ambient ocean sound on" title="Ambient ocean sound is off"><span class="ambient-icon" aria-hidden="true">◖))</span><span class="ambient-label">AMBIENT</span><span class="ambient-state" id="ambient-state">OFF</span></button><div class="top-user"><span class="avatar" aria-hidden="true"><?= htmlspecialchars(strtoupper(substr((string)currentUser(), 0, 1))) ?></span><span class="profile"><strong><?= htmlspecialchars(currentUser()) ?></strong><small><?= htmlspecialchars($roleLabel) ?></small></span></div></div>
    </header>
    <div class="page-content">
