<?php
require "db.php";

/* FORM DATA */
$full_name  = $_POST['full_name'];
$aadhar     = $_POST['aadhar'];
$dob        = $_POST['dob'];
$gender     = $_POST['gender'];
$mobile     = $_POST['mobile'];
$experience = $_POST['experience'];
$address    = $_POST['address'];
$other_info = $_POST['other_info'];
$password   = password_hash($_POST['password'], PASSWORD_DEFAULT);

/* PHOTO UPLOAD */
$photoName = time() . "_" . $_FILES['photo']['name'];
$photoTmp  = $_FILES['photo']['tmp_name'];
move_uploaded_file($photoTmp, "../uploads/user_photos/" . $photoName);

/* INSERT USER */
$query = "
INSERT INTO user_info
(full_name, aadhar, dob, gender, mobile, experience, address, other_info, photo, password)
VALUES
('$full_name','$aadhar','$dob','$gender','$mobile','$experience','$address','$other_info','$photoName','$password')
";

mysqli_query($conn, $query);

/* REDIRECT */
header("Location: ../Front_End/user_login_page.php");
exit;
