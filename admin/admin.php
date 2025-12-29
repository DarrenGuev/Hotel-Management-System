<?php
session_start();
include 'connect.php';

// Check if user is admin
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../frontend/login.php?error=Access denied");
    exit();
}

// Handle booking status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bookingAction'])) {
    $bookingID = (int)$_POST['bookingID'];
    $action = $_POST['bookingAction'];
    
    if ($action === 'confirm') {
        $updateBooking = $conn->prepare("UPDATE bookings SET bookingStatus = 'confirmed', updatedAt = NOW() WHERE bookingID = ?");
        $updateBooking->bind_param("i", $bookingID);
        if ($updateBooking->execute()) {
            header("Location: admin.php?success=Booking confirmed successfully!");
            exit();
        }
    } elseif ($action === 'cancel') {
        $updateBooking = $conn->prepare("UPDATE bookings SET bookingStatus = 'cancelled', paymentStatus = 'refunded', updatedAt = NOW() WHERE bookingID = ?");
        $updateBooking->bind_param("i", $bookingID);
        if ($updateBooking->execute()) {
            header("Location: admin.php?success=Booking cancelled successfully!");
            exit();
        }
    }
}

$getUsersQuery = "SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC";
$usersResult = executeQuery($getUsersQuery);
$customersData = [];
while ($user = mysqli_fetch_assoc($usersResult)) {
    $customersData[] = $user;
}

$getAllBookings = "SELECT bookings.*, rooms.roomName, roomtypes.roomType, users.firstName, users.lastName, users.email AS userEmail 
                    FROM bookings INNER JOIN rooms ON bookings.roomID = rooms.roomID INNER JOIN roomtypes ON rooms.roomTypeId = roomtypes.roomTypeID
                    INNER JOIN users ON bookings.userID = users.userID ORDER BY bookings.createdAt DESC";
$allBookingsResult = executeQuery($getAllBookings);

$allBookingsData = [];
while ($booking = mysqli_fetch_assoc($allBookingsResult)) {
    $allBookingsData[] = $booking;
}

$getConfirmedBookings = "SELECT bookings.*, rooms.roomName, roomtypes.roomType, users.firstName, users.lastName, users.email AS userEmail 
                        FROM bookings INNER JOIN rooms ON bookings.roomID = rooms.roomID INNER JOIN roomtypes ON rooms.roomTypeId = roomtypes.roomTypeID
                        INNER JOIN users ON bookings.userID = users.userID WHERE bookings.bookingStatus IN ('confirmed', 'completed')ORDER BY bookings.createdAt DESC";
$confirmedBookingsResult = executeQuery($getConfirmedBookings);

$confirmedBookingsData = [];
while ($booking = mysqli_fetch_assoc($confirmedBookingsResult)) {
    $confirmedBookingsData[] = $booking;
}

$getPendingBookings = "SELECT bookings.*, rooms.roomName, roomtypes.roomType, users.firstName, users.lastName, users.email AS userEmail 
                        FROM bookings INNER JOIN rooms ON bookings.roomID = rooms.roomID INNER JOIN roomtypes ON rooms.roomTypeId = roomtypes.roomTypeID
                        INNER JOIN users ON bookings.userID = users.userID WHERE bookings.bookingStatus = 'pending' ORDER BY bookings.createdAt DESC";
$pendingBookingsResult = executeQuery($getPendingBookings);

$pendingBookingsData = [];
while ($booking = mysqli_fetch_assoc($pendingBookingsResult)) {
    $pendingBookingsData[] = $booking;
}

