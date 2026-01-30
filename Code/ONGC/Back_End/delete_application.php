<?php
session_start();
require "db.php";

if(!isset($_SESSION['user_id'])) exit;

$id = intval($_GET['id']);

mysqli_query($conn,"
DELETE FROM applications 
WHERE application_id=$id AND status='rejected'
");
