<?php
session_start();
include '../connect.php';

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../frontend/login.php?error=Access denied");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingID = (int)$_POST['bookingID'];
    $bookingStatus = $_POST['bookingStatus'];
    $paymentStatus = $_POST['paymentStatus'];
    $notes = $_POST['notes'] ?? '';

    $validBookingStatuses = ['pending', 'confirmed', 'cancelled', 'completed'];
    $validPaymentStatuses = ['pending', 'paid', 'refunded'];

    if (!in_array($bookingStatus, $validBookingStatuses) || !in_array($paymentStatus, $validPaymentStatuses)) {
        header("Location: ../admin.php?error=Invalid status");
        exit();
    }

    $updateQuery = $conn->prepare("UPDATE bookings SET bookingStatus = ?, paymentStatus = ?, updatedAt = NOW() WHERE bookingID = ?");
    $updateQuery->bind_param("ssi", $bookingStatus, $paymentStatus, $bookingID);

    if ($updateQuery->execute()) {
        header("Location: ../admin.php?success=Booking updated successfully");
    } else {
        header("Location: ../admin.php?error=Failed to update booking");
    }
    exit();
}

header("Location: ../admin.php");
exit();
?>
