<?php
session_start();

// jogosultság ellenőrzés
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'moderator') {
    header('Location: /dashboard2/bej/login.php');
    exit;
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=project_db', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // pending projektek
    $stmt = $pdo->query("SELECT projects.*, users.username 
                         FROM projects 
                         JOIN users ON projects.user_id = users.id
                         WHERE status='pending'");
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

            <!-- Jóváhagyás -->
            <form method="POST" action="./approve.php" style="display:inline;">
                <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
                <button type="submit">Jóváhagy</button>
            </form>

            <!-- Törlés -->
            <form method="POST" action="./delete_project.php" style="display:inline;">
                <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
                <button type="submit">Törlés</button>
            </form>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<a href="/dashboard2/fd2/fd.php">Vissza</a>

</body>
</html>