```php
<?php

require "config.php";
require "auth.php";

requireLogin();


// ===============================
// GET DEVICES
// ===============================

$stmt = $pdo->query("
    SELECT id, device_name, device_type, status
    FROM devices
    ORDER BY id ASC
");

$devices = $stmt->fetchAll();


// ===============================
// GET RECENT ACTIVITY
// ===============================

$stmt = $pdo->query("
    SELECT username, device_name, action, log_time
    FROM activity_logs
    ORDER BY id DESC
    LIMIT 5
");

$activities = $stmt->fetchAll();


// ===============================
// HELPERS
// ===============================

function deviceIcon($type)
{
    switch (strtolower($type)) {

        case "light":
            return "💡";

        case "fan":
            return "🌀";

        case "door":
            return "🚪";

        case "sensor":
            return "🌡";

        default:
            return "⚙";
    }
}


function deviceLabel($type)
{
    switch (strtolower($type)) {

        case "light":
            return "LIGHT";

        case "fan":
            return "FAN";

        case "door":
            return "DOOR";

        case "sensor":
            return "SENSOR";

        default:
            return strtoupper($type);
    }
}


function timeAgo($datetime)
{
    $time = strtotime($datetime);
    $diff = time() - $time;

    if ($diff < 60) {
        return "Just now";
    }

    if ($diff < 3600) {
        return floor($diff / 60) . " min ago";
    }

    if ($diff < 86400) {
        return floor($diff / 3600) . " hr ago";
    }

    return date("d M Y, h:i A", $time);
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Smart Home Security Lab</title>

    <link rel="stylesheet" href="style.css">

</head>


<body>


<!-- =====================================
     SIDEBAR
===================================== -->

<aside class="sidebar">

    <div class="logo">

        <div class="logo-icon">
            ⌂
        </div>

        <div>

            <h2>SMART HOME</h2>

            <span>SECURITY LAB</span>

        </div>

    </div>


    <div class="sidebar-line"></div>


    <nav>


        <!-- DASHBOARD -->

        <a
            href="index.php"
            class="nav-item active"
        >

            <span>▦</span>

            Dashboard

        </a>


        <!-- SMART DEVICES -->

        <a
            href="devices.php"
            class="nav-item"
        >

            <span>⌂</span>

            Smart Devices

        </a>


        <!-- SECURITY -->

        <a
            href="security.php"
            class="nav-item"
        >

            <span>◉</span>

            Security

        </a>


        <!-- ACTIVITY LOGS -->

        <a
            href="logs.php"
            class="nav-item"
        >

            <span>≡</span>

            Activity Logs

        </a>


        <!-- USERS -->

        <a
            href="users.php"
            class="nav-item"
        >

            <span>♙</span>

            Users

        </a>


        <!-- SETTINGS -->

        <a
            href="settings.php"
            class="nav-item"
        >

            <span>⚙</span>

            Settings

        </a>

    </nav>


    <!-- SIDEBAR BOTTOM -->

    <div class="sidebar-bottom">

        <div class="lab-status">

            <span class="status-dot"></span>

            LOCAL LAB ONLINE

        </div>


        <a
            href="logout.php"
            class="logout-btn"
        >

            ⎋ Logout

        </a>

    </div>

</aside>



<!-- =====================================
     MAIN CONTENT
===================================== -->

<main class="main">


    <!-- TOP BAR -->

    <header class="topbar">

        <div>

            <div class="breadcrumb">
                SECURITY LAB / DASHBOARD
            </div>

            <h1>Control Center</h1>

        </div>


        <div class="topbar-right">

            <div class="secure-badge">

                <span>●</span>

                SYSTEM SECURE

            </div>


            <div class="user-box">

                <div class="user-avatar">
                    A
                </div>

                <div>

                    <strong>
                        <?= htmlspecialchars(currentUser()) ?>
                    </strong>

                    <small>
                        Administrator
                    </small>

                </div>

            </div>

        </div>

    </header>



    <!-- =====================================
         WELCOME
    ===================================== -->

    <section class="welcome-section">

        <div>

            <p class="eyebrow">
                SMART HOME MONITORING SYSTEM
            </p>

            <h2>
                Welcome back,
                <?= htmlspecialchars(currentUser()) ?>.
            </h2>

            <p>
                Monitor your smart home devices and
                security activity from one place.
            </p>

        </div>


        <div class="welcome-status">

            <span class="status-dot"></span>

            ALL SYSTEMS OPERATIONAL

        </div>

    </section>



    <!-- =====================================
         DEVICE OVERVIEW
    ===================================== -->

    <section
        class="dashboard-section"
        id="devices"
    >

        <div class="section-heading">

            <div>

                <span class="section-label">
                    HARDWARE
                </span>

                <h2>
                    Device Overview
                </h2>

            </div>


            <a
                href="devices.php"
                class="section-link"
            >
                VIEW ALL →
            </a>

        </div>



        <div class="device-grid">


            <?php foreach ($devices as $device): ?>

                <?php

                $type = strtolower($device["device_type"]);

                $status = strtoupper($device["status"]);

                $isActive =
                    ($status === "ON" || $status === "UNLOCKED");

                ?>


                <div class="device-card">


                    <!-- DEVICE TOP -->

                    <div class="device-top">

                        <div class="device-icon">

                            <?= deviceIcon($type) ?>

                        </div>


                        <span class="device-type">

                            <?= deviceLabel($type) ?>

                        </span>

                    </div>



                    <!-- DEVICE NAME -->

                    <h3>

                        <?= htmlspecialchars(
                            $device["device_name"]
                        ) ?>

                    </h3>


                    <!-- DEVICE STATUS -->

                    <p class="device-status">

                        Status:

                        <strong>

                            <?= htmlspecialchars($status) ?>

                        </strong>

                    </p>



                    <!-- DEVICE BOTTOM -->

                    <div class="device-bottom">


                        <?php if ($type === "sensor"): ?>


                            <div class="sensor-value">

                                <strong>
                                    24.5°C
                                </strong>

                                <span>
                                    CURRENT TEMP
                                </span>

                            </div>


                            <div class="sensor-icon">
                                ●
                            </div>


                        <?php elseif ($type === "door"): ?>


                            <form
                                method="POST"
                                action="device_action.php"
                            >

                                <input
                                    type="hidden"
                                    name="device_id"
                                    value="<?= $device["id"] ?>"
                                >


                                <button
                                    type="submit"
                                    class="lock-button"
                                    title="Lock / Unlock"
                                >

                                    <?php

                                    if ($status === "LOCKED") {
                                        echo "🔒";
                                    } else {
                                        echo "🔓";
                                    }

                                    ?>

                                </button>

                            </form>


                        <?php else: ?>


                            <form
                                method="POST"
                                action="device_action.php"
                            >

                                <input
                                    type="hidden"
                                    name="device_id"
                                    value="<?= $device["id"] ?>"
                                >


                                <button
                                    type="submit"
                                    class="toggle
                                    <?= $isActive ? "active" : "" ?>"
                                >

                                    <span></span>

                                </button>

                            </form>


                            <span class="action-label">

                                <?= $isActive
                                    ? "ACTIVE"
                                    : "OFFLINE" ?>

                            </span>


                        <?php endif; ?>


                    </div>

                </div>


            <?php endforeach; ?>


        </div>

    </section>



    <!-- =====================================
         SECURITY + ACTIVITY
    ===================================== -->

    <div class="dashboard-columns">


        <!-- SECURITY -->

        <section
            class="panel"
            id="security"
        >

            <div class="panel-header">

                <div>

                    <span class="section-label">
                        PROTECTION
                    </span>

                    <h2>
                        Security Status
                    </h2>

                </div>


                <a href="security.php">
                    VIEW →
                </a>

            </div>


            <div class="security-main">

                <div class="security-score">

                    <strong>
                        98
                    </strong>

                    <span>
                        SECURITY SCORE
                    </span>

                </div>


                <div class="security-info">

                    <div class="security-row">

                        <span>
                            ●
                        </span>

                        Network Status

                        <strong>
                            SECURE
                        </strong>

                    </div>


                    <div class="security-row">

                        <span>
                            ●
                        </span>

                        Failed Logins

                        <strong>
                            0
                        </strong>

                    </div>


                    <div class="security-row">

                        <span>
                            ●
                        </span>

                        Active Threats

                        <strong>
                            0
                        </strong>

                    </div>

                </div>

            </div>

        </section>



        <!-- ACTIVITY -->

        <section
            class="panel"
            id="activity"
        >

            <div class="panel-header">

                <div>

                    <span class="section-label">
                        MONITORING
                    </span>

                    <h2>
                        Recent Activity
                    </h2>

                </div>


                <a href="logs.php">
                    VIEW ALL →
                </a>

            </div>


            <div class="activity-list">


                <?php if (count($activities) > 0): ?>


                    <?php foreach ($activities as $activity): ?>


                        <div class="activity-item">


                            <div class="activity-icon">
                                ⚡
                            </div>


                            <div class="activity-content">

                                <strong>

                                    <?= htmlspecialchars(
                                        $activity["action"]
                                    ) ?>

                                </strong>

                                <span>

                                    <?= htmlspecialchars(
                                        $activity["username"]
                                    ) ?>

                                    ·

                                    <?= timeAgo(
                                        $activity["log_time"]
                                    ) ?>

                                </span>

                            </div>


                        </div>


                    <?php endforeach; ?>


                <?php else: ?>


                    <div class="empty-activity">

                        No activity recorded yet.

                    </div>


                <?php endif; ?>


            </div>

        </section>


    </div>



    <!-- =====================================
         FOOTER INFO
    ===================================== -->

    <section class="dashboard-footer">


        <div>

            <span class="footer-icon">
                ◉
            </span>

            <div>

                <strong>
                    LOCAL SECURITY ENVIRONMENT
                </strong>

                <p>
                    XAMPP / MySQL · localhost
                </p>

            </div>

        </div>


        <div>

            <span class="footer-icon">
                ✓
            </span>

            <div>

                <strong>
                    LAB MONITORING ACTIVE
                </strong>

                <p>
                    Smart home security simulation running
                </p>

            </div>

        </div>


    </section>


</main>



<script src="app.js"></script>

</body>

</html>
```
