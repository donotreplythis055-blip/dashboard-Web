<?php
session_start();

$error = '';
$username = '';

try {
    $pdo = new PDO(
        "pgsql:host=dpg-d7cqqn7lk1mc73ebedug-a;port=5432;dbname=dashboard_db_x12n",
        "dashboard_db_x12n_user",
        "5OiwanCxna3FJaYiIvdiV80yy7dnD3UT"
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $error = "Adatbázis hiba!";
}

// ha már be van lépve
if (isset($_SESSION['user_id'])) {
    header('Location: /dashboard2/fd2/fd.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$username || !$password) {
        $error = "Hiányzó adatok!";
    } else {

        $stmt = $pdo->prepare("SELECT id, username, password, role, is_verified FROM users WHERE username = ?");
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

                header("Location: /dashboard2/fd2/fd.php");
                exit;
            }

        } else {
            $error = "Hibás felhasználónév vagy jelszó!";
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
    <link rel="stylesheet" href="/dashboard2/bej/bej.css">
</head>
<body>

<div class="container">
    <h1>Bejelentkezés</h1>

    <form method="POST">
        <input type="text" name="username" placeholder="Felhasználónév vagy email" 
               value="<?php echo htmlspecialchars($username); ?>" required>

        <input type="password" name="password" placeholder="Jelszó" required>

        <button type="submit">Bejelentkezés</button>
    </form>

    <?php if ($error): ?>
        <p><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <p>Nincs fiókod?</p>
    <a href="/dashboard2/reg/create_user.php">Regisztráció</a>
</div>

</body>
</html>
