<?php
session_start();

// ha nincs belépve → login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
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
        <p><strong>Szerver:</strong> <?php echo $_SERVER['SERVER_SOFTWARE']; ?></p>
        <p><strong>Felhasználó:</strong> <?php echo $_SESSION['username']; ?></p>
        <p><strong>Készítő:</strong> Huczka Zsolt</p>
        <p><strong>Desing:</strong> Huczka Zsolt</p>
        <p><strong>Dátum:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
    </div>

    <a href="http://localhost/dashboard2/fd2/fd.php" class="back-btn">Vissza a dashboardra</a>
</div>

</body>
</html>