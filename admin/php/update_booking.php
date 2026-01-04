<?php
session_start();
include '../connect.php';

// Include class autoloader
require_once __DIR__ . '/../../classes/autoload.php';

// Require admin access
Auth::requireAdmin('../../frontend/login.php');

// Initialize models
$bookingModel = new Booking();
$userModel = new User();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingID = (int)$_POST['bookingID'];
    $bookingStatus = $_POST['bookingStatus'];
    $paymentStatus = $_POST['paymentStatus'];
    $notes = $_POST['notes'] ?? '';

    $validBookingStatuses = [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED, Booking::STATUS_CANCELLED, Booking::STATUS_COMPLETED];
    $validPaymentStatuses = [Booking::PAYMENT_PENDING, Booking::PAYMENT_PAID, Booking::PAYMENT_REFUNDED];

    if (!in_array($bookingStatus, $validBookingStatuses) || !in_array($paymentStatus, $validPaymentStatuses)) {
        header("Location: ../admin.php?error=Invalid status");
        exit();
    }

    // Get the current booking before update
    $currentBooking = $bookingModel->find($bookingID);
    $oldStatus = $currentBooking['bookingStatus'] ?? '';
    $phoneNumber = $currentBooking['phoneNumber'] ?? '';
    $checkInDate = $currentBooking['checkInDate'] ?? '';

    // Get customer name
    $customer = $currentBooking ? $userModel->find($currentBooking['userID']) : null;
    $customerName = $customer ? ($customer['firstName'] ?? '') . ' ' . ($customer['lastName'] ?? '') : '';

    // Update booking status and payment status
    $updateData = [
        'bookingStatus' => $bookingStatus,
        'paymentStatus' => $paymentStatus,
        'updatedAt' => date('Y-m-d H:i:s')
    ];

    if ($bookingModel->update($bookingID, $updateData)) {
        // Send SMS notification if status changed
        if ($oldStatus !== $bookingStatus && !empty($phoneNumber)) {
            try {
                require_once '../../integrations/sms/SmsService.php';
                require_once '../../integrations/gmail/EmailService.php';
                $smsService = new SmsService();
                
                if ($bookingStatus === Booking::STATUS_CONFIRMED) {
                    $smsService->sendBookingApprovalSms($bookingID, $phoneNumber, trim($customerName), $checkInDate);
                    
                    // Send email receipt automatically
                    try {
                        $emailService = new EmailService();
                        $bookingData = $bookingModel->getByIdWithDetails($bookingID);
                        
                        if ($bookingData && !empty($bookingData['email'])) {
                            $bookingData['customerName'] = trim($bookingData['firstName'] . ' ' . $bookingData['lastName']);
                            $emailResult = $emailService->sendBookingReceipt($bookingData);
                            if (!$emailResult['success']) {
                                error_log('Email Receipt Error for Booking #' . $bookingID . ': ' . ($emailResult['error'] ?? 'Unknown error'));
                            }
                        }
                    } catch (Exception $emailEx) {
                        error_log('Email Service Error: ' . $emailEx->getMessage());
                    }
                } elseif ($bookingStatus === Booking::STATUS_CANCELLED) {
                    $smsService->sendBookingCancelledSms($bookingID, $phoneNumber, trim($customerName));
                } elseif ($bookingStatus === Booking::STATUS_COMPLETED) {
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
