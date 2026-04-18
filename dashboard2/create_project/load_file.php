<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    die('Nincs belépve');
}
require_once __DIR__ . '/../config.php';
$project = $_GET['project'] ?? '';
$file = $_GET['file'] ?? '';
$stmt = $pdo->prepare('SELECT id FROM projects WHERE name = ? AND user_id = ? LIMIT 1');
$stmt->execute([$project, $_SESSION['user_id']]);
if (!$stmt->fetch()) {
    http_response_code(403);
    die('Nincs hozzáférés');
}
$base = realpath(__DIR__ . '/projects/' . $project);
$path = realpath($base . '/' . $file);
if (!$base || !$path || strpos($path, $base) !== 0 || !is_file($path)) {
    http_response_code(400);
    die('Tiltva');
}
echo file_get_contents($path);
