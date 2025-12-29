<?php
include 'connect.php';

// Helper function to handle image upload
function handleImageUpload($fileInput, $targetFolder = "assets/") {
    if (!isset($_FILES[$fileInput]) || $_FILES[$fileInput]['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'fileName' => '', 'error' => 'No file uploaded or upload error'];
    }

    $file = $_FILES[$fileInput];
    $tempName = $file['tmp_name'];
    $originalName = $file['name'];
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = mime_content_type($tempName);
    
    if (!in_array($fileType, $allowedTypes)) {
        return ['success' => false, 'fileName' => '', 'error' => 'Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.'];
    }

    // Generate unique filename to prevent overwrites
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $uniqueName = uniqid('room_', true) . '.' . strtolower($extension);
    
    // Ensure target folder exists
    if (!is_dir($targetFolder)) {
        mkdir($targetFolder, 0755, true);
    }
    
    $targetPath = $targetFolder . $uniqueName;
    
    if (move_uploaded_file($tempName, $targetPath)) {
        return ['success' => true, 'fileName' => $uniqueName, 'error' => ''];
    } else {
        return ['success' => false, 'fileName' => '', 'error' => 'Failed to move uploaded file. Check folder permissions.'];
    }
}

if (isset($_POST['add_room'])) {
    $roomName = mysqli_real_escape_string($conn, $_POST['roomName']);
    $roomTypeId = (int)$_POST['roomTypeId'];
    $capacity = (int)$_POST['capacity'];
    $quantity = (int)$_POST['quantity'];
    $basePrice = (float)$_POST['base_price'];
    $selectedFeatures = isset($_POST['features']) ? $_POST['features'] : [];
    
    // Handle image upload
    $uploadResult = handleImageUpload('roomImage', 'assets/');
    $fileName = $uploadResult['fileName'];
    
    if (!$uploadResult['success'] && isset($_FILES['roomImage']) && $_FILES['roomImage']['error'] !== UPLOAD_ERR_NO_FILE) {
        echo '<div class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" 
            role="alert" style="z-index: 99999; max-width: 600px; width: calc(100% - 2rem);">
            Image upload failed: ' . htmlspecialchars($uploadResult['error']) . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    }

    if (!empty($roomName) && !empty($roomTypeId)) {
        $postQuery = "INSERT INTO `rooms`(`roomName`, `roomTypeId`, `capacity`, `quantity`, `base_price`, `imagePath`) 
                    VALUES ('$roomName', '$roomTypeId', '$capacity', '$quantity', '$basePrice', '$fileName')";
        
        if (executeQuery($postQuery)) {
            $newRoomID = mysqli_insert_id($conn);
            foreach ($selectedFeatures as $featureId) {
                $featureId = (int)$featureId;
                $insertFeatureQuery = "INSERT INTO `roomfeatures`(`roomID`, `featureID`) VALUES ('$newRoomID', '$featureId')";
                executeQuery($insertFeatureQuery);
            }
            
            echo '<div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" 
                role="alert" style="z-index: 99999; max-width: 600px; width: calc(100% - 2rem);">
                Room Added Successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        }
    }
}

if (isset($_POST['deleteID'])) {
    $deleteID = (int)$_POST['deleteID'];
    
    // Get image path before deleting to remove the file
    $getImageQuery = "SELECT imagePath FROM rooms WHERE roomID = '$deleteID'";
    $imageResult = executeQuery($getImageQuery);
    if ($imageRow = mysqli_fetch_assoc($imageResult)) {
        $imagePath = 'assets/' . $imageRow['imagePath'];
        if (file_exists($imagePath) && !empty($imageRow['imagePath'])) {
            unlink($imagePath); // Delete the image file
        }
    }
    
    $deleteQuery = "DELETE FROM rooms WHERE roomID = '$deleteID'";
    executeQuery($deleteQuery);
}

if (isset($_POST['update_room'])) {
    $roomID = (int)$_POST['roomID'];
    $roomName = mysqli_real_escape_string($conn, $_POST['editRoomName']);
    $roomTypeId = (int)$_POST['editRoomTypeId'];
    $capacity = (int)$_POST['editCapacity'];
    $quantity = (int)$_POST['editQuantity'];
    $basePrice = (float)$_POST['editBasePrice'];
    $selectedFeatures = isset($_POST['editFeatures']) ? $_POST['editFeatures'] : [];

    if (!empty($roomID) && !empty($roomName) && !empty($roomTypeId) && is_numeric($capacity) && is_numeric($quantity) && is_numeric($basePrice)) {
        $updateQuery = "UPDATE `rooms` SET 
                        `roomName`='$roomName', 
                        `roomTypeId`='$roomTypeId', 
                        `capacity`='$capacity', 
                        `quantity`='$quantity', 
                        `base_price`='$basePrice'";
        
        if (isset($_FILES['editRoomImage']) && $_FILES['editRoomImage']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = handleImageUpload('editRoomImage', 'assets/');
            
            if ($uploadResult['success']) {
                $getOldImageQuery = "SELECT imagePath FROM rooms WHERE roomID = '$roomID'";
                $oldImageResult = executeQuery($getOldImageQuery);
                if ($oldImageRow = mysqli_fetch_assoc($oldImageResult)) {
                    $oldImagePath = 'assets/' . $oldImageRow['imagePath'];
                    if (file_exists($oldImagePath) && !empty($oldImageRow['imagePath'])) {
                        unlink($oldImagePath);
                    }
                }
                
                $updateQuery .= ", `imagePath`='" . $uploadResult['fileName'] . "'";
            } else {
                echo '<div class="alert alert-warning alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" 
                    role="alert" style="z-index: 99999; max-width: 600px; width: calc(100% - 2rem);">
                    Image upload failed: ' . htmlspecialchars($uploadResult['error']) . '. Other details were updated.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            }
        }
        
        $updateQuery .= " WHERE `roomID`='$roomID'";

        if (executeQuery($updateQuery)) {
            $deleteFeatures = "DELETE FROM `roomfeatures` WHERE `roomID`='$roomID'";
            executeQuery($deleteFeatures);
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

$getFeatures = "SELECT * FROM features ORDER BY category, featureId";
$features = executeQuery($getFeatures);

// Organize features by category
$featuresByCategory = [];
while ($feature = mysqli_fetch_assoc($features)) {
    $cat = $feature['category'] ?? 'General';
    if (!isset($featuresByCategory[$cat])) {
        $featuresByCategory[$cat] = [];
    }
    $featuresByCategory[$cat][] = $feature;
}
// Reset pointer for later use
mysqli_data_seek($features, 0);

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rooms</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="HOTEL-MANAGEMENT-SYSTEM/css/style.css">
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
                                <td scope="col" class="text-center align-middle"><?php echo $row['roomID'] ?></td>
                                <td scope="col" class="text-center align-middle "><?php echo $row['roomTypeName'] ?></td>
                                <td scope="col" class="text-center align-middle"><?php echo $row['roomName'] ?></td>
                                <td scope="col" class="text-center align-middle"><?php echo $row['capacity'] ?></td>
                                <td scope="col" class="align-middle">
                                    <?php if (!empty($roomFeatures)) { 
                                        foreach ($roomFeatures as $featureName) { ?>
                                            <span class="badge bg-secondary me-1 mb-1"><?php echo htmlspecialchars($featureName); ?></span>
                                        <?php }
                                    } else { ?>
                                        <span class="text-muted">No features</span>
                                    <?php } ?>
                                </td>
                                <td scope="col" class="text-center align-middle">₱<?php echo ($row['base_price']) ?></td>
                                <td scope="col" class="text-center align-middle"><?php echo $row['quantity'] ?></td>
                                <td scope="col" class="text-center align-middle"><?php echo '<img src="assets/' . $row['imagePath'] . '" style="width:200px;">'; ?></td>
                                <td scope="col" class="text-center align-middle">
                                    <form method="POST" style="display: inline-block;">
                                        <input type="hidden" value="<?php echo $row['roomID'] ?>" name="deleteID">
                                        <button class="btn btn-outline-danger btn-sm m-2" type="submit">Delete</button>
                                    </form>
                                    <button type="button" class="btn btn-outline-primary btn-sm m-2" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['roomID']; ?>">
                                        Edit
                                    </button>

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
                                                            <input id="editRoomImage<?php echo $row['roomID']; ?>" class="form-control" type="file" name="editRoomImage" accept="image/*">
                                                            <?php if (!empty($row['imagePath'])) { ?>
                                                                <small class="text-muted">Current image:</small>
                                                                <img src="assets/<?php echo htmlspecialchars($row['imagePath']); ?>" class="img-thumbnail mt-1" style="max-width: 100px; max-height: 60px;">
                                                            <?php } ?>
                                                        </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Room Features</label>
                                                            <div id="editRoomFeaturesContainer<?php echo $row['roomID']; ?>">
                                                                <?php foreach ($featuresByCategory as $category => $categoryFeatures) { ?>
                                                                    <div class="mb-3">
                                                                        <h6 class="text-muted border-bottom pb-1"><i class="bi bi-tag-fill me-1"></i><?php echo htmlspecialchars($category); ?></h6>
                                                                        <div class="row">
                                                                            <?php foreach ($categoryFeatures as $feature) { 
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
                                                                <?php } ?>
                                                            </div>
                                                            <div class="mt-3 border-top pt-3">
                                                                <label class="form-label text-muted small">Add Custom Feature</label>
                                                                <div class="input-group">
                                                                    <select class="form-select" id="customFeatureCategoryInputEdit<?php echo $row['roomID']; ?>" style="max-width: 140px;">
                                                                        <option value="Beds">Beds</option>
                                                                        <option value="Rooms">Rooms</option>
                                                                        <option value="Bathroom">Bathroom</option>
                                                                        <option value="Amenities">Amenities</option>
                                                                        <option value="Entertainment">Entertainment</option>
                                                                        <option value="General" selected>General</option>
                                                                    </select>
                                                                    <input type="text" class="form-control" id="customFeatureInputEdit<?php echo $row['roomID']; ?>" placeholder="Enter new feature name">
                                                                    <button type="button" class="btn btn-outline-success" onclick="addCustomFeature('editRoomFeaturesContainer<?php echo $row['roomID']; ?>', 'customFeatureInputEdit<?php echo $row['roomID']; ?>', 'editFeatures[]', '<?php echo $row['roomID']; ?>', 'customFeatureCategoryInputEdit<?php echo $row['roomID']; ?>')">
                                                                        <i class="bi bi-plus-lg"></i> Add
                                                                    </button>
                                                                </div>
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
                                        <div id="addRoomFeaturesContainer">
                                            <?php foreach ($featuresByCategory as $category => $categoryFeatures) { ?>
                                                <div class="mb-3">
                                                    <h6 class="text-muted border-bottom pb-1"><i class="bi bi-tag-fill me-1"></i><?php echo htmlspecialchars($category); ?></h6>
                                                    <div class="row">
                                                        <?php foreach ($categoryFeatures as $feature) { ?>
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
                                            <?php } ?>
                                        </div>
                                        <div class="mt-3 border-top pt-3">
                                            <label class="form-label text-muted small">Add Custom Feature</label>
                                            <div class="input-group">
                                                <select class="form-select" id="customFeatureCategoryInput" style="max-width: 140px;">
                                                    <option value="Beds">Beds</option>
                                                    <option value="Rooms">Rooms</option>
                                                    <option value="Bathroom">Bathroom</option>
                                                    <option value="Amenities">Amenities</option>
                                                    <option value="Entertainment">Entertainment</option>
                                                    <option value="General" selected>General</option>
                                                </select>
                                                <input type="text" class="form-control" id="customFeatureInput" placeholder="Enter new feature name">
                                                <button type="button" class="btn btn-outline-success" onclick="addCustomFeature('addRoomFeaturesContainer', 'customFeatureInput', 'features[]', null, 'customFeatureCategoryInput')">
                                                    <i class="bi bi-plus-lg"></i> Add
                                                </button>
                                            </div>
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
        document.addEventListener('DOMContentLoaded', function() {
            filterRooms('All');
        });

        // Function to add custom feature via AJAX
        function addCustomFeature(containerId, inputId, checkboxName, roomId = null, categorySelectId = null) {
            const input = document.getElementById(inputId);
            const featureName = input.value.trim();
            
            // Get category from dropdown if provided
            let category = 'General';
            if (categorySelectId) {
                const categorySelect = document.getElementById(categorySelectId);
                if (categorySelect) {
                    category = categorySelect.value;
                }
            }
            
            if (!featureName) {
                alert('Please enter a feature name');
                input.focus();
                return;
            }
            
            // Create FormData
            const formData = new FormData();
            formData.append('featureName', featureName);
            formData.append('category', category);
            
            // Send AJAX request
            fetch('php/add_feature.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add new checkbox to the container under the correct category section
                    addFeatureCheckboxToCategory(containerId, data.featureId, data.featureName, data.category, checkboxName, roomId, true);
                    
                    // Also add to all other feature containers on the page
                    addFeatureToAllContainers(data.featureId, data.featureName, data.category, containerId);
                    
                    // Clear input
                    input.value = '';
                    
                    // Show success message
                    showToast('Feature "' + data.featureName + '" added to ' + data.category + ' successfully!', 'success');
                } else if (data.error === 'Feature already exists') {
                    // Feature exists, just check the existing checkbox if available
                    const existingCheckbox = document.querySelector('#' + containerId + ' input[value="' + data.featureId + '"]');
                    if (existingCheckbox) {
                        existingCheckbox.checked = true;
                        showToast('Feature already exists. It has been selected.', 'info');
                    } else {
                        // Add the checkbox since it's not in this container
                        addFeatureCheckboxToCategory(containerId, data.featureId, featureName, data.category || 'General', checkboxName, roomId, true);
                        showToast('Feature already exists. It has been added and selected.', 'info');
                    }
                    input.value = '';
                } else {
                    showToast('Error: ' + data.error, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred while adding the feature', 'danger');
            });
        }
        
        // Function to add feature checkbox to a specific category section
        function addFeatureCheckboxToCategory(containerId, featureId, featureName, category, checkboxName, roomId = null, isChecked = false) {
            const container = document.getElementById(containerId);
            if (!container) return;
            
            // Check if checkbox already exists
            const existingCheckbox = container.querySelector('input[value="' + featureId + '"]');
            if (existingCheckbox) {
                if (isChecked) existingCheckbox.checked = true;
                return;
            }
            
            // Find or create the category section
            let categorySection = container.querySelector('[data-category="' + category + '"]');
            
            if (!categorySection) {
                // Create new category section
                categorySection = document.createElement('div');
                categorySection.className = 'mb-3';
                categorySection.setAttribute('data-category', category);
                categorySection.innerHTML = `
                    <h6 class="text-muted border-bottom pb-1"><i class="bi bi-tag-fill me-1"></i>${escapeHtml(category)}</h6>
                    <div class="row category-features"></div>
                `;
                container.appendChild(categorySection);
            }
            
            // Find the row within the category section
            let featuresRow = categorySection.querySelector('.category-features') || categorySection.querySelector('.row');
            if (!featuresRow) {
                featuresRow = document.createElement('div');
                featuresRow.className = 'row category-features';
                categorySection.appendChild(featuresRow);
            }
            
            // Create unique ID for the checkbox
            const checkboxId = roomId ? 'editFeature' + roomId + '_' + featureId : 'feature' + featureId;
            
            // Create the checkbox HTML
            const colDiv = document.createElement('div');
            colDiv.className = 'col-6';
            colDiv.innerHTML = `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="${checkboxName}" value="${featureId}" id="${checkboxId}" ${isChecked ? 'checked' : ''}>
                    <label class="form-check-label" for="${checkboxId}">
                        ${escapeHtml(featureName)}
                    </label>
                </div>
            `;
            
            featuresRow.appendChild(colDiv);
        }
        
        // Function to add feature checkbox to a container (legacy - used for simple append)
        function addFeatureCheckbox(containerId, featureId, featureName, checkboxName, roomId = null, isChecked = false) {
            addFeatureCheckboxToCategory(containerId, featureId, featureName, 'General', checkboxName, roomId, isChecked);
        }
        
        // Function to add feature to all containers on the page
        function addFeatureToAllContainers(featureId, featureName, category, excludeContainerId) {
            // Add to main add room container
            if (excludeContainerId !== 'addRoomFeaturesContainer') {
                addFeatureCheckboxToCategory('addRoomFeaturesContainer', featureId, featureName, category, 'features[]', null, false);
            }
            
            // Add to all edit room containers
            document.querySelectorAll('[id^="editRoomFeaturesContainer"]').forEach(container => {
                if (container.id !== excludeContainerId) {
                    const roomId = container.id.replace('editRoomFeaturesContainer', '');
                    addFeatureCheckboxToCategory(container.id, featureId, featureName, category, 'editFeatures[]', roomId, false);
                }
            });
        }
        
        // Helper function to escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Function to show toast notification
        function showToast(message, type = 'success') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
            alertDiv.style.cssText = 'z-index: 99999; max-width: 600px; width: calc(100% - 2rem);';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            document.body.appendChild(alertDiv);
            
            // Auto-remove after 3 seconds
            setTimeout(() => {
                alertDiv.remove();
            }, 3000);
        }
    </script>
</body>

</html>