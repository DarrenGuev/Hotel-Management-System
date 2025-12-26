<?php
session_start();
include '../connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingID = mysqli_real_escape_string($conn, $_POST['bookingID']);
    $bookingStatus = mysqli_real_escape_string($conn, $_POST['bookingStatus']);
    $paymentStatus = mysqli_real_escape_string($conn, $_POST['paymentStatus']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    
    // Validate statuses
    $validBookingStatuses = ['pending', 'confirmed', 'cancelled', 'completed'];
    $validPaymentStatuses = ['pending', 'paid', 'refunded'];
    
    if (!in_array($bookingStatus, $validBookingStatuses) || !in_array($paymentStatus, $validPaymentStatuses)) {
        header("Location: ../admin.php?error=Invalid status selected");
        exit();
    }
    
    // Update booking
    $updateQuery = "UPDATE bookings SET bookingStatus = '$bookingStatus', paymentStatus = '$paymentStatus', notes = '$notes' WHERE bookingID = '$bookingID'";
    
    if (executeQuery($updateQuery)) {
        header("Location: ../admin.php?success=Booking status updated successfully");
    } else {
        header("Location: ../admin.php?error=Failed to update booking status");
    }
    exit();
} else {
    header("Location: ../admin.php");
    exit();
}
?>
