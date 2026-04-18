<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /dashboard2/bej/login.php');
    exit;
}

require_once __DIR__ . '/../config.php';

$project = $_GET['project'] ?? '';
$stmt = $pdo->prepare('SELECT id FROM projects WHERE name = ? AND user_id = ? LIMIT 1');
$stmt->execute([$project, $_SESSION['user_id']]);

if (!$stmt->fetch()) {
    die('Nincs hozzáférés');
}

$base = realpath(__DIR__ . '/projects/' . $project);
if ($base === false) {
    die('A projekt mappája nem található.');
}

$files = [];
foreach (array_diff(scandir($base), ['.', '..']) as $f) {
    if (is_file($base . DIRECTORY_SEPARATOR . $f)) {
        $files[] = $f;
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
<meta charset="UTF-8">
<title>Pro Editor</title>
<link rel="stylesheet" href="editor.css">
<script src="https://unpkg.com/monaco-editor@0.44.0/min/vs/loader.js"></script>
</head>
<body>
<div class="editor-container">
    <div class="sidebar">
        <h3>Fájlok</h3>
        <?php foreach ($files as $f): ?>
            <div class="file" onclick="loadFile('<?php echo rawurlencode($f); ?>')"><?php echo htmlspecialchars($f); ?></div>
        <?php endforeach; ?>
    </div>
    <div class="main">
        <div class="topbar">
            <button onclick="goBack()" class="back-btn">← Dashboard</button>
            <span id="filename">index.php</span>
            <button onclick="saveFile()">💾 Mentés</button>
        </div>
        <div id="editor"></div>
    </div>
</div>
<script>
let editor;
let currentFile = 'index.php';
const project = <?php echo json_encode($project); ?>;
require.config({ paths: { vs: 'https://unpkg.com/monaco-editor@0.44.0/min/vs' }});
require(['vs/editor/editor.main'], function () {
    editor = monaco.editor.create(document.getElementById('editor'), {
        value: '', language: 'php', theme: 'vs-dark', automaticLayout: true
    });
    loadFile(encodeURIComponent(currentFile));
});
function loadFile(file) {
    currentFile = decodeURIComponent(file);
    fetch('load_file.php?project=' + encodeURIComponent(project) + '&file=' + file)
        .then(res => res.text())
        .then(data => {
            editor.setValue(data);
            document.getElementById('filename').innerText = currentFile;
        });
}
function saveFile() {
    fetch('save_file.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'project=' + encodeURIComponent(project) + '&file=' + encodeURIComponent(currentFile) + '&code=' + encodeURIComponent(editor.getValue())
    })
    .then(res => res.text())
    .then(msg => alert(msg));
}
function goBack() { window.location.href = '/dashboard2/dashboard/dashboard.php'; }
</script>
</body>
</html>
