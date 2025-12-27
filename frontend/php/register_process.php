<?php
session_start();
include '../../dbconnect/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastName = mysqli_real_escape_string($conn, $_POST['lastname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($password !== $confirmPassword) {
        header("Location: ../register.php?error=Passwords do not match");
        exit();
    }

    $checkEmail = "SELECT * FROM users WHERE email = '$email'";
    $emailResult = executeQuery($checkEmail);
    if (mysqli_num_rows($emailResult) > 0) {
        header("Location: ../register.php?error=Email already registered");
        exit();
    }

    $checkUsername = "SELECT * FROM users WHERE username = '$username'";
    $usernameResult = executeQuery($checkUsername);
    if (mysqli_num_rows($usernameResult) > 0) {
        header("Location: ../register.php?error=Username already taken");
        exit();
    }

    $hashedPassword = md5($password);
    $insertQuery = "INSERT INTO users (firstName, lastName, email, username, phoneNumber, password, role) 
                    VALUES ('$firstName', '$lastName', '$email', '$username', '$phone', '$hashedPassword', 'user')";
    
    if (executeQuery($insertQuery)) {
        header("Location: ../login.php?success=Registration successful! Please login.");
        exit();
    } else {
        header("Location: ../register.php?error=Registration failed. Please try again.");
        exit();
    }
} else {
    header("Location: ../register.php");
    exit();
}
?>