<?php
session_start();
include '../../dbconnect/connect.php';

// Detect AJAX flag (sent by frontend as form field 'ajax')
$isAjax = false;
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax']) && $_POST['ajax'] == '1') {
    $isAjax = true;
}

function ajaxResponse($data, $status = 200)
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

function handleRedirectOrJson($message, $status = 400, $redirectTo = '../rooms.php')
{
    global $isAjax;
    if ($isAjax) {
        ajaxResponse(['success' => false, 'message' => $message], $status);
    } else {
        header("Location: {$redirectTo}?error=" . urlencode($message));
        exit();
    }
}

if (!isset($_SESSION['userID'])) {
    if ($isAjax) {
        ajaxResponse(['success' => false, 'message' => 'Please login to book a room'], 401);
    }
    header("Location: ../login.php?error=Please login to book a room");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userID = (int)$_SESSION['userID'];
    $roomID = (int)$_POST['roomID'];

    // Prefer posted first/last name when available; otherwise fetch from users table
    $firstName = isset($_POST['firstName']) ? trim($_POST['firstName']) : '';
    $lastName = isset($_POST['lastName']) ? trim($_POST['lastName']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phoneNumber = isset($_POST['phoneNumber']) ? trim($_POST['phoneNumber']) : '';

    if ($firstName === '' || $lastName === '' || $email === '' || $phoneNumber === '') {
        $stmt = $conn->prepare("SELECT firstName, lastName, email, phoneNumber FROM users WHERE userID = ?");
        $stmt->bind_param('i', $userID);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $row = $res->fetch_assoc()) {
            if ($firstName === '') $firstName = $row['firstName'];
            if ($lastName === '') $lastName = $row['lastName'];
            if ($email === '') $email = $row['email'];
            if ($phoneNumber === '') $phoneNumber = $row['phoneNumber'];
        }
        $stmt->close();
    }

    $fullName = trim($firstName . ' ' . $lastName);
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
        handleRedirectOrJson('Check-in date cannot be in the past', 400);
    }
    
    if ($checkOut <= $checkIn) {
        handleRedirectOrJson('Check-out date must be after check-in date', 400);
    }
    
    // Only PayPal payments are accepted from the frontend
    $validPaymentMethods = ['paypal'];
    if (!in_array($paymentMethod, $validPaymentMethods)) {
        handleRedirectOrJson('Invalid payment method', 400);
    }

    // For PayPal flow we should mark payment as pending until capture completes.
    $paymentStatus = 'pending';
    $checkAvailability = $conn->prepare("SELECT quantity FROM rooms WHERE roomID = ?");
    $checkAvailability->bind_param("i", $roomID);
    $checkAvailability->execute();
    $roomResult = $checkAvailability->get_result();
    $room = $roomResult->fetch_assoc();
    if (!$room || $room['quantity'] < 1) {
        handleRedirectOrJson('Room is not available', 400);
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
        handleRedirectOrJson('Room is not available for the selected dates', 400);
    }
    
    $insertBooking = $conn->prepare("INSERT INTO bookings (userID, roomID, fullName, email, phoneNumber, checkInDate, checkOutDate, numberOfGuests, totalPrice, paymentMethod, paymentStatus, bookingStatus, createdAt, updatedAt) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW(), NOW())");
    $insertBooking->bind_param("iisssssisss", $userID, $roomID, $fullName, $email, $phoneNumber, $checkInDate, $checkOutDate, $numberOfGuests, $totalPrice, $paymentMethod, $paymentStatus);

    if ($insertBooking->execute()) {
        $newBookingId = $conn->insert_id;
        // If AJAX request, return JSON containing bookingID so frontend can continue to PayPal
        if (isset($_POST['ajax']) && $_POST['ajax'] == '1') {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'bookingID' => $newBookingId]);
            exit();
        }

        header("Location: ../bookings.php?success=Booking submitted successfully! Waiting for confirmation.");
    } else {
        if (isset($_POST['ajax']) && $_POST['ajax'] == '1') {
            header('Content-Type: application/json', true, 500);
            echo json_encode(['success' => false, 'message' => 'Failed to create booking']);
            exit();
        }

        handleRedirectOrJson('Failed to submit booking. Please try again.', 500);
    }
    $insertBooking->close();
    exit();
} else {
    header("Location: ../rooms.php");
    exit();
}
?>
