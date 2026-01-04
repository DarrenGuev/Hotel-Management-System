<?php
session_start();
include '../../dbconnect/connect.php';

// Include class autoloader
require_once __DIR__ . '/../../classes/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Use the User model for authentication
    $userModel = new User();
    $user = $userModel->authenticate($username, $password);

    if ($user) {
        // Use Auth class to log in the user
        Auth::login($user);

        if ($user['role'] === 'admin') {
            header("Location: ../../admin/admin.php");
        } else {
            header("Location: ../../index.php");
        }
        exit();
    } else {
        header("Location: ../login.php?error=Invalid username or password");
        exit();
    }
} else {
    header("Location: ../login.php");
    exit();
}
?>