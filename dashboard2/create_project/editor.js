editor.addCommand(monaco.KeyCode.Tab, function () {
    const value = editor.getValue().trim();

    if (value === "!") {
        editor.setValue(`<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
</body>
</html>`);
    }
});