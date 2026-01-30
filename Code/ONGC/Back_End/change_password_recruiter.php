<?php
session_start();
require "db.php";

// Set header to return JSON (required for the AJAX toast notifications)
header('Content-Type: application/json');

if(!isset($_SESSION['company_id'])){
    echo json_encode(['status' => 'error', 'message' => 'Session expired. Please login again.']);
    exit;
}

$company_id = $_SESSION['company_id'];

if(isset($_POST['change_password'])){
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    // 1. Basic validation
    if(empty($current) || empty($new) || empty($confirm)){
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    if($new !== $confirm){
        echo json_encode(['status' => 'error', 'message' => 'New passwords do not match.']);
        exit;
    }

    // 2. Check current password
    // Using prepared statements for better security
    $stmt = mysqli_prepare($conn, "SELECT password FROM companies WHERE company_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $company_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if(!$row || !password_verify($current, $row['password'])){
        echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect.']);
        exit;
    }

    // 3. Update with new hash
    $hashed = password_hash($new, PASSWORD_DEFAULT);
    $update_stmt = mysqli_prepare($conn, "UPDATE companies SET password = ? WHERE company_id = ?");
    mysqli_stmt_bind_param($update_stmt, "si", $hashed, $company_id);
    
    if(mysqli_stmt_execute($update_stmt)){
        echo json_encode(['status' => 'success', 'message' => 'Password changed successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error. Please try again.']);
    }
    exit;
}
?>