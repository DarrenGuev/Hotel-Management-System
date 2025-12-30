<?php
session_start();
include '../../dbconnect/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize form data
    $userName = trim($_POST['userName'] ?? '');
    $userEmail = trim($_POST['userEmail'] ?? '');
    $rating = intval($_POST['rating'] ?? 0);
    $roomID = intval($_POST['roomID'] ?? 0);
    $comments = trim($_POST['comments'] ?? '');

    // Validation
    $errors = [];

    if (empty($userName) || strlen($userName) > 300) {
        $errors[] = "Please enter a valid name (max 300 characters).";
    }

    if (empty($userEmail) || !filter_var($userEmail, FILTER_VALIDATE_EMAIL) || strlen($userEmail) > 200) {
        $errors[] = "Please enter a valid email address.";
    }

    if ($rating < 1 || $rating > 5) {
        $errors[] = "Please select a rating between 1 and 5 stars.";
    }

    if ($roomID <= 0) {
        $errors[] = "Please select a valid room.";
    }

    if (empty($comments) || strlen($comments) < 10) {
        $errors[] = "Please enter comments (minimum 10 characters).";
    }

    // Verify room exists
    if ($roomID > 0) {
        $roomCheck = $conn->prepare("SELECT roomID FROM rooms WHERE roomID = ?");
        $roomCheck->bind_param("i", $roomID);
        $roomCheck->execute();
        if ($roomCheck->get_result()->num_rows === 0) {
            $errors[] = "Selected room does not exist.";
        }
        $roomCheck->close();
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO feedback (userName, userEmail, rating, roomID, comments) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $userName, $userEmail, $rating, $roomID, $comments);

        if ($stmt->execute()) {
            $_SESSION['feedback_success'] = "Thank you for your feedback! Your review has been submitted successfully.";
            $stmt->close();
            $conn->close();
            header("Location: ../userFeedback.php?success=1");
            exit();
        } else {
            $_SESSION['feedback_error'] = "An error occurred while submitting your feedback. Please try again.";
            $stmt->close();
            $conn->close();
            header("Location: ../userFeedback.php?error=1");
            exit();
        }
    } else {
        $_SESSION['feedback_error'] = implode("<br>", $errors);
        $conn->close();
        header("Location: ../userFeedback.php?error=1");
        exit();
    }
} else {
    header("Location: ../userFeedback.php");
    exit();
}
