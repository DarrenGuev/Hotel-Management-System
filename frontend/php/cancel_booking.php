<?php
session_start();
include '../../admin/connect.php';

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: ../login.php?error=Please login to cancel a booking");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userID = $_SESSION['userID'];
    $bookingID = mysqli_real_escape_string($conn, $_POST['bookingID']);
    
    // Verify the booking belongs to the user and is pending
    $checkQuery = "SELECT * FROM bookings WHERE bookingID = '$bookingID' AND userID = '$userID' AND bookingStatus = 'pending'";
    $result = executeQuery($checkQuery);
    
    if (mysqli_num_rows($result) === 0) {
        header("Location: ../bookings.php?error=Booking not found or cannot be cancelled");
        exit();
    }
    
    // Update booking status to cancelled
    $updateQuery = "UPDATE bookings SET bookingStatus = 'cancelled' WHERE bookingID = '$bookingID' AND userID = '$userID'";
    
    if (executeQuery($updateQuery)) {
        header("Location: ../bookings.php?success=Booking cancelled successfully");
    } else {
        header("Location: ../bookings.php?error=Failed to cancel booking. Please try again.");
    }
    exit();
} else {
    header("Location: ../bookings.php");
    exit();
}
?>
