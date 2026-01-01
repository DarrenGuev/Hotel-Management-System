<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../frontend/login.php?error=Access denied");
    exit();
}

// Include SMS Service
require_once '../integrations/sms/SmsService.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $bookingID = (int)$_POST['bookingID'];
    $action = $_POST['action'];
    
    // Get booking details for SMS
    $getBookingDetails = $conn->prepare("SELECT b.phoneNumber, b.checkInDate, u.firstName, u.lastName 
                                          FROM bookings b 
                                          INNER JOIN users u ON b.userID = u.userID 
                                          WHERE b.bookingID = ?");
    $getBookingDetails->bind_param("i", $bookingID);
    $getBookingDetails->execute();
    $bookingDetails = $getBookingDetails->get_result()->fetch_assoc();
    $phoneNumber = $bookingDetails['phoneNumber'] ?? '';
    $checkInDate = $bookingDetails['checkInDate'] ?? '';
    $customerName = trim(($bookingDetails['firstName'] ?? '') . ' ' . ($bookingDetails['lastName'] ?? ''));
    
    if ($action === 'confirm') {
        $updateBooking = $conn->prepare("UPDATE bookings SET bookingStatus = 'confirmed', updatedAt = NOW() WHERE bookingID = ?");
        $updateBooking->bind_param("i", $bookingID);
        $updateBooking->execute();
        
        // Send SMS notification
        if (!empty($phoneNumber)) {
            try {
                $smsService = new SmsService();
                $smsService->sendBookingApprovalSms($bookingID, $phoneNumber, $customerName, $checkInDate);
            } catch (Exception $e) {
                error_log('SMS Error: ' . $e->getMessage());
            }
        }
        
        $message = "Booking confirmed successfully!";
        $messageType = "success";
    } elseif ($action === 'cancel') {
        $updateBooking = $conn->prepare("UPDATE bookings SET bookingStatus = 'cancelled', paymentStatus = 'refunded', updatedAt = NOW() WHERE bookingID = ?");
        $updateBooking->bind_param("i", $bookingID);
        $updateBooking->execute();
        
        // Send SMS notification
        if (!empty($phoneNumber)) {
            try {
                $smsService = new SmsService();
                $smsService->sendBookingCancelledSms($bookingID, $phoneNumber, $customerName);
            } catch (Exception $e) {
                error_log('SMS Error: ' . $e->getMessage());
            }
        }
        
        $message = "Booking cancelled successfully!";
        $messageType = "warning";
    } elseif ($action === 'complete') {
        $updateBooking = $conn->prepare("UPDATE bookings SET bookingStatus = 'completed', updatedAt = NOW() WHERE bookingID = ?");
        $updateBooking->bind_param("i", $bookingID);
        $updateBooking->execute();
        
        // Send SMS notification
        if (!empty($phoneNumber)) {
            try {
                $smsService = new SmsService();
                $smsService->sendBookingCompletedSms($bookingID, $phoneNumber, $customerName);
            } catch (Exception $e) {
                error_log('SMS Error: ' . $e->getMessage());
            }
        }
        
        $message = "Booking marked as completed!";
        $messageType = "info";
    }
}

$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';
$whereClause = "";
if ($statusFilter !== 'all') {
    $whereClause = "WHERE bookings.bookingStatus = '" . $conn->real_escape_string($statusFilter) . "'";
}

$getBookings = "SELECT bookings.*, users.firstName, users.lastName, users.email as userEmail, 
                rooms.roomName, rooms.imagePath, roomtypes.roomType 
                FROM bookings 
                INNER JOIN users ON bookings.userID = users.userID 
                INNER JOIN rooms ON bookings.roomID = rooms.roomID 
                INNER JOIN roomtypes ON rooms.roomTypeId = roomtypes.roomTypeID 
                $whereClause
                ORDER BY bookings.createdAt DESC";
$bookingsResult = executeQuery($getBookings);

