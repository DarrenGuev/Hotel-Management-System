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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <script>
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
                headers: ['#', 'Name', 'Email', 'Phone', 'Total Bookings', 'Member Since', 'Status', 'Notes'],
                rows: [
                    ['1', 'Mark Johnson', 'mark@email.com', '+63 912 345 6789', '5', '2024-01-15', 'Active', 'VIP Customer'],
                    ['2', 'Sarah Smith', 'sarah@email.com', '+63 923 456 7890', '2', '2024-06-20', 'Active', 'None'],
                    ['3', 'Mike Brown', 'mike@email.com', '+63 934 567 8901', '1', '2025-01-01', 'New', 'First-time guest']
                ]
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

        function switchTable(tableType) {
            const data = tableData[tableType];
            if (!data) return;

            // Update table headers
            const thead = document.querySelector('#tableSection table thead tr');
            thead.innerHTML = data.headers.map((header, index) => 
                index === 0 ? `<th scope="col">${header}</th>` : `<th scope="col">${header}</th>`
            ).join('');

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

            // ==========================================
            // BACKEND INTEGRATION POINT
            // ==========================================
            // When ready for backend, replace the above with:
            //
            // fetch(`/api/admin/${tableType}`)
            //     .then(response => response.json())
            //     .then(data => {
            //         // Update headers
            //         const thead = document.querySelector('#tableSection table thead tr');
            //         thead.innerHTML = data.headers.map(h => `<th scope="col">${h}</th>`).join('');
            //         
            //         // Update body
            //         const tbody = document.querySelector('#tableSection table tbody');
            //         tbody.innerHTML = data.rows.map(row => `
            //             <tr>
            //                 <th scope="row">${row[0]}</th>
            //                 ${row.slice(1).map(cell => `<td>${cell}</td>`).join('')}
            //             </tr>
            //         `).join('');
            //     })
            //     .catch(error => console.error('Error:', error));
        }

        // Initialize with reservations on page load
        document.addEventListener('DOMContentLoaded', function() {
            switchTable('reservations');
        });
    </script>
</body>

</html>