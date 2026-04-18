<?php
session_start();
if (!isset($_SESSION['user_id']) || (($_SESSION['role'] ?? 'user') !== 'moderator')) {
    header('Location: /dashboard2/bej/login.php');
    exit;
}
require_once __DIR__ . '/../config.php';
try {
    $stmt = $pdo->query("SELECT projects.*, users.username FROM projects JOIN users ON projects.user_id = users.id WHERE status='pending' ORDER BY projects.id DESC");
    $projects = $stmt->fetchAll();
} catch (PDOException $e) {
    die('Adatbázis hiba');
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="panel.css">
    <title>Moderátor panel</title>
</head>
<body>
<h1>Moderátor panel</h1>
<?php if (empty($projects)): ?>
    <p>Nincs ellenőrizendő projekt.</p>
<?php else: ?>
    <?php foreach ($projects as $project): ?>
        <div style="margin-bottom:20px; padding:10px; border:1px solid #ccc;">
            <p><strong>Projekt:</strong> <?php echo htmlspecialchars($project['name']); ?></p>
            <p><strong>Létrehozta:</strong> <?php echo htmlspecialchars($project['username']); ?></p>
            <form method="POST" action="./approve.php" style="display:inline;">
                <input type="hidden" name="id" value="<?php echo (int)$project['id']; ?>">
                <button type="submit">Jóváhagy</button>
            </form>
            <form method="POST" action="./delete_project.php" style="display:inline;">
                <input type="hidden" name="id" value="<?php echo (int)$project['id']; ?>">
                <button type="submit">Törlés</button>
            </form>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
<a href="/dashboard2/fd2/fd.php">Vissza</a>
</body>
</html>
