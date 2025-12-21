<?php
// Sample data - expanded for filtering demonstration
$rooms = [
    ["id" => 1, "type" => "Deluxe", "price" => 3500, "status" => "Available"],
    ["id" => 2, "type" => "Standard", "price" => 2500, "status" => "Booked"],
    ["id" => 3, "type" => "Suite", "price" => 5000, "status" => "Available"],
    ["id" => 4, "type" => "Basic", "price" => 1800, "status" => "Available"],
    ["id" => 5, "type" => "Twin", "price" => 2800, "status" => "Maintenance"],
    ["id" => 6, "type" => "Family", "price" => 4500, "status" => "Available"],
    ["id" => 7, "type" => "Basic", "price" => 1800, "status" => "Booked"],
    ["id" => 8, "type" => "Deluxe", "price" => 3600, "status" => "Available"],
];
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container p-5 mt-5">
        <div class="row">
            <div class="col-12">
                <ul class="nav nav-tabs mb-3" id="roomTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-room-type="All" type="button" role="tab" onclick="filterRooms('All')">
                            <h6>All Rooms</h6>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-room-type="Basic" type="button" role="tab" onclick="filterRooms('Basic')">
                            <h6>Basic Room</h6>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-room-type="Twin" type="button" role="tab" onclick="filterRooms('Twin')">
                            <h6>Twin Room</h6>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-room-type="Deluxe" type="button" role="tab" onclick="filterRooms('Deluxe')">
                            <h6>Deluxe Room</h6>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-room-type="Family" type="button" role="tab" onclick="filterRooms('Family')">
                            <h6>Family Room</h6>
                        </button>
                    </li>
                </ul>

                <table class="table table-hover table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Room Type</th>
                            <th>Price</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="roomsTableBody">
                        <?php foreach ($rooms as $room) { ?>
                            <tr data-room-type="<?= $room['type'] ?>">
                                <td><?= $room['id'] ?></td>
                                <td><?= $room['type'] ?></td>
                                <td>₱<?= number_format($room['price']) ?></td>
                                <td>
                                    <span class="badge <?= $room['status'] === 'Available' ? 'bg-success' : ($room['status'] === 'Booked' ? 'bg-danger' : 'bg-warning') ?>">
                                        <?= $room['status'] ?>
                                    </span>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <script>
        function filterRooms(roomType) {
            // Update active tab
            document.querySelectorAll('#roomTabs .nav-link').forEach(tab => {
                tab.classList.remove('active');
            });
            const activeTab = document.querySelector(`#roomTabs [data-room-type="${roomType}"]`);
            if (activeTab) {
                activeTab.classList.add('active');
            }

            // Filter table rows
            const tableRows = document.querySelectorAll('#roomsTableBody tr');
            tableRows.forEach(row => {
                if (roomType === 'All' || row.dataset.roomType === roomType) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Initialize with 'All' rooms visible
        document.addEventListener('DOMContentLoaded', function() {
            filterRooms('All');
        });
    </script>
</body>

</html>