<?php
session_start();
include '../connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userID = mysqli_real_escape_string($conn, $_POST['userID']);
    
    // Prevent deleting admin users (optional safety check)
    $checkQuery = "SELECT role FROM users WHERE userID = '$userID'";
    $result = executeQuery($checkQuery);
    
    if (mysqli_num_rows($result) === 0) {
        header("Location: ../admin.php?error=User not found");
        exit();
    }
    
    // Delete user
    $deleteQuery = "DELETE FROM users WHERE userID = '$userID'";
    
    if (executeQuery($deleteQuery)) {
        header("Location: ../admin.php?success=User deleted successfully");
    } else {
        header("Location: ../admin.php?error=Failed to delete user");
    }
    exit();
} else {
    header("Location: ../admin.php");
    exit();
}
?>
