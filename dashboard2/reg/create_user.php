<?php
session_start();

// 📦 PHPMailer betöltés (FIGYELJ AZ ÚTVONALRA!)
require __DIR__ . '/../phpmailer/src/PHPMailer.php';
require __DIR__ . '/../phpmailer/src/SMTP.php';
require __DIR__ . '/../phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 🛢️ adatbázis
$pdo = new PDO('mysql:host=localhost;dbname=project_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $msg = "Hiányzó adatok!";
    } else {

        // 🔍 ellenőrzés (email már létezik?)
        $check = $pdo->prepare("SELECT id FROM users WHERE email=?");
        $check->execute([$email]);

        if ($check->fetch()) {
            $msg = "Ez az email már regisztrálva van!";
        } else {

            // 🔐 kód generálás
            $code = rand(100000, 999999);

            // 💾 mentés (email = username)
            $stmt = $pdo->prepare("
                INSERT INTO users (username, email, password, verification_code, is_verified) 
                VALUES (?, ?, ?, ?, 0)
            ");

            $stmt->execute([
                $email, // username
                $email,
                password_hash($password, PASSWORD_DEFAULT),
                $code
            ]);

            $_SESSION['verify_email'] = $email;

            // 📧 EMAIL KÜLDÉS
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;

                $mail->Username = ''; // ❗ ide a saját gmail
                $mail->Password = '';       // ❗ ide az app password

                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('TE_EMAIL@gmail.com', 'Rendszer');
                $mail->addAddress($email);

                $mail->Subject = 'Megerősítő kód';
                $mail->Body = "A megerősítő kódod: $code";

                $mail->send();

                // 👉 átirányítás verify oldalra
                header("Location: verify.php");
                exit;

            } catch (Exception $e) {
                $msg = "Email hiba: " . $mail->ErrorInfo;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Regisztráció</title>
    <link rel="stylesheet" href="reg.css">
</head>
<body>

<div class="container">
    <h1>Regisztráció</h1>

    <form method="POST" class="form">
        <input type="email" name="email" placeholder="Email cím" required>
        <input type="password" name="password" placeholder="Jelszó" required>

        <button type="submit">Regisztráció</button>
    </form>

    <?php if ($msg): ?>
        <p class="message"><?php echo htmlspecialchars($msg); ?></p>
    <?php endif; ?>
</div>

</body>
</html>