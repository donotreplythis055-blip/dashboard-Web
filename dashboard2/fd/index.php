<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// a többi kód...
session_start();

// Session ellenőrzés
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard2\fd\index.html');
    exit();
}
?>
