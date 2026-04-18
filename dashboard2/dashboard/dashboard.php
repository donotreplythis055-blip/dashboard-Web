<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /dashboard2/bej/login.php');
    exit;
}

require_once __DIR__ . '/../config.php';

try {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE user_id=? ORDER BY id DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $projects = $stmt->fetchAll();
} catch (PDOException $e) {
    die('DB hiba');
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
<div class="page-shell">
    <header class="page-header">
        <div>
            <p class="eyebrow">Dashboard</p>
            <h1>Projektjeim</h1>
            <p class="subtitle">Felhasználó: <?php echo htmlspecialchars($_SESSION['username']); ?></p>
        </div>
        <a href="/dashboard2/create_project/create_project.php" class="create-project-btn">Új projekt</a>
        <form action="/dashboard2/fd2/fd.php" method="get">
            <button type="submit" class="edit-btn">Vissza a főoldalra</button>
        </form>
        <form method="POST" action="logout.php" class="logout-form">
            <button type="submit">Kijelentkezés</button>
        </form>
    </header>
    <?php if (empty($projects)): ?>
        <div class="empty-state">Nincs még projekted.</div>
    <?php else: ?>
        <div class="folder-grid">
            <?php foreach ($projects as $project): ?>
                <article class="folder-card">
                    <h2><?php echo htmlspecialchars($project['name']); ?></h2>
                    <p>Státusz: <?php echo htmlspecialchars($project['status']); ?></p>
                    <?php if ($project['status'] === 'approved'): ?>
                        <a href="/dashboard2/create_project/projects/<?php echo rawurlencode($project['name']); ?>" target="_blank"><button class="view-btn">Megtekintés</button></a>
                    <?php endif; ?>
                    <form action="/dashboard2/create_project/editor.php" method="get">
                        <input type="hidden" name="project" value="<?php echo htmlspecialchars($project['name']); ?>">
                        <button type="submit" class="edit-btn">Szerkesztés</button>
                    </form>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
