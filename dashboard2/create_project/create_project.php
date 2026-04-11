<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /dashboard2/bej/login.php');
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=project_db', 'root', '');
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['project_name']);

    if ($name === '') {
        $message = 'Adj meg nevet';
    } else {
        $safe_name = preg_replace('/[^a-zA-Z0-9_-]/', '', $name);
        $path = __DIR__ . '/projects/' . $safe_name;

        if (!file_exists($path)) {
            mkdir($path, 0777, true);

            // alap fájl létrehozás
            file_put_contents($path . '/index.php', "<?php echo 'Hello project'; ?>");

            // DB mentés
            $stmt = $pdo->prepare("INSERT INTO projects (name, user_id) VALUES (?, ?)");
            $stmt->execute([$safe_name, $_SESSION['user_id']]);

            header("Location: editor.php?project=" . $safe_name);
            exit;
        } else {
            $message = 'Már létezik';
        }
    }
}
?>

<h1>Új projekt</h1>

<form method="POST">
    <input type="text" name="project_name" placeholder="Projekt neve">
    <button type="submit">Létrehozás</button>
</form>

<p><?php echo $message; ?></p>