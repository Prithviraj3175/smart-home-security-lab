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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Cyberpunk Pirate Security Lab</title>

    <link rel="stylesheet" href="login.css">

</head>

<body class="pirate-login">


    <div class="pirate-overlay"></div>


    <div class="mist mist-one"></div>
    <div class="mist mist-two"></div>


    <main class="login-stage">


        <section class="pirate-title">

            <div class="title-mark">
                ☠
            </div>

            <div class="title-line"></div>

            <span>
                LOCAL SECURITY ENVIRONMENT
            </span>

            <h1>
                SMART HOME
            </h1>

            <h2>
                SECURITY LAB
            </h2>

            <p>
                ENTER THE SHADOW NETWORK
            </p>

        </section>


        <section class="login-panel">


            <div class="panel-skull">
                ☠
            </div>


            <div class="panel-header">

                <span class="status-dot"></span>

                SECURE ACCESS PORTAL

            </div>


            <h3>
                AUTHORIZED ACCESS
            </h3>

            <p class="panel-subtitle">
                Identify yourself to enter the control center.
            </p>


            <?php if ($error): ?>

                <div class="login-error">

                    <span>⚠</span>

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>


            <form method="POST">


                <div class="field">


                    <label for="username">
                        USERNAME
                    </label>


                    <div class="input-wrap">

                        <span class="input-icon">
                            ◈
                        </span>

                        <input
                            id="username"
                            type="text"
                            name="username"
                            placeholder="Enter username"
                            autocomplete="username"
                            required
                        >

                    </div>

                </div>


                <div class="field">


                    <label for="password">
                        PASSWORD
                    </label>


                    <div class="input-wrap">

                        <span class="input-icon">
                            ◇
                        </span>

                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="Enter password"
                            autocomplete="current-password"
                            required
                        >

                    </div>

                </div>


                <button type="submit">

                    <span>
                        ENTER SECURITY CENTER
                    </span>

                    <b>
                        →
                    </b>

                </button>


            </form>


            <div class="access-warning">

                <span>☠</span>

                AUTHORIZED PERSONNEL ONLY

                <span>☠</span>

            </div>


            <div class="panel-footer">

                <span>
                    SYSTEM
                </span>

                <strong>
                    ONLINE
                </strong>

                <i></i>

                <span>
                    LOCALHOST
                </span>

            </div>


        </section>


    </main>


    <footer class="login-bottom">

        <span>
            ☠ SMART HOME SECURITY LAB
        </span>

        <span>
            XAMPP / MYSQL
        </span>

        <span>
            LOCAL CYBER SECURITY ENVIRONMENT
        </span>

    </footer>


</body>

</html>