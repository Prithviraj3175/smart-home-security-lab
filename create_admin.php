<?php

require "config.php";

$username = "admin";
$password = "Admin@12345";
$role = "admin";

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare(
    "INSERT INTO users (username, password, role)
     VALUES (?, ?, ?)"
);

$stmt->execute([
    $username,
    $hash,
    $role
]);

echo "Admin account created successfully.<br>";
echo "Username: admin<br>";
echo "Password: Admin@12345<br>";
echo "<br>Delete create_admin.php after this.";