<?php
session_start();
session_destroy();
header("Location: test_update.php");
exit();
?>