$countAllBookings = count($allBookingsData);
$countCustomers = count($customersData);
$countConfirmed = count($confirmedBookingsData);
$countPending = count($pendingBookingsData);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TravelMates - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="HOTEL-MANAGEMENT-SYSTEM/css/style.css">
    <style>
        .card-dashboard {
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }
        .card-dashboard:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .card-dashboard.active {
            border: 3px solid #212529 !important;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>    

    <?php if (isset($_GET['success'])): ?>
        <div class="container mt-5 pt-4">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($_GET['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="container mt-5 pt-4">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($_GET['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Dashboard Cards -->
    <div class="container mt-5 pt-5">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold">Admin Dashboard</h2>
                <p class="text-muted">Welcome back, <?php echo htmlspecialchars($_SESSION['firstName']); ?>!</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-6 col-md-3">
                <div class="card card-dashboard text-bg-primary h-100" data-table="reservations" onclick="switchTable('reservations')">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-calendar-check display-4"></i>
                        <h3 class="fw-bold mt-2"><?php echo $countAllBookings; ?></h3>
                        <p class="mb-0">All Reservations</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card card-dashboard text-bg-warning h-100" data-table="customers" onclick="switchTable('customers')">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-people display-4"></i>
                        <h3 class="fw-bold mt-2"><?php echo $countCustomers; ?></h3>
                        <p class="mb-0">Customers</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card card-dashboard text-bg-success h-100" data-table="confirmed" onclick="switchTable('confirmed')">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-check-circle display-4"></i>
                        <h3 class="fw-bold mt-2"><?php echo $countConfirmed; ?></h3>
                        <p class="mb-0">Confirmed</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card card-dashboard text-bg-danger h-100" data-table="pending" onclick="switchTable('pending')">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-clock display-4"></i>
                        <h3 class="fw-bold mt-2"><?php echo $countPending; ?></h3>
                        <p class="mb-0">Pending</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Section -->
    <div class="container-fluid my-5" id="tableSection">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="adminTabs">
                            <li class="nav-item">
                                <button class="nav-link active" id="tab-reservations" data-table="reservations" onclick="switchTable('reservations')">
                                    <i class="bi bi-calendar-check me-1"></i>All Reservations
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" id="tab-customers" data-table="customers" onclick="switchTable('customers')">
                                    <i class="bi bi-people me-1"></i>Customers
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" id="tab-confirmed" data-table="confirmed" onclick="switchTable('confirmed')">
                                    <i class="bi bi-check-circle me-1"></i>Confirmed
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" id="tab-pending" data-table="pending" onclick="switchTable('pending')">
                                    <i class="bi bi-clock me-1"></i>Pending
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="dataTable">
                                <thead class="table-dark">
                                    <tr id="tableHeaders"></tr>
                                </thead>
                                <tbody id="tableBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Booking Modal -->
    <div class="modal fade" id="viewBookingModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-eye me-2"></i>Booking Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="bookingDetailsContent"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Role Modal -->
    <div class="modal fade" id="editRoleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="php/update_role.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="userID" id="editUserID">
                        <p>User: <strong id="editUserName"></strong></p>
                        <div class="mb-3">
                            <label for="newRole" class="form-label">Select Role</label>
                            <select class="form-select" name="role" id="newRole" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete User Modal -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="php/delete_user.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="userID" id="deleteUserID">
                        <p>Are you sure you want to delete <strong id="deleteUserName"></strong>?</p>
                        <p class="text-danger"><i class="bi bi-exclamation-triangle me-1"></i>This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <script>
        const customersData = <?php echo json_encode($customersData); ?>;
        const allBookingsData = <?php echo json_encode($allBookingsData); ?>;
        const confirmedBookingsData = <?php echo json_encode($confirmedBookingsData); ?>;
        const pendingBookingsData = <?php echo json_encode($pendingBookingsData); ?>;

        function getStatusBadge(status) {
            const badges = {
                'pending': '<span class="badge bg-warning text-dark">Pending</span>',
                'confirmed': '<span class="badge bg-success">Confirmed</span>',
                'cancelled': '<span class="badge bg-danger">Cancelled</span>',
                'completed': '<span class="badge bg-success">Confirmed</span>'
            };
            return badges[status] || '<span class="badge bg-secondary">' + status + '</span>';
        }

        function getPaymentBadge(status) {
            const badges = {
                'pending': '<span class="badge bg-warning text-dark">Pending</span>',
                'paid': '<span class="badge bg-success">Paid</span>',
                'refunded': '<span class="badge bg-secondary">Refunded</span>'
            };
            return badges[status] || '<span class="badge bg-secondary">' + status + '</span>';
        }

        function getBookingActions(booking) {
            let actions = `<button class="btn btn-sm btn-outline-primary me-1" onclick="viewBooking(${booking.bookingID})">
                <i class="bi bi-eye"></i>
            </button>`;
            
            if (booking.bookingStatus === 'pending') {
                actions += `
                <form method="POST" class="d-inline">
                    <input type="hidden" name="bookingID" value="${booking.bookingID}">
                    <input type="hidden" name="bookingAction" value="confirm">
                    <button type="submit" class="btn btn-sm btn-outline-success me-1" title="Approve">
                        <i class="bi bi-check-lg"></i>
                    </button>
                </form>
                <form method="POST" class="d-inline">
                    <input type="hidden" name="bookingID" value="${booking.bookingID}">
                    <input type="hidden" name="bookingAction" value="cancel">
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Reject">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </form>`;
            }
            return actions;
        }

        // Store all bookings for modals
        const allBookingsMap = {};
        allBookingsData.forEach(b => allBookingsMap[b.bookingID] = b);

        function viewBooking(bookingID) {
            const booking = allBookingsMap[bookingID];
            if (!booking) return;
            
            const checkIn = new Date(booking.checkInDate).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
            const checkOut = new Date(booking.checkOutDate).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
            
            document.getElementById('bookingDetailsContent').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">Guest Information</h6>
                        <p><strong>Name:</strong> ${booking.fullName}</p>
                        <p><strong>Email:</strong> ${booking.email}</p>
                        <p><strong>Phone:</strong> ${booking.phoneNumber}</p>
                        <p><strong>Guests:</strong> ${booking.numberOfGuests}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">Booking Information</h6>
                        <p><strong>Room:</strong> ${booking.roomName} (${booking.roomType})</p>
                        <p><strong>Check-in:</strong> ${checkIn}</p>
                        <p><strong>Check-out:</strong> ${checkOut}</p>
                        <p><strong>Total:</strong> ₱${parseFloat(booking.totalPrice).toLocaleString('en-PH', {minimumFractionDigits: 2})}</p>
                        <p><strong>Payment:</strong> ${booking.paymentMethod ? booking.paymentMethod.replace('_', ' ') + ' ' + getPaymentBadge(booking.paymentStatus) : getPaymentBadge(booking.paymentStatus)}</p>
                        <p><strong>Status:</strong> ${getStatusBadge(booking.bookingStatus)}</p>
                    </div>
                </div>
            `;
            new bootstrap.Modal(document.getElementById('viewBookingModal')).show();
        }

        function openEditRoleModal(userID, userName, currentRole) {
            document.getElementById('editUserID').value = userID;
            document.getElementById('editUserName').textContent = userName;
            document.getElementById('newRole').value = currentRole;
            new bootstrap.Modal(document.getElementById('editRoleModal')).show();
        }

        function openDeleteModal(userID, userName) {
            document.getElementById('deleteUserID').value = userID;
            document.getElementById('deleteUserName').textContent = userName;
            new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
        }

        // Table configurations
        const tableConfigs = {
            reservations: {
                headers: ['#', 'Guest', 'Room', 'Check-In', 'Check-Out', 'Total', 'Status', 'Payment', 'Actions'],
                getData: () => allBookingsData,
                renderRow: (booking, index) => `
                    <tr>
                        <td>${index + 1}</td>
                        <td><strong>${booking.firstName} ${booking.lastName}</strong><br><small class="text-muted">${booking.userEmail}</small></td>
                        <td>${booking.roomName}<br><small class="text-muted">${booking.roomType}</small></td>
                        <td>${booking.checkInDate}</td>
                        <td>${booking.checkOutDate}</td>
                        <td>₱${parseFloat(booking.totalPrice).toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                        <td>${getStatusBadge(booking.bookingStatus)}</td>
                        <td>${booking.paymentMethod ? booking.paymentMethod.replace('_', ' ') + ' ' + getPaymentBadge(booking.paymentStatus) : getPaymentBadge(booking.paymentStatus)}</td>
                        <td>${getBookingActions(booking)}</td>
                    </tr>
                `
            },
            customers: {
                headers: ['#', 'Name', 'Email', 'Username', 'Phone', 'Member Since', 'Role', 'Actions'],
                getData: () => customersData,
                renderRow: (user, index) => `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${user.firstName} ${user.lastName}</td>
                        <td>${user.email}</td>
                        <td>${user.username}</td>
                        <td>${user.phoneNumber || 'N/A'}</td>
                        <td>${user.created_at}</td>
                        <td><span class="badge bg-${user.role === 'admin' ? 'danger' : 'primary'}">${user.role}</span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-warning me-1" onclick="openEditRoleModal(${user.userID}, '${user.firstName} ${user.lastName}', '${user.role}')">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="openDeleteModal(${user.userID}, '${user.firstName} ${user.lastName}')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `
            },
            confirmed: {
                headers: ['#', 'Guest', 'Room', 'Check-In', 'Check-Out', 'Total', 'Status', 'Payment', 'Actions'],
                getData: () => confirmedBookingsData,
                renderRow: (booking, index) => tableConfigs.reservations.renderRow(booking, index)
            },
            pending: {
                headers: ['#', 'Guest', 'Room', 'Check-In', 'Check-Out', 'Total', 'Status', 'Payment', 'Actions'],
                getData: () => pendingBookingsData,
                renderRow: (booking, index) => tableConfigs.reservations.renderRow(booking, index)
            }
        };

        function switchTable(tableType) {
            const config = tableConfigs[tableType];
            if (!config) return;
            document.getElementById('tableHeaders').innerHTML = config.headers.map(h => `<th>${h}</th>`).join('');
            const data = config.getData();
            if (data.length > 0) {
                document.getElementById('tableBody').innerHTML = data.map((item, index) => config.renderRow(item, index)).join('');
            } else {
                document.getElementById('tableBody').innerHTML = `
                    <tr>
                        <td colspan="${config.headers.length}" class="text-center py-4">
                            <i class="bi bi-inbox display-4 text-muted"></i>
                            <p class="text-muted mt-2">No data found</p>
                        </td>
                    </tr>
                `;
            }

            document.querySelectorAll('.card-dashboard').forEach(card => card.classList.remove('active'));
            document.querySelector(`.card-dashboard[data-table="${tableType}"]`)?.classList.add('active');

            document.querySelectorAll('#adminTabs .nav-link').forEach(tab => tab.classList.remove('active'));
            document.getElementById('tab-' + tableType)?.classList.add('active');
        }
        document.addEventListener('DOMContentLoaded', () => switchTable('reservations'));
    </script>
</body>
</html>