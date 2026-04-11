<?php
session_start();

$pdo = new PDO('mysql:host=localhost;dbname=project_db', 'root', '');

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'];
    $email = $_SESSION['verify_email'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=? AND verification_code=?");
    $stmt->execute([$email, $code]);

    if ($stmt->fetch()) {
        $pdo->prepare("UPDATE users SET is_verified=1 WHERE email=?")->execute([$email]);
        $msg = "Sikeres aktiválás! Most már bejelentkezhetsz.";
    } else {
        $msg = "Hibás kód!";
    }
}
?>

<form method="POST">
    <input type="text" name="code" placeholder="Kód">
    <button>Aktiválás</button>
</form>

<p><?php echo $msg; ?></p>