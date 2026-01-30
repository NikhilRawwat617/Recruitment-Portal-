<?php
session_start();
require "../Back_End/db.php";

$company_name   = trim($_POST['company_name']);
$recruiter_name = trim($_POST['recruiter_name']);
$email          = trim($_POST['email']);
$phone          = trim($_POST['phone']);
$website        = trim($_POST['website']);
$password       = $_POST['password'];
$confirm        = $_POST['confirm_password'];

/* Validation */
if(!$company_name || !$recruiter_name || !$email || !$phone || !$password || !$confirm) die("All fields required");
if($password !== $confirm) die("Passwords do not match");

/* Photo upload */
$allowedTypes = ['image/jpeg','image/png','image/webp'];
if(!in_array($_FILES['company_photo']['type'],$allowedTypes)) die("Invalid image type");

$ext = pathinfo($_FILES['company_photo']['name'], PATHINFO_EXTENSION);
$photoName = uniqid("company_", true).".".$ext;
move_uploaded_file($_FILES['company_photo']['tmp_name'], "../uploads/company_photos/".$photoName);

/* Check existing email */
$stmt = mysqli_prepare($conn,"SELECT company_id FROM companies WHERE email=?");
mysqli_stmt_bind_param($stmt,"s",$email);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
if(mysqli_stmt_num_rows($stmt)>0) die("Email already exists");
mysqli_stmt_close($stmt);

/* Insert company */
$hashed = password_hash($password, PASSWORD_DEFAULT);
$stmt = mysqli_prepare($conn,"INSERT INTO companies (company_name,recruiter_name,email,phone,website,password,company_photo) VALUES (?,?,?,?,?,?,?)");
mysqli_stmt_bind_param($stmt,"sssssss",$company_name,$recruiter_name,$email,$phone,$website,$hashed,$photoName);
mysqli_stmt_execute($stmt);

/* Log in user */
$company_id = mysqli_insert_id($conn);
$_SESSION['company_id'] = $company_id;
$_SESSION['company_name'] = $company_name;
$_SESSION['company_photo'] = $photoName;

/* Redirect to dashboard */
header("Location: ../Front_End/recuriter_main_panel.php");
exit;
?>
