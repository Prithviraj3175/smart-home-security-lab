<?php
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
function requireLogin(): void { if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; } }
function currentUser(): string { return (string)($_SESSION['username'] ?? 'Guest'); }
function csrfToken(): string { if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); } return (string)$_SESSION['csrf_token']; }