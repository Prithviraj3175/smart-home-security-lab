<?php

require "config.php";

session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {

        $error = "Username and password are required.";

    } else {

        $stmt = $pdo->prepare(
            "SELECT id, username, password, role
             FROM users
             WHERE username = ?
             LIMIT 1"
        );

        $stmt->execute([$username]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password"])) {

            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"] = $user["role"];

            // Login activity
            $log = $pdo->prepare(
                "INSERT INTO activity_logs
                (username, device_name, action)
                VALUES (?, ?, ?)"
            );

            $log->execute([
                $user["username"],
                "SYSTEM",
                "User logged in"
            ]);

            header("Location: index.php");
            exit;

        } else {

            $error = "Invalid username or password.";

        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Smart Home Security | Login</title>

    <link rel="stylesheet" href="style.css">

</head>

<body class="login-page">

    <div class="login-box">

        <div class="login-icon">
            🛡️
        </div>

        <h1>SMARTGUARD</h1>

        <p class="login-subtitle">
            SMART HOME SECURITY LAB
        </p>

        <?php if ($error): ?>

            <div class="login-error">
                ⚠️ <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <label>USERNAME</label>

            <input
                type="text"
                name="username"
                placeholder="Enter username"
                autocomplete="username"
                required
            >

            <label>PASSWORD</label>

            <input
                type="password"
                name="password"
                placeholder="Enter password"
                autocomplete="current-password"
                required
            >

            <button type="submit">
                ENTER SECURITY CENTER
            </button>

        </form>

        <div class="login-footer">
            AUTHORIZED ACCESS ONLY
        </div>

    </div>

</body>

</html>