<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /dashboard2/bej/login.php');
    exit;
}

require_once __DIR__ . '/../config.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['project_name'] ?? '');

    if ($name === '') {
        $message = 'Adj meg projektnevet.';
    } else {
        $safe_name = preg_replace('/[^a-zA-Z0-9_-]/', '', $name);

        if ($safe_name === '') {
            $message = 'A projekt neve csak betűket, számokat, kötőjelet és aláhúzást tartalmazhat.';
        } else {
            $path = __DIR__ . '/projects/' . $safe_name;
            if (!file_exists($path)) {
                mkdir($path, 0775, true);
                file_put_contents($path . '/index.php', "<?php
 echo 'Hello project';
");

                $stmt = $pdo->prepare("INSERT INTO projects (name, user_id, status) VALUES (?, ?, 'pending')");
                $stmt->execute([$safe_name, $_SESSION['user_id']]);

                header('Location: editor.php?project=' . urlencode($safe_name));
                exit;
            }
            $message = 'Már létezik ilyen projekt.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head><meta charset="UTF-8"><title>Új projekt</title></head>
<body>
<h1>Új projekt</h1>
<form method="POST">
    <input type="text" name="project_name" placeholder="Projekt neve">
    <button type="submit">Létrehozás</button>
</form>
<p><?php echo htmlspecialchars($message); ?></p>
<p><a href="/dashboard2/dashboard/dashboard.php">Vissza</a></p>
</body>
</html>
