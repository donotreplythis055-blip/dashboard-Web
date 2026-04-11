<?php
session_start();

$project = $_POST['project'];
$file = $_POST['file'];
$code = $_POST['code'];

$base = __DIR__ . '/projects/' . $project;
$path = realpath($base . '/' . $file);

if (!$path || strpos($path, realpath($base)) !== 0) {
    die('Tiltva');
}

file_put_contents($path, $code);

echo "Mentve!";