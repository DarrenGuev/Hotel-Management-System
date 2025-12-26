<?php
session_start();
include '../../admin/connect.php';

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: ../login.php?error=Please login to book a room");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userID = $_SESSION['userID'];
    $roomID = mysqli_real_escape_string($conn, $_POST['roomID']);
    $fullName = mysqli_real_escape_string($conn, $_POST['fullName']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phoneNumber = mysqli_real_escape_string($conn, $_POST['phoneNumber']);
    $checkInDate = mysqli_real_escape_string($conn, $_POST['checkInDate']);
    $checkOutDate = mysqli_real_escape_string($conn, $_POST['checkOutDate']);
    $numberOfGuests = mysqli_real_escape_string($conn, $_POST['numberOfGuests']);
    $totalPrice = mysqli_real_escape_string($conn, $_POST['totalPrice']);
    $paymentMethod = mysqli_real_escape_string($conn, $_POST['paymentMethod']);
    
    // Validate dates
    $checkIn = new DateTime($checkInDate);
    $checkOut = new DateTime($checkOutDate);
    $today = new DateTime();
    
    if ($checkIn < $today) {
        header("Location: ../rooms.php?error=Check-in date cannot be in the past");
        exit();
    }
    
    if ($checkOut <= $checkIn) {
        header("Location: ../rooms.php?error=Check-out date must be after check-in date");
        exit();
    }
    
    // Set payment status based on payment method (prototype - cash is pending, others are "paid")
    $paymentStatus = ($paymentMethod === 'cash') ? 'pending' : 'paid';
    
    // Insert booking
    $insertBooking = "INSERT INTO bookings (userID, roomID, fullName, email, phoneNumber, checkInDate, checkOutDate, numberOfGuests, totalPrice, paymentMethod, paymentStatus, bookingStatus) 
                      VALUES ('$userID', '$roomID', '$fullName', '$email', '$phoneNumber', '$checkInDate', '$checkOutDate', '$numberOfGuests', '$totalPrice', '$paymentMethod', '$paymentStatus', 'pending')";
    
    if (executeQuery($insertBooking)) {
        header("Location: ../bookings.php?success=Booking submitted successfully! Waiting for confirmation.");
    } else {
        header("Location: ../rooms.php?error=Failed to submit booking. Please try again.");
    }
    exit();
} else {
    header("Location: ../rooms.php");
    exit();
}
?>