$countAll = executeQuery("SELECT COUNT(*) as count FROM bookings")->fetch_assoc()['count'];
$countPending = executeQuery("SELECT COUNT(*) as count FROM bookings WHERE bookingStatus = 'pending'")->fetch_assoc()['count'];
$countConfirmed = executeQuery("SELECT COUNT(*) as count FROM bookings WHERE bookingStatus = 'confirmed'")->fetch_assoc()['count'];
$countCancelled = executeQuery("SELECT COUNT(*) as count FROM bookings WHERE bookingStatus = 'cancelled'")->fetch_assoc()['count'];
$countCompleted = executeQuery("SELECT COUNT(*) as count FROM bookings WHERE bookingStatus = 'completed'")->fetch_assoc()['count'];
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TravelMates Admin - Manage Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 12px 20px;
            margin: 4px 12px;
            border-radius: 8px;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,0.1);
        }
        .stat-card {
            border-radius: 12px;
            border: none;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>

<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-12 col-lg-2 px-0 sidebar">
                <div class="p-4 text-center">
                    <h4 class="text-white fw-bold">TravelMates</h4>
                    <small class="text-white-50">Admin Panel</small>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link" href="admin.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                    <a class="nav-link" href="rooms.php"><i class="bi bi-door-open me-2"></i>Rooms</a>
                    <a class="nav-link active" href="manage_bookings.php"><i class="bi bi-calendar-check me-2"></i>Bookings</a>
                    <a class="nav-link" href="users.php"><i class="bi bi-people me-2"></i>Users</a>
                    <hr class="text-white-50 mx-3">
                    <a class="nav-link text-danger" href="../frontend/php/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-12 col-lg-10 p-4">
                <div class="row mb-4">
                    <div class="col-12">
                        <h2 class="fw-bold">Manage Bookings</h2>
                        <p class="text-muted">View and manage all customer bookings</p>
                    </div>
                </div>

                <!-- Alert Messages -->
                <?php if (isset($message)): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                            <?php echo $message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Stats Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-6 col-md-3 col-xl">
                        <div class="card stat-card bg-primary text-white">
                            <div class="card-body text-center">
                                <i class="bi bi-calendar3 display-6"></i>
                                <h3 class="fw-bold mt-2"><?php echo $countAll; ?></h3>
                                <small>Total Bookings</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-xl">
                        <div class="card stat-card bg-warning text-dark">
                            <div class="card-body text-center">
                                <i class="bi bi-hourglass-split display-6"></i>
                                <h3 class="fw-bold mt-2"><?php echo $countPending; ?></h3>
                                <small>Pending</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-xl">
                        <div class="card stat-card bg-success text-white">
                            <div class="card-body text-center">
                                <i class="bi bi-check-circle display-6"></i>
                                <h3 class="fw-bold mt-2"><?php echo $countConfirmed; ?></h3>
                                <small>Confirmed</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-xl">
                        <div class="card stat-card bg-danger text-white">
                            <div class="card-body text-center">
                                <i class="bi bi-x-circle display-6"></i>
                                <h3 class="fw-bold mt-2"><?php echo $countCancelled; ?></h3>
                                <small>Cancelled</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-xl">
                        <div class="card stat-card bg-info text-white">
                            <div class="card-body text-center">
                                <i class="bi bi-flag display-6"></i>
                                <h3 class="fw-bold mt-2"><?php echo $countCompleted; ?></h3>
                                <small>Completed</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Tabs -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-body p-2">
                                <ul class="nav nav-pills">
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo $statusFilter === 'all' ? 'active' : ''; ?>" href="?status=all">All</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo $statusFilter === 'pending' ? 'active' : ''; ?>" href="?status=pending">Pending</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo $statusFilter === 'confirmed' ? 'active' : ''; ?>" href="?status=confirmed">Confirmed</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo $statusFilter === 'cancelled' ? 'active' : ''; ?>" href="?status=cancelled">Cancelled</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo $statusFilter === 'completed' ? 'active' : ''; ?>" href="?status=completed">Completed</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bookings Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <?php if (mysqli_num_rows($bookingsResult) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>ID</th>
                                                <th>Guest</th>
                                                <th>Room</th>
                                                <th>Dates</th>
                                                <th>Total</th>
                                                <th>Payment</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($booking = mysqli_fetch_assoc($bookingsResult)): 
                                                $statusBadgeClass = match($booking['bookingStatus']) {
                                                    'confirmed' => 'bg-success',
                                                    'pending' => 'bg-warning text-dark',
                                                    'cancelled' => 'bg-danger',
                                                    'completed' => 'bg-info',
                                                    default => 'bg-secondary'
                                                };
                                                $paymentBadgeClass = match($booking['paymentStatus']) {
                                                    'paid' => 'bg-success',
                                                    'pending' => 'bg-warning text-dark',
                                                    'refunded' => 'bg-secondary',
                                                    default => 'bg-secondary'
                                                };
                                            ?>
                                            <tr>
                                                <td><strong>#<?php echo $booking['bookingID']; ?></strong></td>
                                                <td>
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($booking['firstName'] . ' ' . $booking['lastName']); ?></strong>
                                                        <br><small class="text-muted"><?php echo htmlspecialchars($booking['email']); ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($booking['roomName']); ?></strong>
                                                        <br><small class="text-muted"><?php echo htmlspecialchars($booking['roomType']); ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <small>
                                                        <?php echo date('M d', strtotime($booking['checkInDate'])); ?> - 
                                                        <?php echo date('M d, Y', strtotime($booking['checkOutDate'])); ?>
                                                    </small>
                                                </td>
                                                <td><strong>₱<?php echo number_format($booking['totalPrice'], 2); ?></strong></td>
                                                <td>
                                                    <span class="badge <?php echo $paymentBadgeClass; ?>">
                                                        <?php echo ucfirst($booking['paymentStatus']); ?>
                                                    </span>
                                                    <br><small class="text-muted">
                                                        <?php
                                                            $pm = $booking['paymentMethod'] ?? '';
                                                            $ps = strtolower($booking['paymentStatus'] ?? '');
                                                            if ($pm !== '') {
                                                                if (strtolower($pm) === 'paypal') {
                                                                    echo '<i class="bi bi-paypal me-1"></i>PayPal';
                                                                } else {
                                                                    echo ucfirst(str_replace('_', ' ', $pm));
                                                                }
                                                            } elseif ($ps === 'paid') {
                                                                echo '<i class="bi bi-paypal me-1"></i>PayPal';
                                                            } else {
                                                                echo '-';
                                                            }
                                                        ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="badge <?php echo $statusBadgeClass; ?>">
                                                        <?php echo ucfirst($booking['bookingStatus']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $booking['bookingID']; ?>">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                        <?php if ($booking['bookingStatus'] === 'pending'): ?>
                                                            <form method="POST" class="d-inline">
                                                                <input type="hidden" name="bookingID" value="<?php echo $booking['bookingID']; ?>">
                                                                <input type="hidden" name="action" value="confirm">
                                                                <button type="submit" class="btn btn-outline-success" title="Approve">
                                                                    <i class="bi bi-check-lg"></i>
                                                                </button>
                                                            </form>
                                                            <form method="POST" class="d-inline">
                                                                <input type="hidden" name="bookingID" value="<?php echo $booking['bookingID']; ?>">
                                                                <input type="hidden" name="action" value="cancel">
                                                                <button type="submit" class="btn btn-outline-danger" title="Reject">
                                                                    <i class="bi bi-x-lg"></i>
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>
                                                        <?php if ($booking['bookingStatus'] === 'confirmed'): ?>
                                                            <form method="POST" class="d-inline">
                                                                <input type="hidden" name="bookingID" value="<?php echo $booking['bookingID']; ?>">
                                                                <input type="hidden" name="action" value="complete">
                                                                <button type="submit" class="btn btn-outline-info" title="Mark as Completed">
                                                                    <i class="bi bi-flag"></i>
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- View Modal -->
                                            <div class="modal fade" id="viewModal<?php echo $booking['bookingID']; ?>" tabindex="-1">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Booking Details #<?php echo $booking['bookingID']; ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <h6 class="fw-bold mb-3">Guest Information</h6>
                                                                    <p><strong>Name:</strong> <?php echo htmlspecialchars($booking['fullName']); ?></p>
                                                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($booking['email']); ?></p>
                                                                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($booking['phoneNumber']); ?></p>
                                                                    <p><strong>Guests:</strong> <?php echo $booking['numberOfGuests']; ?></p>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <h6 class="fw-bold mb-3">Booking Information</h6>
                                                                    <p><strong>Room:</strong> <?php echo htmlspecialchars($booking['roomName']); ?> (<?php echo htmlspecialchars($booking['roomType']); ?>)</p>
                                                                    <p><strong>Check-in:</strong> <?php echo date('F d, Y', strtotime($booking['checkInDate'])); ?></p>
                                                                    <p><strong>Check-out:</strong> <?php echo date('F d, Y', strtotime($booking['checkOutDate'])); ?></p>
                                                                    <p><strong>Total Price:</strong> ₱<?php echo number_format($booking['totalPrice'], 2); ?></p>
                                                                        <p><strong>Payment:</strong> <?php
                                                                            $pm = $booking['paymentMethod'] ?? '';
                                                                            $ps = strtolower($booking['paymentStatus'] ?? '');
                                                                            if ($pm !== '') {
                                                                                if (strtolower($pm) === 'paypal') {
                                                                                    echo 'PayPal';
                                                                                } else {
                                                                                    echo ucfirst(str_replace('_', ' ', $pm));
                                                                                }
                                                                            } elseif ($ps === 'paid') {
                                                                                echo 'PayPal';
                                                                            } else {
                                                                                echo '-';
                                                                            }
                                                                            echo ' (' . ucfirst($booking['paymentStatus']) . ')';
                                                                        ?></p>
                                                                    <p><strong>Status:</strong> <span class="badge <?php echo $statusBadgeClass; ?>"><?php echo ucfirst($booking['bookingStatus']); ?></span></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-inbox display-1 text-muted"></i>
                                    <h5 class="mt-3">No bookings found</h5>
                                    <p class="text-muted">There are no bookings matching your filter.</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>
</html>
