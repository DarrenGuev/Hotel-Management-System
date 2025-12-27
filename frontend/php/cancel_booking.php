<?php
session_start();
include '../../dbconnect/connect.php';

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: ../login.php?error=Please login to cancel a booking");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingID = (int)$_POST['bookingID'];
    $userID = (int)$_SESSION['userID'];
    $verifyBooking = $conn->prepare("SELECT bookingID, bookingStatus FROM bookings WHERE bookingID = ? AND userID = ?");
    $verifyBooking->bind_param("ii", $bookingID, $userID);
    $verifyBooking->execute();
    $result = $verifyBooking->get_result();
    
    if ($result->num_rows === 0) {
        header("Location: ../bookings.php?error=Booking not found");
        exit();
    }
    
    $booking = $result->fetch_assoc();
    
    if ($booking['bookingStatus'] !== 'pending') {
        header("Location: ../bookings.php?error=Only pending bookings can be cancelled");
        exit();
    }
    
    // Update booking status to cancelled
    $cancelBooking = $conn->prepare("UPDATE bookings SET bookingStatus = 'cancelled', paymentStatus = 'refunded', updatedAt = NOW() WHERE bookingID = ? AND userID = ?");
    $cancelBooking->bind_param("ii", $bookingID, $userID);
    
    if ($cancelBooking->execute()) {
        header("Location: ../bookings.php?success=Booking cancelled successfully");
    } else {
        header("Location: ../bookings.php?error=Failed to cancel booking. Please try again.");
    }
    $cancelBooking->close();
    exit();
} else {
    header("Location: ../bookings.php");
    exit();
}
?>
