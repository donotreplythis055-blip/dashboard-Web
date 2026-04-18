<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /dashboard2/bej/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Info</title>
    <link rel="stylesheet" href="info.css">
</head>
<body>
<div class="info-container">
    <h1>Rendszer információk</h1>
    <div class="info-box">
        <p><strong>PHP verzió:</strong> <?php echo phpversion(); ?></p>
        <p><strong>Szerver:</strong> <?php echo htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'ismeretlen'); ?></p>
        <p><strong>Felhasználó:</strong> <?php echo htmlspecialchars($_SESSION['username'] ?? 'ismeretlen'); ?></p>
        <p><strong>Készítő:</strong> Huczka Zsolt</p>
        <p><strong>Design:</strong> Huczka Zsolt</p>
        <p><strong>Dátum:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
    </div>
    <a href="/dashboard2/fd2/fd.php" class="back-btn">Vissza a dashboardra</a>
</div>
</body>
</html>
