<?php
session_start();
require "db.php";

if(!isset($_SESSION['company_id'])){ header("Location: ../Front_End/homePage_recruit.php"); exit; }

$company_id = $_SESSION['company_id'];

if(isset($_POST['edit_profile'])){
    $company_name = trim($_POST['company_name']);
    $photo_sql = "";

    // optional photo update
    if(!empty($_FILES['company_photo']['name'])){
        $allowedTypes = ['image/jpeg','image/png','image/jpg','image/webp'];
        if(in_array($_FILES['company_photo']['type'],$allowedTypes)){
            $ext = pathinfo($_FILES['company_photo']['name'],PATHINFO_EXTENSION);
            $photoName = uniqid("company_",true).".".$ext;
            move_uploaded_file($_FILES['company_photo']['tmp_name'], "../uploads/company_photos/".$photoName);
            $photo_sql = ", company_photo='$photoName'";
        }
    }

    mysqli_query($conn,"UPDATE companies SET company_name='$company_name' $photo_sql WHERE company_id=$company_id");

    $_SESSION['company_name'] = $company_name;
    if(isset($photoName)) $_SESSION['company_photo'] = $photoName;

    header("Location: ../Front_End/recuriter_main_panel.php");
    exit;
}
?>
