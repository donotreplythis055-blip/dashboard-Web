<?php
session_start();
session_destroy();
header("Location: /dashboard2/bej/login.php");
exit();
?>