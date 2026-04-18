<?php
require_once __DIR__ . '/../config.php';

$message = '';
$messageType = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '') {
        $message = 'Felhasználónév megadása kötelező!';
        $messageType = 'error';
    } elseif ($password === '') {
        $message = 'Jelszó megadása kötelező!';
        $messageType = 'error';
    } elseif (strlen($username) < 3) {
        $message = 'Felhasználónév legalább 3 karakter hosszú kell legyen!';
        $messageType = 'error';
    } elseif (strlen($password) < 6) {
        $message = 'Jelszó legalább 6 karakter hosszú kell legyen!';
        $messageType = 'error';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);

        if ($stmt->fetch()) {
            $message = 'Ez a felhasználónév már foglalt!';
            $messageType = 'error';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (username, password, role) VALUES (?, ?, ?)');

            try {
                $stmt->execute([$username, $hashed_password, 'user']);
                $message = 'Sikeres regisztráció! Most bejelentkezhet.';
                $messageType = 'success';
                $username = '';
            } catch (PDOException $e) {
                $message = 'Regisztráció sikertelen. Próbálja meg később.';
                $messageType = 'error';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regisztráció</title>
    <link rel="stylesheet" href="reg.css">
</head>
<body>
    <div class="page-shell">
        <header class="topbar">
            <div class="brand">ArchitecturalFluidity</div>
        </header>
        <main class="auth-card">
            <div class="auth-header">
                <p class="eyebrow">Új fiók létrehozása</p>
                <h1>Regisztráció</h1>
                <p class="subtext">Hozzon létre felhasználót, majd jelentkezzen be.</p>
            </div>
            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <form method="POST" class="auth-form" novalidate>
                <label>
                    <span class="label-text">Felhasználónév</span>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" placeholder="Felhasználónév" required>
                </label>
                <label>
                    <span class="label-text">Jelszó</span>
                    <input type="password" name="password" placeholder="Jelszó" required>
                </label>
                <button type="submit" class="button-primary">Regisztráció</button>
            </form>
            <div class="auth-footer">
                <p>Van már fiókja?</p>
                <a class="button-secondary" href="/dashboard2/bej/login.php">Bejelentkezés</a>
            </div>
        </main>
    </div>
</body>
</html>
