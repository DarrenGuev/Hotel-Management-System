<?php
include 'connect.php';

if (isset($_POST['add_room'])) {
    $roomName = $_POST['roomName'];
    $roomTypeId = $_POST['roomTypeId'];
    $capacity = $_POST['capacity'];
    $quantity = $_POST['quantity'];
    $base_price = $_POST['base_price'];
    $selectedFeatures = isset($_POST['features']) ? $_POST['features'] : [];

    // Image Handling for Add
    $fileName = isset($_FILES['roomImage']) && $_FILES['roomImage']['error'] === UPLOAD_ERR_OK ? $_FILES['roomImage']['name'] : '';
    $tempName = isset($_FILES['roomImage']) && $_FILES['roomImage']['error'] === UPLOAD_ERR_OK ? $_FILES['roomImage']['tmp_name'] : '';
    $folder = "assets/" . $fileName; 

    if (!empty($roomName) && !empty($roomTypeId)) {
        // Move file first
        move_uploaded_file($tempName, $folder);

        $postQuery = "INSERT INTO `rooms`(`roomName`, `roomTypeId`, `capacity`, `quantity`, `base_price`, `imagePath`) 
                    VALUES ('$roomName', '$roomTypeId', '$capacity', '$quantity', '$base_price', '$fileName')";
        
        if (executeQuery($postQuery)) {
            $newRoomID = mysqli_insert_id($conn);
            
            // Insert selected features into roomfeatures
            foreach ($selectedFeatures as $featureId) {
                $featureId = (int)$featureId;
                $insertFeatureQuery = "INSERT INTO `roomfeatures`(`roomID`, `featureID`) VALUES ('$newRoomID', '$featureId')";
                executeQuery($insertFeatureQuery);
            }
            
            echo '<div class="alert alert-success position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 9999;">Room Added!</div>';
        }
    }
}


if (isset($_POST['deleteID'])) {
    $deleteID = $_POST['deleteID'];
    $deleteQuery = "DELETE FROM rooms WHERE roomID = '$deleteID'";

    executeQuery($deleteQuery);
}

if (isset($_POST['update_room'])) {
    $roomID = $_POST['roomID'];
    $roomName = $_POST['editRoomName'];
    $roomTypeId = $_POST['editRoomTypeId'];
    $capacity = $_POST['editCapacity'];
    $quantity = $_POST['editQuantity'];
    $base_price = $_POST['editBasePrice'];
    $selectedFeatures = isset($_POST['editFeatures']) ? $_POST['editFeatures'] : [];
    $fileName = isset($_FILES['roomImage']) && $_FILES['roomImage']['error'] === UPLOAD_ERR_OK ? $_FILES['roomImage']['name'] : '';
    $tempName = isset($_FILES['roomImage']) && $_FILES['roomImage']['error'] === UPLOAD_ERR_OK ? $_FILES['roomImage']['tmp_name'] : '';
    $folder = "assets/" . $fileName;

    if (!empty($roomID) && !empty($roomName) && !empty($roomTypeId) && is_numeric($capacity) && is_numeric($quantity) && is_numeric($base_price)) {
        $updateQuery = "UPDATE `rooms` SET 
                        `roomName`='$roomName', 
                        `roomTypeId`='$roomTypeId', 
                        `capacity`='$capacity', 
                        `quantity`='$quantity', 
                        `base_price`='$base_price'";
        if (!empty($fileName)) {
            if (move_uploaded_file($tempName, $folder)) {
                $updateQuery .= ", `imagePath`='$fileName'";
            }
        }

        $updateQuery .= " WHERE `roomID`='$roomID'";

        if (executeQuery($updateQuery)) {
            // Delete existing features for this room
            $deleteFeatures = "DELETE FROM `roomfeatures` WHERE `roomID`='$roomID'";
            executeQuery($deleteFeatures);
            
            // Insert new selected features
            foreach ($selectedFeatures as $featureId) {
                $featureId = (int)$featureId;
                $insertFeatureQuery = "INSERT INTO `roomfeatures`(`roomID`, `featureID`) VALUES ('$roomID', '$featureId')";
                executeQuery($insertFeatureQuery);
            }
            
            echo '<div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3"
                role="alert" style="z-index: 99999; max-width: 600px; width: calc(100% - 2rem);">
                Room updated successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        } else {
            echo '<div class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3"
                role="alert" style="z-index: 99999; max-width: 600px; width: calc(100% - 2rem);">
                Error updating room.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        }
    }
}

