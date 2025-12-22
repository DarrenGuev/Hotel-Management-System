<?php

if (isset($_POST['add_room'])) {
    $roomName = $_POST['roomName'];
    $roomTypeId = $_POST['roomTypeId'];
    $capacity = $_POST['capacity'];
    $quantity = $_POST['quantity'];
    $base_price = $_POST['base_price'];
    $status = $_POST['status'];





    
    // // Basic validation
    // if (!empty($roomName) && !empty($roomTypeId) && is_numeric($capacity) && is_numeric($quantity) && is_numeric($base_price)) {
    //     $postQuery = "INSERT INTO `rooms`(`roomName`, `roomTypeId`, `capacity`, `quantity`, `base_price`, `status`) 
    //                 VALUES ('$roomName', '$roomTypeId', '$capacity', '$quantity', '$base_price', '$status')";
    //     if (executeQuery($postQuery)) {
    //         // Optional: Redirect or show success message
    //         header("Location: " . $_SERVER['PHP_SELF']);
    //         exit();
    //     } else {
    //         // Optional: Handle error
    //         echo "Error adding room.";
    //     }
    // }
}

include 'connect.php';
$getRooms = "SELECT r.*, rt.roomType AS roomTypeName FROM rooms r INNER JOIN roomtypes rt ON r.roomTypeId = rt.roomTypeID";
$rooms = executeQuery($getRooms);

$getRoomTypes = "SELECT * FROM roomtypes";
$roomTypes = executeQuery($getRoomTypes);

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
                            <th>Room Name</th>
                            <th>Max Occupancy</th>
                            <th>Price</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody id="roomsTableBody">
                        <?php while ($row = mysqli_fetch_assoc($rooms)) { ?>
                            <tr data-room-type="<?php echo htmlspecialchars($row['roomTypeName'], ENT_QUOTES); ?>">
                                <td><?php echo $row['roomID'] ?></td>
                                <td><?php echo $row['roomTypeName'] ?></td>
                                <td><?php echo $row['roomName'] ?></td>
                                <td><?php echo $row['capacity'] ?></td>
                                <td>₱<?php echo ($row['base_price']) ?></td>
                                <td><?php echo $row['quantity'] ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col justify-content-center text-center">
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    Click to add more rooms
                </button>

                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Input Room Details</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="roomName" class="form-label">Room Name</label>
                                        <input id="roomName" class="form-control" type="text" name="roomName" placeholder="e.g., Deluxe Suite" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="roomTypeId" class="form-label">Room Type</label>
                                        <select id="roomTypeId" class="form-select" name="roomTypeId" required>
                                            <option value="" selected disabled>-- Select Room Type --</option>
                                            <?php 
                                                // Reset pointer and loop through room types for the dropdown
                                                mysqli_data_seek($roomTypes, 0);
                                                while ($type = mysqli_fetch_assoc($roomTypes)) { 
                                            ?>
                                                <option value="<?php echo $type['roomTypeID']; ?>"><?php echo htmlspecialchars($type['roomType']); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="capacity" class="form-label">Capacity</label>
                                            <input id="capacity" class="form-control" type="number" name="capacity" placeholder="e.g., 2" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="quantity" class="form-label">Quantity</label>
                                            <input id="quantity" class="form-control" type="number" name="quantity" placeholder="e.g., 5" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="base_price" class="form-label">Price (₱)</label>
                                        <input id="base_price" class="form-control" type="number" step="0.01" name="base_price" placeholder="e.g., 1500.00" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select id="status" class="form-select" name="status" required>
                                            <option value="available">Available</option>
                                            <option value="maintenance">Maintenance</option>
                                            <option value="unavailable">Unavailable</option>
                                        </select>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" name="add_room" class="btn btn-primary">Save Room</button>
                                    </div>
                                </form>
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