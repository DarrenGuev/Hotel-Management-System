<?php
require __DIR__ . '/config.php';
session_start();
require __DIR__ . '/../../dbconnect/connect.php';

$orderID = $_GET['token'] ?? null;
$roomID = $_GET['roomID'] ?? null;
$bookingID = $_GET['bookingID'] ?? null;

if (!$orderID) {
    header('Location: /HOTEL-MANAGEMENT-SYSTEM/frontend/rooms.php?paypal_error=1');
    exit;
}

$access = paypal_get_access_token();
if ($access['error']) {
    header('Location: /HOTEL-MANAGEMENT-SYSTEM/frontend/rooms.php?paypal_error=token');
    exit;
}
$token = $access['access_token'];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, PAYPAL_API_BASE . '/v2/checkout/orders/' . urlencode($orderID) . '/capture');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token
]);
$response = curl_exec($ch);
$err = curl_error($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($err) {
    header('Location: /HOTEL-MANAGEMENT-SYSTEM/frontend/rooms.php?paypal_error=curl');
    exit;
}

$data = json_decode($response, true);
if ($status >= 200 && $status < 300) {

    if ($bookingID) {
        $update = $conn->prepare("UPDATE bookings SET paymentStatus = 'paid', paymentMethod = 'paypal', updatedAt = NOW() WHERE bookingID = ? AND bookingStatus = 'pending'");
        $update->bind_param("i", $bookingID);
        $update->execute();
    } else {

        $userID = isset($_SESSION['userID']) ? (int)$_SESSION['userID'] : null;
        if ($userID && $roomID) {
            $find = $conn->prepare("SELECT bookingID FROM bookings WHERE userID = ? AND roomID = ? AND bookingStatus = 'pending' ORDER BY createdAt DESC LIMIT 1");
            $find->bind_param("ii", $userID, $roomID);
            $find->execute();
            $res = $find->get_result();
            if ($row = $res->fetch_assoc()) {
                $foundId = (int)$row['bookingID'];
                $update = $conn->prepare("UPDATE bookings SET paymentStatus = 'paid', paymentMethod = 'paypal', updatedAt = NOW() WHERE bookingID = ?");
                $update->bind_param("i", $foundId);
                $update->execute();
            }
        }
    }

    // Keep bookingStatus as 'pending' for admin to confirm; redirect to bookings with success message
    $redirect = '/HOTEL-MANAGEMENT-SYSTEM/frontend/bookings.php?paypal_success=1&order=' . urlencode($orderID);
    header('Location: ' . $redirect);
    exit;
}

// Failure
header('Location: /HOTEL-MANAGEMENT-SYSTEM/frontend/rooms.php?paypal_error=1');
exit;
