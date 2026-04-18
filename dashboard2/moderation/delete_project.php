<?php
session_start();
if (!isset($_SESSION['user_id']) || (($_SESSION['role'] ?? 'user') !== 'moderator')) {
    die('Nincs jogosultság');
}
require_once __DIR__ . '/../config.php';
$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) { die('Hibás azonosító'); }
$stmt = $pdo->prepare('DELETE FROM projects WHERE id=?');
$stmt->execute([$id]);
header('Location: /dashboard2/moderation/panel.php');
exit;
