<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /dashboard2/bej/login.php');
    exit;
}
$role = $_SESSION['role'] ?? 'user';
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Főoldal</title>
    <link rel="stylesheet" href="fd2.css">
</head>
<body>
<div class="main-container">
    <h1>Mit szeretnél?</h1>
    <a href="/dashboard2/dashboard/dashboard.php" class="btn dashboard-btn">Dashboard</a>
    <a href="/dashboard2/infok/info.php" class="btn info-btn">Információk</a>
    <?php if ($role === 'moderator'): ?>
        <a href="/dashboard2/moderation/panel.php" class="btn admin-btn">Moderátor panel</a>
    <?php endif; ?>
    <a href="/dashboard2/dashboard/logout.php" class="btn logout-btn">Kijelentkezés</a>
</div>
</body>
</html>
