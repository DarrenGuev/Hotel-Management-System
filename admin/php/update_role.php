<?php
session_start();
include '../connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userID = mysqli_real_escape_string($conn, $_POST['userID']);
    $newRole = mysqli_real_escape_string($conn, $_POST['role']);
    
    // Validate role
    if ($newRole !== 'user' && $newRole !== 'admin') {
        header("Location: ../admin.php?error=Invalid role selected");
        exit();
    }
    
    // Update user role
    $updateQuery = "UPDATE users SET role = '$newRole' WHERE userID = '$userID'";
    
    if (executeQuery($updateQuery)) {
        header("Location: ../admin.php?success=User role updated successfully");
    } else {
        header("Location: ../admin.php?error=Failed to update user role");
    }
    exit();
} else {
    header("Location: ../admin.php");
    exit();
}
?>