$getRooms = "SELECT rooms.*, roomtypes.roomType AS roomTypeName FROM rooms INNER JOIN roomtypes ON rooms.roomTypeId = roomtypes.roomTypeID";
$rooms = executeQuery($getRooms);

$getRoomTypes = "SELECT * FROM roomtypes";
$roomTypes = executeQuery($getRoomTypes);

$getFeatures = "SELECT * FROM features ORDER BY featureId";
$features = executeQuery($getFeatures);

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
    <div class="container-fluid p-5 mt-5">
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
                        <button class="nav-link" data-room-type="Family" type="button" role="tab" onclick="filterRooms('Family')">
                            <h6>Family Room</h6>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-room-type="Suite" type="button" role="tab" onclick="filterRooms('Suite')">
                            <h6>Suite Room</h6>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-room-type="Deluxe" type="button" role="tab" onclick="filterRooms('Deluxe')">
                            <h6>Deluxe Room</h6>
                        </button>
                    </li>
                </ul>

                <table class="table table-hover table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Room Type</th>
                            <th scope="col">Room Name</th>
                            <th scope="col">Max Occupancy</th>
                            <th scope="col">Features</th>
                            <th scope="col">Price</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Room Image</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody id="roomsTableBody">
                        <?php while ($row = mysqli_fetch_assoc($rooms)) { 
                            // Get features for this room
                            $roomFeaturesQuery = "SELECT f.featureName FROM features f INNER JOIN roomfeatures rf ON f.featureId = rf.featureID  WHERE rf.roomID = " . (int)$row['roomID'];
                            $roomFeaturesResult = executeQuery($roomFeaturesQuery);
                            $roomFeatures = [];
                            while ($feature = mysqli_fetch_assoc($roomFeaturesResult)) {
                                $roomFeatures[] = $feature['featureName'];
                            }
                            
                            // Get feature IDs for this room (for edit modal)
                            $roomFeatureIdsQuery = "SELECT featureID FROM roomfeatures WHERE roomID = " . (int)$row['roomID'];
                            $roomFeatureIdsResult = executeQuery($roomFeatureIdsQuery);
                            $roomFeatureIds = [];
                            while ($fid = mysqli_fetch_assoc($roomFeatureIdsResult)) {
                                $roomFeatureIds[] = $fid['featureID'];
                            }
                        ?>
                            <tr data-room-type="<?php echo htmlspecialchars($row['roomTypeName'], ENT_QUOTES); ?>">
                                <td scope="col"><?php echo $row['roomID'] ?></td>
                                <td scope="col"><?php echo $row['roomTypeName'] ?></td>
                                <td scope="col"><?php echo $row['roomName'] ?></td>
                                <td scope="col"><?php echo $row['capacity'] ?></td>
                                <td scope="col">
                                    <?php if (!empty($roomFeatures)) { 
                                        foreach ($roomFeatures as $featureName) { ?>
                                            <span class="badge bg-secondary me-1 mb-1"><?php echo htmlspecialchars($featureName); ?></span>
                                        <?php }
                                    } else { ?>
                                        <span class="text-muted">No features</span>
                                    <?php } ?>
                                </td>
                                <td scope="col">₱<?php echo ($row['base_price']) ?></td>
                                <td scope="col"><?php echo $row['quantity'] ?></td>
                                <td scope="col"><?php echo '<img src="assets/' . $row['imagePath'] . '" style="width:200px;">'; ?></td>
                                <td scope="col" class="text-center">
                                    <form method="POST" style="display: inline-block;">
                                        <input type="hidden" value="<?php echo $row['roomID'] ?>" name="deleteID">
                                        <button class="btn btn-outline-danger btn-sm" type="submit">Delete</button>
                                    </form>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['roomID']; ?>">
                                        Edit
                                    </button>

                                    <!-- Edit Modal for Room <?php echo $row['roomID']; ?> -->
                                    <div class="modal fade" id="editModal<?php echo $row['roomID']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $row['roomID']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="editModalLabel<?php echo $row['roomID']; ?>">Edit Room Details</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-start">
                                                    <form method="POST" enctype="multipart/form-data">
                                                        <input type="hidden" name="roomID" value="<?php echo $row['roomID']; ?>">
                                                        <div class="mb-3">
                                                            <label for="editRoomName<?php echo $row['roomID']; ?>" class="form-label">Room Name</label>
                                                            <input id="editRoomName<?php echo $row['roomID']; ?>" class="form-control" type="text" name="editRoomName" value="<?php echo htmlspecialchars($row['roomName']); ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="editRoomTypeId<?php echo $row['roomID']; ?>" class="form-label">Room Type</label>
                                                            <select id="editRoomTypeId<?php echo $row['roomID']; ?>" class="form-select" name="editRoomTypeId" required>
                                                                <?php
                                                                mysqli_data_seek($roomTypes, 0);
                                                                while ($type = mysqli_fetch_assoc($roomTypes)) {
                                                                    $selected = ($type['roomTypeID'] == $row['roomTypeId']) ? 'selected' : '';
                                                                ?>
                                                                    <option value="<?php echo $type['roomTypeID']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($type['roomType']); ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6 mb-3">
                                                                <label for="editCapacity<?php echo $row['roomID']; ?>" class="form-label">Capacity</label>
                                                                <input id="editCapacity<?php echo $row['roomID']; ?>" class="form-control" type="number" name="editCapacity" value="<?php echo $row['capacity']; ?>" required>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label for="editQuantity<?php echo $row['roomID']; ?>" class="form-label">Quantity</label>
                                                                <input id="editQuantity<?php echo $row['roomID']; ?>" class="form-control" type="number" name="editQuantity" value="<?php echo $row['quantity']; ?>" required>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6 mb-3">
                                                            <label for="editBasePrice<?php echo $row['roomID']; ?>" class="form-label">Price (₱)</label>
                                                            <input id="editBasePrice<?php echo $row['roomID']; ?>" class="form-control" type="number" step="0.01" name="editBasePrice" value="<?php echo $row['base_price']; ?>" required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="editRoomImage<?php echo $row['roomID']; ?>" class="form-label">Room Image</label>
                                                            <input id="editRoomImage<?php echo $row['roomID']; ?>" class="form-control" type="file" name="roomImage" accept="image/*">
                                                        </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Room Features</label>
                                                            <div class="row">
                                                                <?php
                                                                mysqli_data_seek($features, 0);
                                                                while ($feature = mysqli_fetch_assoc($features)) {
                                                                    $checked = in_array($feature['featureId'], $roomFeatureIds) ? 'checked' : '';
                                                                ?>
                                                                    <div class="col-6">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox" name="editFeatures[]" value="<?php echo $feature['featureId']; ?>" id="editFeature<?php echo $row['roomID'] . '_' . $feature['featureId']; ?>" <?php echo $checked; ?>>
                                                                            <label class="form-check-label" for="editFeature<?php echo $row['roomID'] . '_' . $feature['featureId']; ?>">
                                                                                <?php echo htmlspecialchars($feature['featureName']); ?>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" name="update_room" class="btn btn-primary">Save Changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
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
                <button type="button" class="btn btn-warning mb-4" data-bs-toggle="modal" data-bs-target="#exampleModal">
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
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label for="roomName" class="form-label">Room Name</label>
                                        <input id="roomName" class="form-control" type="text" name="roomName" placeholder="e.g., Deluxe Suite" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="roomTypeId" class="form-label">Room Type</label>
                                        <select id="roomTypeId" class="form-select" name="roomTypeId" required>
                                            <option value="" selected disabled>-- Select Room Type --</option>
                                            <?php
                                            mysqli_data_seek($roomTypes, 0);
                                            while ($type = mysqli_fetch_assoc($roomTypes)) {
                                            ?>
                                                <option value="<?php echo $type['roomTypeID']; ?>"><?php echo htmlspecialchars($type['roomType']); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="capacity" class="form-label">Maximum Guest Capacity</label>
                                            <input id="capacity" class="form-control" type="number" name="capacity" placeholder="number of max capacity" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="quantity" class="form-label">Quantity of Rooms</label>
                                            <input id="quantity" class="form-control" type="number" name="quantity" placeholder="number of rooms" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="base_price" class="form-label">Price (₱)</label>
                                            <input id="base_price" class="form-control" type="number" step="0.01" name="base_price" placeholder="e.g., 1500.00" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="roomImage" class="form-label">Room Image</label>
                                            <input id="roomImage" class="form-control" type="file" name="roomImage" accept="image/*">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Room Features</label>
                                        <div class="row">
                                            <?php
                                            mysqli_data_seek($features, 0);
                                            while ($feature = mysqli_fetch_assoc($features)) {
                                            ?>
                                                <div class="col-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="features[]" value="<?php echo $feature['featureId']; ?>" id="feature<?php echo $feature['featureId']; ?>">
                                                        <label class="form-check-label" for="feature<?php echo $feature['featureId']; ?>">
                                                            <?php echo htmlspecialchars($feature['featureName']); ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" name="add_room" class="btn btn-warning">Save Room</button>
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