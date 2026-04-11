<?php
session_start();

$error = '';
$username = '';

// Ha már be van jelentkezve
if (isset($_SESSION['user_id'])) {
    header('Location: /dashboard2/fd2/fd.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'A felhasználónév és a jelszó megadása kötelező.';
    } else {
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=project_db', 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare('SELECT id, username, password, role, is_verified FROM users WHERE username = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {

                if (!$user['is_verified']) {
                    $error = "Erősítsd meg az emailed!";
                } else {
                    session_regenerate_id(true);

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];

                    header('Location: /dashboard2/fd2/fd.php');
                    exit;
                }

            } else {
                $error = 'Hibás felhasználónév vagy jelszó.';
            }

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
                <p class="subtext">Lépjen be a fiókjába.</p>
            </div>

            <?php if ($error): ?>
                <div class="message error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <label>
                    <span>Felhasználónév</span>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                </label>

                <label>
                    <span>Jelszó</span>
                    <input type="password" name="password" required>
                </label>

                <button type="submit">Bejelentkezés</button>
            </form>

            <p>Nincs fiók?</p>
            <a href="/dashboard2/reg/create_user.php">Regisztráció</a>
        </main>
    </div>
</body>
</html>
