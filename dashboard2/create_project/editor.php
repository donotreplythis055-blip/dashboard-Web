<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die('Nincs belépve');
}

$pdo = new PDO('mysql:host=localhost;dbname=project_db', 'root', '');

$project = $_GET['project'] ?? '';

// jogosultság check
$stmt = $pdo->prepare("SELECT * FROM projects WHERE name=? AND user_id=?");
$stmt->execute([$project, $_SESSION['user_id']]);

if (!$stmt->fetch()) {
    die('Nincs hozzáférés');
}

$base = __DIR__ . '/projects/' . $project;

// fájlok
$files = array_values(array_diff(scandir($base), ['.', '..']));
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Pro Editor</title>
<link rel="stylesheet" href="editor2.css">

<!-- Monaco CDN -->
<script src="https://unpkg.com/monaco-editor@0.44.0/min/vs/loader.js"></script>
</head>
<body>

<div class="editor-container">

    <!-- BAL OLDAL -->
    <div class="sidebar">
        <h3>Fájlok</h3>

        <?php foreach ($files as $f): ?>
            <div class="file" onclick="loadFile('<?php echo $f; ?>')">
                <?php echo htmlspecialchars($f); ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- JOBB OLDAL -->
    <div class="main">
        <div class="topbar">
            <span id="filename">index.php</span>
            <button onclick="saveFile()">💾 Mentés</button>
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
const project = "<?php echo $project; ?>";

require.config({ paths: { vs: 'https://unpkg.com/monaco-editor@0.44.0/min/vs' }});

require(['vs/editor/editor.main'], function () {

    editor = monaco.editor.create(document.getElementById('editor'), {
        value: '',
        language: 'php',
        theme: 'vs-dark',
        automaticLayout: true
    });

    loadFile(currentFile);
});

// fájl betöltés
function loadFile(file) {
    currentFile = file;

    fetch('load_file.php?project=' + project + '&file=' + file)
        .then(res => res.text())
        .then(data => {
            editor.setValue(data);
            document.getElementById('filename').innerText = file;
        });
}

// mentés
function saveFile() {
    fetch('save_file.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'project=' + project + '&file=' + currentFile + '&code=' + encodeURIComponent(editor.getValue())
    })
    .then(res => res.text())
    .then(msg => alert(msg));
}

</script>
<script>
function goBack() {
    window.location.href = "/dashboard2/dashboard/dashboard.php";
}
editor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyS, function () {
    saveFile();
});

editor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyF, function () {
    editor.getAction('actions.find').run();
});

editor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyH, function () {
    editor.getAction('editor.action.startFindReplaceAction').run();
});

editor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyZ, function () {
    editor.trigger('', 'undo', null);
});

editor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyY, function () {
    editor.trigger('', 'redo', null);
});

editor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyA, function () {
    editor.trigger('', 'selectAll', null);
});

editor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyD, function () {
    editor.getAction('editor.action.addSelectionToNextFindMatch').run();
});

editor.addCommand(monaco.KeyMod.Alt | monaco.KeyCode.UpArrow, function () {
    editor.trigger('', 'editor.action.copyLinesUpAction', null);
});

editor.addCommand(monaco.KeyMod.Alt | monaco.KeyCode.DownArrow, function () {
    editor.trigger('', 'editor.action.copyLinesDownAction', null);
});

editor.addCommand(monaco.KeyMod.Shift | monaco.KeyMod.Alt | monaco.KeyCode.DownArrow, function () {
    editor.trigger('', 'editor.action.insertCursorBelow', null);
});

editor.addCommand(monaco.KeyMod.Shift | monaco.KeyMod.Alt | monaco.KeyCode.UpArrow, function () {
    editor.trigger('', 'editor.action.insertCursorAbove', null);
});

</script>

</body>
</html>