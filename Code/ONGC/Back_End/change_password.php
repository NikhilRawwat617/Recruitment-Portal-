<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id'])) {
    $_SESSION['msg'] = "Unauthorized access";
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $_SESSION['msg'] = "Invalid request";
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

$user_id = $_SESSION['user_id'];
$current = $_POST['current'];
$new     = $_POST['new'];

/* Fetch password */
$stmt = $conn->prepare("SELECT password FROM user_info WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

/* Verify current password */
if (!password_verify($current, $user['password'])) {
    $_SESSION['msg'] = "Current password is incorrect";
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

/* Update password */
$new_hash = password_hash($new, PASSWORD_DEFAULT);
$update = $conn->prepare("UPDATE user_info SET password = ? WHERE user_id = ?");
$update->bind_param("si", $new_hash, $user_id);

if ($update->execute()) {
    $_SESSION['msg'] = "Password updated successfully";
} else {
    $_SESSION['msg'] = "Failed to update password";
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
