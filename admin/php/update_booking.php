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

    // Get the current booking status before update
    $getCurrentStatus = $conn->prepare("SELECT bookingStatus, phoneNumber, checkInDate FROM bookings WHERE bookingID = ?");
    $getCurrentStatus->bind_param("i", $bookingID);
    $getCurrentStatus->execute();
    $currentResult = $getCurrentStatus->get_result();
    $currentBooking = $currentResult->fetch_assoc();
    $oldStatus = $currentBooking['bookingStatus'] ?? '';
    $phoneNumber = $currentBooking['phoneNumber'] ?? '';
    $checkInDate = $currentBooking['checkInDate'] ?? '';

    // Get customer name
    $getCustomer = $conn->prepare("SELECT u.firstName, u.lastName FROM bookings b INNER JOIN users u ON b.userID = u.userID WHERE b.bookingID = ?");
    $getCustomer->bind_param("i", $bookingID);
    $getCustomer->execute();
    $customerResult = $getCustomer->get_result();
    $customer = $customerResult->fetch_assoc();
    $customerName = ($customer['firstName'] ?? '') . ' ' . ($customer['lastName'] ?? '');

    $updateQuery = $conn->prepare("UPDATE bookings SET bookingStatus = ?, paymentStatus = ?, updatedAt = NOW() WHERE bookingID = ?");
    $updateQuery->bind_param("ssi", $bookingStatus, $paymentStatus, $bookingID);

    if ($updateQuery->execute()) {
        // Send SMS notification if status changed
        if ($oldStatus !== $bookingStatus && !empty($phoneNumber)) {
            try {
                require_once '../../integrations/sms/SmsService.php';
                $smsService = new SmsService();
                
                if ($bookingStatus === 'confirmed') {
                    $smsService->sendBookingApprovalSms($bookingID, $phoneNumber, trim($customerName), $checkInDate);
                } elseif ($bookingStatus === 'cancelled') {
                    $smsService->sendBookingCancelledSms($bookingID, $phoneNumber, trim($customerName));
                } elseif ($bookingStatus === 'completed') {
                    $smsService->sendBookingCompletedSms($bookingID, $phoneNumber, trim($customerName));
                }
            } catch (Exception $e) {
                // Log error but don't fail the booking update
                error_log('SMS Error: ' . $e->getMessage());
            }
        }
        
        header("Location: ../admin.php?success=Booking updated successfully");
    } else {
        header("Location: ../admin.php?error=Failed to update booking");
    }
    exit();
}

header("Location: ../admin.php");
exit();
?>
