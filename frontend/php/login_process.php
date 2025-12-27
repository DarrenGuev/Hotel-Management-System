<?php
session_start();
include '../../dbconnect/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $hashedPassword = md5($password);

    $loginQuery = "SELECT * FROM users WHERE username = '$username' AND password = '$hashedPassword'";
    $result = executeQuery($loginQuery);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        $_SESSION['userID'] = $user['userID'];
        $_SESSION['firstName'] = $user['firstName'];
        $_SESSION['lastName'] = $user['lastName'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

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