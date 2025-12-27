<?php
session_start();
include '../../dbconnect/connect.php';

if (!isset($_SESSION['userID'])) {
    header("Location: ../login.php?error=Please login to book a room");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userID = (int)$_SESSION['userID'];
    $roomID = (int)$_POST['roomID'];
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phoneNumber'];
    $checkInDate = $_POST['checkInDate'];
    $checkOutDate = $_POST['checkOutDate'];
    $numberOfGuests = (int)$_POST['numberOfGuests'];
    $totalPrice = (float)$_POST['totalPrice'];
    $paymentMethod = $_POST['paymentMethod'];
    $checkIn = new DateTime($checkInDate);
    $checkOut = new DateTime($checkOutDate);
    $today = new DateTime();
    $today->setTime(0, 0, 0);
    
    if ($checkIn < $today) {
        header("Location: ../rooms.php?error=Check-in date cannot be in the past");
        exit();
    }
    
    if ($checkOut <= $checkIn) {
        header("Location: ../rooms.php?error=Check-out date must be after check-in date");
        exit();
    }
    
    $validPaymentMethods = ['paypal', 'gcash', 'credit_card', 'debit_card'];
    if (!in_array($paymentMethod, $validPaymentMethods)) {
        header("Location: ../rooms.php?error=Invalid payment method");
        exit();
    }

    $paymentStatus = 'paid';
    $checkAvailability = $conn->prepare("SELECT quantity FROM rooms WHERE roomID = ?");
    $checkAvailability->bind_param("i", $roomID);
    $checkAvailability->execute();
    $roomResult = $checkAvailability->get_result();
    $room = $roomResult->fetch_assoc();
    if (!$room || $room['quantity'] < 1) {
        header("Location: ../rooms.php?error=Room is not available");
        exit();
    }
    
    $checkOverlap = $conn->prepare("SELECT COUNT(*) as count FROM bookings 
                                    WHERE roomID = ? 
                                    AND bookingStatus NOT IN ('cancelled', 'completed')
                                    AND ((checkInDate <= ? AND checkOutDate > ?) 
                                    OR (checkInDate < ? AND checkOutDate >= ?)
                                    OR (checkInDate >= ? AND checkOutDate <= ?))");
    $checkOverlap->bind_param("issssss", $roomID, $checkOutDate, $checkInDate, $checkOutDate, $checkInDate, $checkInDate, $checkOutDate);
    $checkOverlap->execute();
    $overlapResult = $checkOverlap->get_result();
    $overlap = $overlapResult->fetch_assoc();
    
    if ($overlap['count'] >= $room['quantity']) {
        header("Location: ../rooms.php?error=Room is not available for the selected dates");
        exit();
    }
    
    $insertBooking = $conn->prepare("INSERT INTO bookings (userID, roomID, fullName, email, phoneNumber, checkInDate, checkOutDate, numberOfGuests, totalPrice, paymentMethod, paymentStatus, bookingStatus) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
    $insertBooking->bind_param("iisssssisss", $userID, $roomID, $fullName, $email, $phoneNumber, $checkInDate, $checkOutDate, $numberOfGuests, $totalPrice, $paymentMethod, $paymentStatus);
    
    if ($insertBooking->execute()) {
        header("Location: ../bookings.php?success=Booking submitted successfully! Waiting for confirmation.");
    } else {
        header("Location: ../rooms.php?error=Failed to submit booking. Please try again.");
    }
    $insertBooking->close();
    exit();
} else {
    header("Location: ../rooms.php");
    exit();
}
?>
