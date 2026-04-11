<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /dashboard2/bej/login.php');
    exit;
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=project_db', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // CSAK saját + jóváhagyott projektek
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE user_id=? AND status='approved'");
    $stmt->execute([$_SESSION['user_id']]);
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            <p class="subtitle">
                Felhasználó: <?php echo htmlspecialchars($_SESSION['username']); ?>
            </p>
        </div>

        <a href="/dashboard2/create_project/create_project.php" class="create-project-btn">
            Új projekt
        </a>
        <form action="/dashboard2/fd2/fd.php" method="post">
            <button type="submit" class="edit-btn">Visza A Dashbordra</button>
        </form>

        <form method="POST" action="logout.php" class="logout-form">
            <button type="submit">Kijelentkezés</button>
        </form>
    </header>

    <?php if (empty($projects)): ?>
        <div class="empty-state">
            Nincs még projekted.
        </div>
    <?php else: ?>
        <div class="folder-grid">

            <?php foreach ($projects as $project): ?>
                <article class="folder-card">
                    <h2><?php echo htmlspecialchars($project['name']); ?></h2>
                    <p>Státusz: <?php echo $project['status']; ?></p>

                    <!-- PUBLIC -->
                    <a href="/dashboard2/create_project/projects/<?php echo rawurlencode($project['name']); ?>" target="_blank">
                        <button class="view-btn">Megtekintés</button>
                    </a>

                   <a href="/dashboard2/create_project/editor.php?project=<?php echo urlencode($project['name']); ?>" class="edit-btn">
                    Szerkesztés
                    </a>
                    
                </article>
            <?php endforeach; ?>

        </div>
    <?php endif; ?>

</div>

</body>
</html>