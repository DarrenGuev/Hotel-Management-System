<?php
include 'connect.php';

// Fetch users from database for customers tab
$getUsersQuery = "SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC";
$usersResult = executeQuery($getUsersQuery);

$customersData = [];
while ($user = mysqli_fetch_assoc($usersResult)) {
    $customersData[] = $user;
}

// Fetch all bookings for reservations tab
$getAllBookings = "SELECT bookings.*, rooms.roomName, users.email AS userEmail 
                   FROM bookings 
                   INNER JOIN rooms ON bookings.roomID = rooms.roomID 
                   INNER JOIN users ON bookings.userID = users.userID 
                   ORDER BY bookings.createdAt DESC";
$allBookingsResult = executeQuery($getAllBookings);

$allBookingsData = [];
while ($booking = mysqli_fetch_assoc($allBookingsResult)) {
    $allBookingsData[] = $booking;
}

// Fetch confirmed bookings
$getConfirmedBookings = "SELECT bookings.*, rooms.roomName, users.email AS userEmail 
                         FROM bookings 
                         INNER JOIN rooms ON bookings.roomID = rooms.roomID 
                         INNER JOIN users ON bookings.userID = users.userID 
                         WHERE bookings.bookingStatus = 'confirmed' 
                         ORDER BY bookings.createdAt DESC";
$confirmedBookingsResult = executeQuery($getConfirmedBookings);

$confirmedBookingsData = [];
while ($booking = mysqli_fetch_assoc($confirmedBookingsResult)) {
    $confirmedBookingsData[] = $booking;
}

// Fetch pending bookings
$getPendingBookings = "SELECT bookings.*, rooms.roomName, users.email AS userEmail 
                       FROM bookings 
                       INNER JOIN rooms ON bookings.roomID = rooms.roomID 
                       INNER JOIN users ON bookings.userID = users.userID 
                       WHERE bookings.bookingStatus = 'pending' 
                       ORDER BY bookings.createdAt DESC";
$pendingBookingsResult = executeQuery($getPendingBookings);

