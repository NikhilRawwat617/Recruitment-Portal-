<?php
session_start();
require "db.php";

$aadhar = $_POST['aadhar'];
$password = $_POST['password'];

$query = mysqli_query($conn, "SELECT * FROM user_info WHERE aadhar='$aadhar' LIMIT 1");

if(mysqli_num_rows($query) == 0){
    $_SESSION['login_error'] = "User does not exist!";
    header("Location: ../Front_End/user_login_page.php");
    exit;
}

$user = mysqli_fetch_assoc($query);
if(!password_verify($password, $user['password'])){
    $_SESSION['login_error'] = "Incorrect password!";
    header("Location: ../Front_End/user_login_page.php");
    exit;
}

// login success
$_SESSION['user_id'] = $user['user_id'];
header("Location: ../Front_End/user_main_panel.php");
?>
