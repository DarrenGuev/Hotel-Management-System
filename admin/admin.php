<?php
include 'connect.php';

// Fetch users from database for customers tab
$getUsersQuery = "SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC";
$usersResult = executeQuery($getUsersQuery);

$customersData = [];
while ($user = mysqli_fetch_assoc($usersResult)) {
    $customersData[] = $user;
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <script>
        // Customer data from database
        const customersFromDb = <?php echo json_encode($customersData); ?>;

        const tableData = {
            reservations: {
                headers: ['#', 'Email', 'Check-In', 'Check-Out', 'Room Type', 'Time-Stamp', 'Status', 'Notes'],
                rows: [
                    ['1', 'mark@email.com', '07/18/2025', '08/19/2025', 'Deluxe Room', '2025-07-01 10:00:00', 'Pending', 'Awaiting confirmation'],
                    ['2', 'sarah@email.com', '07/20/2025', '07/25/2025', 'Basic Room', '2025-07-02 14:30:00', 'Pending', 'Early check-in requested'],
                    ['3', 'mike@email.com', '08/01/2025', '08/05/2025', 'Family Room', '2025-07-03 09:15:00', 'Pending', 'None']
                ]
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
                headers: ['#', 'Email', 'Check-In', 'Check-Out', 'Room Type', 'Time-Stamp', 'Status', 'Notes'],
                rows: [
                    ['1', 'john@email.com', '07/10/2025', '07/15/2025', 'Twin Room', '2025-06-28 11:00:00', 'Confirmed', 'Paid in full'],
                    ['2', 'anna@email.com', '07/12/2025', '07/14/2025', 'Single Room', '2025-06-29 16:45:00', 'Confirmed', 'None'],
                    ['3', 'peter@email.com', '07/18/2025', '07/22/2025', 'Deluxe Room', '2025-07-01 08:30:00', 'Confirmed', 'Airport pickup']
                ]
            },
            pending: {
                headers: ['#', 'Email', 'Check-In', 'Check-Out', 'Room Type', 'Time-Stamp', 'Status', 'Action Required'],
                rows: [
                    ['1', 'guest1@email.com', '07/25/2025', '07/28/2025', 'Basic Room', '2025-07-04 10:00:00', 'Pending', 'Awaiting payment'],
                    ['2', 'guest2@email.com', '08/01/2025', '08/03/2025', 'Twin Room', '2025-07-04 12:30:00', 'Pending', 'ID verification needed'],
                    ['3', 'guest3@email.com', '08/05/2025', '08/10/2025', 'Family Room', '2025-07-04 15:00:00', 'Pending', 'Awaiting confirmation']
                ]
            }
        };

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
            thead.innerHTML = data.headers.map((header, index) => 
                index === 0 ? `<th scope="col">${header}</th>` : `<th scope="col">${header}</th>`
            ).join('');

            // Update table body - use innerHTML for action buttons
            const tbody = document.querySelector('#tableSection table tbody');
            if (tableType === 'customers') {
                tbody.innerHTML = data.rows.map(row => `
                    <tr>
                        <th scope="row">${row[0]}</th>
                        ${row.slice(1).map((cell, idx) => 
                            idx === row.length - 2 ? `<td>${cell}</td>` : `<td>${cell}</td>`
                        ).join('')}
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = data.rows.map(row => `
                    <tr>
                        <th scope="row">${row[0]}</th>
                        ${row.slice(1).map(cell => `<td>${cell}</td>`).join('')}
                    </tr>
                `).join('');
            }

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