<?php
session_start();

$project = $_GET['project'];
$file = $_GET['file'];

$base = __DIR__ . '/projects/' . $project;
$path = realpath($base . '/' . $file);

if (!$path || strpos($path, realpath($base)) !== 0) {
    die('Tiltva');
}

echo file_get_contents($path);