<?php
session_start();
session_destroy();
header("Location: ../Front_End/homePage_recruit.php");
exit;
?>
