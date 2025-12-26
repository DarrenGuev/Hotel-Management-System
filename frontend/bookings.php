<?php
session_start();
include '../dbconnect/connect.php';

// Redirect to login if not logged in
if (!isset($_SESSION['userID'])) {
    header("Location: login.php?error=Please login to view your bookings");
    exit();
}

$userID = $_SESSION['userID'];

// Fetch user's bookings with room details and features
$getBookings = "SELECT bookings.*, rooms.roomName, rooms.imagePath, rooms.capacity, rooms.base_price, roomtypes.roomType 
                FROM bookings 
                INNER JOIN rooms ON bookings.roomID = rooms.roomID 
                INNER JOIN roomtypes ON rooms.roomTypeId = roomtypes.roomTypeID 
                WHERE bookings.userID = '$userID' 
                ORDER BY bookings.createdAt DESC";
$bookingsResult = executeQuery($getBookings);

// Function to get features for a room
function getBookingRoomFeatures($roomID) {
    $query = "SELECT features.featureName FROM features 
              INNER JOIN roomfeatures ON features.featureId = roomfeatures.featureID 
              WHERE roomfeatures.roomID = " . (int)$roomID;
    return executeQuery($query);
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TravelMates - My Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/HOTEL-MANAGEMENT-SYSTEM/css/style.css">
    <style>
        .booking-card {
            background: #2d2d2d;
            border-radius: 12px;
            overflow: hidden;
        }
        
        .booking-card-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        @media (min-width: 768px) {
            .booking-card-img {
                height: 100%;
                min-height: 250px;
            }
        }
        
        .feature-badge {
            background: #4a4a4a;
            color: #fff;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            display: inline-block;
            margin: 2px;
        }
        
        .room-description {
            color: #b0b0b0;
            font-size: 0.85rem;
            line-height: 1.5;
        }
        
        .status-section {
            background: #1a1a1a;
            padding: 15px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .btn-booking-status {
            background: #6c757d;
            color: #fff;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            font-size: 0.85rem;
        }
        
        .btn-cancel {
            background: #dc3545;
            color: #fff;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            font-size: 0.85rem;
        }
    </style>
</head>

<body class="bg-dark text-white">
    <?php include 'includes/navbar.php'; ?>

    <div class="container py-5">
        <h1 class="mb-4"><i class="bi bi-calendar-check me-2"></i>My Bookings</h1>
        <p class="text-muted mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['firstName']); ?>! Here are your bookings.</p>

        <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_GET['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <?php if (mysqli_num_rows($bookingsResult) > 0): ?>
            <?php while ($booking = mysqli_fetch_assoc($bookingsResult)): 
                // Get features for this room
                $featuresResult = getBookingRoomFeatures($booking['roomID']);
                $features = [];
                while ($feature = mysqli_fetch_assoc($featuresResult)) {
                    $features[] = $feature['featureName'];
                }
                
                // Calculate nights
                $checkIn = new DateTime($booking['checkInDate']);
                $checkOut = new DateTime($booking['checkOutDate']);
                $nights = $checkIn->diff($checkOut)->days;
            ?>
            <div class="booking-card mb-4">
                <div class="row g-0">
                    <!-- Room Image -->
                    <div class="col-12 col-md-3">
                        <img src="/HOTEL-MANAGEMENT-SYSTEM/admin/assets/<?php echo htmlspecialchars($booking['imagePath']); ?>" 
                             alt="<?php echo htmlspecialchars($booking['roomName']); ?>" 
                             class="booking-card-img">
                    </div>
                    
                    <!-- Room Details -->
                    <div class="col-12 col-md-6 p-4">
                        <h4 class="fw-bold mb-3"><?php echo htmlspecialchars($booking['roomName']); ?></h4>
                        
                        <!-- Features -->
                        <div class="mb-3">
                            <?php foreach ($features as $featureName): ?>
                                <span class="feature-badge"><?php echo htmlspecialchars($featureName); ?></span>
                            <?php endforeach; ?>
                            <span class="feature-badge"><?php echo $booking['capacity']; ?> Guests Max</span>
                        </div>
                        
                        <!-- Room Description -->
                        <p class="room-description mb-3">
                            <strong>Check-in:</strong> <?php echo date('M d, Y', strtotime($booking['checkInDate'])); ?><br>
                            <strong>Check-out:</strong> <?php echo date('M d, Y', strtotime($booking['checkOutDate'])); ?><br>
                            <strong>Duration:</strong> <?php echo $nights; ?> night(s)<br>
                            <strong>Guests:</strong> <?php echo $booking['numberOfGuests']; ?><br>
                            <strong>Total Price:</strong> ₱<?php echo number_format($booking['totalPrice'], 2); ?><br>
                            <strong>Payment Method:</strong> <?php echo ucfirst(str_replace('_', ' ', $booking['paymentMethod'])); ?>
                        </p>
                    </div>
                    
                    <!-- Status Section -->
                    <div class="col-12 col-md-3 status-section">
                        <div class="text-center mb-3">
                            <!-- Payment Status -->
                            <p class="text-muted small mb-1">Payment Status</p>
                            <?php 
                            $paymentClass = match($booking['paymentStatus']) {
                                'paid' => 'bg-success',
                                'pending' => 'bg-warning text-dark',
                                'refunded' => 'bg-secondary',
                                default => 'bg-secondary'
                            };
                            ?>
                            <span class="badge <?php echo $paymentClass; ?> mb-3">
                                <?php echo ucfirst($booking['paymentStatus']); ?>
                            </span>
                            
                            <!-- Booking Status -->
                            <p class="text-muted small mb-1">Booking Status</p>
                            <?php 
                            $statusClass = match($booking['bookingStatus']) {
                                'confirmed' => 'bg-success',
                                'pending' => 'bg-warning text-dark',
                                'cancelled' => 'bg-danger',
                                'completed' => 'bg-info',
                                default => 'bg-secondary'
                            };
                            ?>
                            <span class="badge <?php echo $statusClass; ?> fs-6 px-3 py-2">
                                <?php echo ucfirst($booking['bookingStatus']); ?>
                            </span>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button class="btn-booking-status" disabled>
                                <i class="bi bi-info-circle me-1"></i>Booking #<?php echo $booking['bookingID']; ?>
                            </button>
                            <?php if ($booking['bookingStatus'] === 'pending'): ?>
                            <button class="btn-cancel" onclick="cancelBooking(<?php echo $booking['bookingID']; ?>)">
                                <i class="bi bi-x-circle me-1"></i>Cancel
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>You have no bookings yet. 
                <a href="rooms.php" class="alert-link">Browse our rooms</a> and make your first booking!
            </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>

    <!-- Cancel Booking Modal -->
    <div class="modal fade" id="cancelBookingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Cancel Booking</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="php/cancel_booking.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="bookingID" id="cancelBookingID">
                        <p>Are you sure you want to cancel this booking?</p>
                        <p class="text-warning"><i class="bi bi-info-circle me-1"></i>This action may be subject to cancellation policies.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keep Booking</button>
                        <button type="submit" class="btn btn-danger">Yes, Cancel Booking</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <script>
        function cancelBooking(bookingID) {
            document.getElementById('cancelBookingID').value = bookingID;
            new bootstrap.Modal(document.getElementById('cancelBookingModal')).show();
        }
    </script>
</body>

</html>