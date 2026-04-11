<?php
session_start();

require __DIR__ . '/../phpmailer/src/PHPMailer.php';
require __DIR__ . '/../phpmailer/src/SMTP.php';
require __DIR__ . '/../phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$msg = '';

try {
    $pdo = new PDO(
        "pgsql:host=dpg-d7cqqn7lk1mc73ebedug-a;port=5432;dbname=dashboard_db_x12n",
        "dashboard_db_x12n_user",
        "5OiwanCxna3FJaYiIvdiV80yy7dnD3UT"
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $msg = "Adatbázis hiba!";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $msg = "Hiányzó adatok!";
    } else {

        $check = $pdo->prepare("SELECT id FROM users WHERE email=?");
        $check->execute([$email]);

        if ($check->fetch()) {
            $msg = "Ez az email már regisztrálva van!";
        } else {

            $code = rand(100000, 999999);

            $stmt = $pdo->prepare("
                INSERT INTO users (username, email, password, verification_code, is_verified) 
                VALUES (?, ?, ?, ?, FALSE)
            ");

            $stmt->execute([
                $email,
                $email,
                password_hash($password, PASSWORD_DEFAULT),
                $code
            ]);

            $_SESSION['verify_email'] = $email;

            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'TE_EMAIL@gmail.com';
                $mail->Password = 'APP_PASSWORD';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('TE_EMAIL@gmail.com', 'Rendszer');
                $mail->addAddress($email);

                $mail->Subject = 'Megerősítő kód';
                $mail->Body = "A kódod: $code";

                $mail->send();

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regisztráció</title>
    <link rel="stylesheet" href="/dashboard2/reg/reg.css">
</head>
<body>

<div class="container">
    <h1>Regisztráció</h1>

    <form method="POST">
        <input type="email" name="email" placeholder="Email cím" required>
        <input type="password" name="password" placeholder="Jelszó" required>
        <button type="submit">Regisztráció</button>
    </form>

    <?php if ($msg): ?>
        <p><?php echo htmlspecialchars($msg); ?></p>
    <?php endif; ?>

    <p>Van már fiókod?</p>
    <a href="/dashboard2/bej/login.php">Bejelentkezés</a>
</div>

</body>
</html>
