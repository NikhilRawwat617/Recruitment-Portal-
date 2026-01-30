<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id'], $_POST['job_id'])) {
    header("Location: ../Front_End/user_login_page.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$job_id  = (int)$_POST['job_id'];

$check = mysqli_query($conn,
    "SELECT 1 FROM applications WHERE user_id=$user_id AND job_id=$job_id"
);

if (mysqli_num_rows($check) == 0) {
    mysqli_query($conn,
        "INSERT INTO applications (user_id, job_id) VALUES ($user_id,$job_id)"
    );
}

header("Location: ../Front_End/user_main_panel.php");
exit;
