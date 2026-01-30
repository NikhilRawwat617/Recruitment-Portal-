<?php
session_start();
require "db.php";

if(!isset($_SESSION['company_id'])){
    header("Location: ../Front_End/homePage_recruit.php");
    exit;
}

$company_id = $_SESSION['company_id'];

// Check if 'id' is provided
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("Invalid Job ID");
}

$job_id = $_GET['id'];

// Delete only if the job belongs to this company
$stmt = mysqli_prepare($conn, "DELETE FROM jobs WHERE job_id = ? AND company_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $job_id, $company_id);

if(mysqli_stmt_execute($stmt)){
    header("Location: ../Front_End/recuriter_main_panel.php");
    exit;
} else {
    die("Failed to delete job: " . mysqli_error($conn));
}