$pendingBookingsData = [];
while ($booking = mysqli_fetch_assoc($pendingBookingsResult)) {
    $pendingBookingsData[] = $booking;
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="HOTEL-MANAGEMENT-SYSTEM/css/style.css">
</head>

<body>
    <?php include 'header.php'; ?>    

    <?php if (isset($_GET['success'])): ?>
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_GET['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_GET['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    <!-- cards -->
    <div class="container mt-5 p-5">
        <div class="row">

            <div class="col-3" id="reservationPage">
                <div class="card text-bg-primary mb-3 card-select" style="max-width: 18rem; cursor: pointer;" 
                    data-table="reservations" onclick="switchTable('reservations')">
                    <div class="card-body">
                        <h6 class="card-title p-4">Reservations</h6>
                    </div>
                    <div class="card-footer">View Details</div>
                </div>
            </div>

            <div class="col-3" id="customersPage">
                <div class="card text-bg-warning mb-3 card-select" style="max-width: 18rem; cursor: pointer;" 
                    data-table="customers" onclick="switchTable('customers')">
                    <div class="card-body">
                        <h6 class="card-title p-4">Customers</h6>
                    </div>
                    <div class="card-footer">View Details</div>
                </div>
            </div>

            <div class="col-3" id="confirmedBookingsPage">
                <div class="card text-bg-success mb-3 card-select" style="max-width: 18rem; cursor: pointer;" 
                    data-table="confirmed" onclick="switchTable('confirmed')">
                    <div class="card-body">
                        <h6 class="card-title p-4">Confirmed Bookings</h6>
                    </div>
                    <div class="card-footer">View Bookings</div>
                </div>
            </div>

            <div class="col-3" id="pendingReservationsPage">
                <div class="card text-bg-danger mb-3 card-select" style="max-width: 18rem; cursor: pointer;" 
                    data-table="pending" onclick="switchTable('pending')">
                    <div class="card-body">
                        <h6 class="card-title p-4">Pending Reservations</h6>
                    </div>
                    <div class="card-footer">View Details</div>
                </div>
            </div>

        </div>
    </div>

    <!-- table -->
    <div class="container" id="tableSection">
        <!-- Nav Tabs -->
        <ul class="nav nav-tabs mb-3" id="adminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-reservations" data-table="reservations" 
                        type="button" role="tab" onclick="switchTable('reservations')">
                    <i class="bi bi-calendar-check me-1"></i>Reservations
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-customers" data-table="customers" 
                        type="button" role="tab" onclick="switchTable('customers')">
                    <i class="bi bi-people me-1"></i>Customers
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-confirmed" data-table="confirmed" 
                        type="button" role="tab" onclick="switchTable('confirmed')">
                    <i class="bi bi-check-circle me-1"></i>Confirmed
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-pending" data-table="pending" 
                        type="button" role="tab" onclick="switchTable('pending')">
                    <i class="bi bi-clock me-1"></i>Pending
                </button>
            </li>
        </ul>

        <table class="table table-hover table-bordered rounded-4">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Email</th>
                    <th scope="col">Check-In</th>
                    <th scope="col">Check-Out</th>
                    <th scope="col">Room Type</th>
                    <th scope="col">Time-Stamp</th>
                    <th scope="col">Status</th>
                    <th scope="col">Payment</th>
                    <th scope="col">Notes</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">1</th>
                    <td>Mark@email.com</td>
                    <td>07/18/2025</td>
                    <td>08/19/2025</td>
                    <td>Deluxe Room</td>
                    <td>2025-07-01 10:00:00</td>
                    <td>Confirmed</td>
                    <td>Paid</td>
                    <td>None</td>
                </tr>
                <tr>
                    <th scope="row">2</th>
                    <td>Jacob</td>
                    <td>07/18/2025</td>
                    <td>08/19/2025</td>
                    <td>Deluxe Room</td>
                    <td>2025-07-01 10:00:00</td>
                    <td>Confirmed</td>
                    <td>Paid</td>
                    <td>None</td>
                </tr>
                <tr>
                    <th scope="row">3</th>
                    <td>John</td>
                    <td>07/18/2025</td>
                    <td>07/18/2025</td>
                    <td>Deluxe Room</td>
                    <td>2025-07-01 10:00:00</td>
                    <td>Confirmed</td>
                    <td>Paid</td>
                    <td>None</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Edit Role Modal -->
    <div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editRoleModalLabel">Edit User Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editRoleForm" action="php/update_role.php" method="POST">
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
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteUserModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="deleteUserForm" action="php/delete_user.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="userID" id="deleteUserID">
                        <p>Are you sure you want to delete user <strong id="deleteUserName"></strong>?</p>
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

    <!-- Update Booking Status Modal -->
    <div class="modal fade" id="updateBookingModal" tabindex="-1" aria-labelledby="updateBookingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="updateBookingModalLabel">Update Booking Status</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateBookingForm" action="php/update_booking.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="bookingID" id="updateBookingID">
                        <p>Booking #<strong id="updateBookingNumber"></strong></p>
                        <p>Guest: <strong id="updateBookingGuest"></strong></p>
                        <div class="mb-3">
                            <label for="newBookingStatus" class="form-label">Booking Status</label>
                            <select class="form-select" name="bookingStatus" id="newBookingStatus" required>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="newPaymentStatus" class="form-label">Payment Status</label>
                            <select class="form-select" name="paymentStatus" id="newPaymentStatus" required>
                                <option value="pending">Pending</option>
                                <option value="paid">Paid</option>
                                <option value="refunded">Refunded</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="bookingNotes" class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" id="bookingNotes" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <script>
        // Data from database
        const customersFromDb = <?php echo json_encode($customersData); ?>;
        const allBookingsFromDb = <?php echo json_encode($allBookingsData); ?>;
        const confirmedBookingsFromDb = <?php echo json_encode($confirmedBookingsData); ?>;
        const pendingBookingsFromDb = <?php echo json_encode($pendingBookingsData); ?>;

        // Helper function to format booking rows
        function formatBookingRow(booking, index) {
            const statusBadge = {
                'pending': '<span class="badge bg-warning text-dark">Pending</span>',
                'confirmed': '<span class="badge bg-success">Confirmed</span>',
                'cancelled': '<span class="badge bg-danger">Cancelled</span>',
                'completed': '<span class="badge bg-info">Completed</span>'
            };
            
            const paymentBadge = {
                'pending': '<span class="badge bg-warning text-dark">Pending</span>',
                'paid': '<span class="badge bg-success">Paid</span>',
                'refunded': '<span class="badge bg-secondary">Refunded</span>'
            };
            
            return [
                (index + 1).toString(),
                booking.userEmail,
                booking.checkInDate,
                booking.checkOutDate,
                booking.roomName,
                booking.createdAt,
                statusBadge[booking.bookingStatus] || booking.bookingStatus,
                paymentBadge[booking.paymentStatus] || booking.paymentStatus,
                booking.notes || 'None',
                `<button class="btn btn-sm btn-primary" onclick="openUpdateBookingModal(${booking.bookingID}, '${booking.fullName}', '${booking.bookingStatus}', '${booking.paymentStatus}', '${booking.notes || ''}')">
                    <i class="bi bi-pencil-square"></i> Update
                </button>`
            ];
        }

        const tableData = {
            reservations: {
                headers: ['#', 'Email', 'Check-In', 'Check-Out', 'Room', 'Booked On', 'Status', 'Payment', 'Notes', 'Actions'],
                rows: allBookingsFromDb.length > 0 
                    ? allBookingsFromDb.map((booking, index) => formatBookingRow(booking, index))
                    : [['', 'No reservations found', '', '', '', '', '', '', '', '']]
            },
            customers: {
                headers: ['#', 'Name', 'Email', 'Username', 'Phone', 'Member Since', 'Role', 'Actions'],
                rows: customersFromDb.length > 0 
                    ? customersFromDb.map((user, index) => [
                        (index + 1).toString(),
                        user.firstName + ' ' + user.lastName,
                        user.email,
                        user.username,
                        user.phoneNumber || 'N/A',
                        user.created_at,
                        user.role,
                        `<button class="btn btn-sm btn-warning me-1" onclick="openEditRoleModal(${user.userID}, '${user.firstName} ${user.lastName}', '${user.role}')">
                            <i class="bi bi-pencil-square"></i> Edit Role
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="openDeleteModal(${user.userID}, '${user.firstName} ${user.lastName}')">
                            <i class="bi bi-trash"></i> Delete
                        </button>`
                    ])
                    : [['', 'No customers found', '', '', '', '', '', '']]
            },
            confirmed: {
                headers: ['#', 'Email', 'Check-In', 'Check-Out', 'Room', 'Booked On', 'Status', 'Payment', 'Notes', 'Actions'],
                rows: confirmedBookingsFromDb.length > 0 
                    ? confirmedBookingsFromDb.map((booking, index) => formatBookingRow(booking, index))
                    : [['', 'No confirmed bookings', '', '', '', '', '', '', '', '']]
            },
            pending: {
                headers: ['#', 'Email', 'Check-In', 'Check-Out', 'Room', 'Booked On', 'Status', 'Payment', 'Notes', 'Actions'],
                rows: pendingBookingsFromDb.length > 0 
                    ? pendingBookingsFromDb.map((booking, index) => formatBookingRow(booking, index))
                    : [['', 'No pending bookings', '', '', '', '', '', '', '', '']]
            }
        };

        // Open Update Booking Modal
        function openUpdateBookingModal(bookingID, guestName, bookingStatus, paymentStatus, notes) {
            document.getElementById('updateBookingID').value = bookingID;
            document.getElementById('updateBookingNumber').textContent = bookingID;
            document.getElementById('updateBookingGuest').textContent = guestName;
            document.getElementById('newBookingStatus').value = bookingStatus;
            document.getElementById('newPaymentStatus').value = paymentStatus;
            document.getElementById('bookingNotes').value = notes;
            new bootstrap.Modal(document.getElementById('updateBookingModal')).show();
        }

        // Open Edit Role Modal
        function openEditRoleModal(userID, userName, currentRole) {
            document.getElementById('editUserID').value = userID;
            document.getElementById('editUserName').textContent = userName;
            document.getElementById('newRole').value = currentRole;
            new bootstrap.Modal(document.getElementById('editRoleModal')).show();
        }

        // Open Delete Confirmation Modal
        function openDeleteModal(userID, userName) {
            document.getElementById('deleteUserID').value = userID;
            document.getElementById('deleteUserName').textContent = userName;
            new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
        }

        function switchTable(tableType) {
            const data = tableData[tableType];
            if (!data) return;

            // Update table headers
            const thead = document.querySelector('#tableSection table thead tr');
            thead.innerHTML = data.headers.map(header => `<th scope="col">${header}</th>`).join('');

            // Update table body
            const tbody = document.querySelector('#tableSection table tbody');
            tbody.innerHTML = data.rows.map(row => `
                <tr>
                    <th scope="row">${row[0]}</th>
                    ${row.slice(1).map(cell => `<td>${cell}</td>`).join('')}
                </tr>
            `).join('');

            // Update active card styling
            document.querySelectorAll('.card-select').forEach(card => {
                card.classList.remove('border', 'border-3', 'border-dark');
            });
            const activeCard = document.querySelector(`.card-select[data-table="${tableType}"]`);
            if (activeCard) {
                activeCard.classList.add('border', 'border-3', 'border-dark');
            }

            // Update active tab styling
            document.querySelectorAll('#adminTabs .nav-link').forEach(tab => {
                tab.classList.remove('active');
            });
            const activeTab = document.querySelector(`#adminTabs [data-table="${tableType}"]`);
            if (activeTab) {
                activeTab.classList.add('active');
            }
        }

        // Initialize with reservations on page load
        document.addEventListener('DOMContentLoaded', function() {
            switchTable('reservations');
        });
    </script>
</body>

</html>