<?php
session_start();
require "db.php";

if(isset($_POST['login'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Check if company exists
    $res = mysqli_query($conn,"SELECT * FROM companies WHERE email='$email' LIMIT 1");

    if(mysqli_num_rows($res) == 0){
        // User does not exist
        $_SESSION['login_error'] = "User does not exist";
        header("Location: ../Front_End/homePage_recruit.php");
        exit;
    }

    $user = mysqli_fetch_assoc($res);

    if(password_verify($password, $user['password'])){
        // Login successful
        $_SESSION['company_id'] = $user['company_id'];
        $_SESSION['company_name'] = $user['company_name'];
        $_SESSION['company_photo'] = $user['company_photo'];
        header("Location: ../Front_End/recuriter_main_panel.php");
        exit;
    } else {
        $_SESSION['login_error'] = "Incorrect password";
        header("Location: ../Front_End/homePage_recruit.php");
        exit;
    }
}
?>
