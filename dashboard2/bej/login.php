<?php
session_start();
require __DIR__ . '/../db.php';
require_once __DIR__ . '/../config.php';

$error = '';
$username = '';

if (isset($_SESSION['user_id'])) {
    header('Location: /dashboard2/fd2/fd.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'A felhasználónév és a jelszó megadása kötelező.';
    } else {
        try {
            $stmt = $pdo->prepare('SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1');
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int)$user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'] ?: 'user';

                header('Location: /dashboard2/fd2/fd.php');
                exit;
            }

            $error = 'Hibás felhasználónév vagy jelszó.';
        } catch (PDOException $e) {
            $error = 'Adatbázis hiba. Próbálja meg később.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bejelentkezés</title>
    <link rel="stylesheet" href="bej.css">
</head>
<body>
    <div class="page-shell">
        <header class="topbar">
            <div class="brand">ArchitecturalFluidity</div>
        </header>
        <main class="auth-card">
            <div class="auth-header">
                <p class="eyebrow">Üdv újra</p>
                <h1>Bejelentkezés</h1>
                <p class="subtext">Lépjen be a fiókjába, hogy elérje a dashboardot.</p>
            </div>
            <?php if ($error): ?>
                <div class="message error"><?php echo htmlspecialchars($error); ?></div>
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
                <button type="submit" class="button-primary">Bejelentkezés</button>
            </form>
            <div class="auth-footer">
                <p>Még nincs fiókja?</p>
                <a class="button-secondary" href="/dashboard2/reg/create_user.php">Regisztráció</a>
            </div>
        </main>
    </div>
</body>
</html>
