<?php
session_start();

if ($_SESSION['role'] !== 'moderator') {
    die('Nincs jogosultság');
}

$pdo = new PDO('mysql:host=localhost;dbname=project_db', 'root', '');

$id = $_POST['id'];

$stmt = $pdo->prepare("UPDATE projects SET status='approved' WHERE id=?");
$stmt->execute([$id]);

header('Location: http://localhost/dashboard2/moderation/panel.php');