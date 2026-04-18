<?php
session_start();

// Session ellenőrzés
if (isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit();
}
?>
