<?php
session_start();
include 'connect.php';

// Check if user is admin
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../frontend/login.php?error=Access denied");
    exit();
}

// Helper function to handle image upload
function handleImageUpload($fileInput, $targetFolder = "assets/")
{
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
                $insertFeatureQuery = "INSERT INTO `roomFeatures`(`roomID`, `featureID`) VALUES ('$newRoomID', '$featureId')";
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
            unlink($imagePath); 
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
            $deleteFeatures = "DELETE FROM `roomFeatures` WHERE `roomID`='$roomID'";
            executeQuery($deleteFeatures);
            foreach ($selectedFeatures as $featureId) {
                $featureId = (int)$featureId;
                $insertFeatureQuery = "INSERT INTO `roomFeatures`(`roomID`, `featureID`) VALUES ('$roomID', '$featureId')";
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

// Add this PHP handler after the existing POST handlers (around line 170, before the $getRooms query)

// Handle adding new room type
if (isset($_POST['add_room_type'])) {
    $newRoomType = mysqli_real_escape_string($conn, trim($_POST['newRoomType']));
    
    if (!empty($newRoomType)) {
        // Check if room type already exists
        $checkQuery = "SELECT roomTypeID FROM roomTypes WHERE roomType = '$newRoomType'";
        $checkResult = executeQuery($checkQuery);
        
        if (mysqli_num_rows($checkResult) > 0) {
            echo '<div class="alert alert-warning alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" 
                role="alert" style="z-index: 99999; max-width: 600px; width: calc(100% - 2rem);">
                Room type "' . htmlspecialchars($newRoomType) . '" already exists!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        } else {
            $insertQuery = "INSERT INTO roomTypes (roomType) VALUES ('$newRoomType')";
            if (executeQuery($insertQuery)) {
                echo '<div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" 
                    role="alert" style="z-index: 99999; max-width: 600px; width: calc(100% - 2rem);">
                    Room type "' . htmlspecialchars($newRoomType) . '" added successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            } else {
                echo '<div class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" 
                    role="alert" style="z-index: 99999; max-width: 600px; width: calc(100% - 2rem);">
                    Error adding room type. Please try again.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            }
        }
    }
}

// Handle deleting room type
if (isset($_POST['delete_room_type'])) {
    $deleteTypeID = (int)$_POST['deleteRoomTypeID'];
    
    // Check if any rooms are using this type
    $checkRoomsQuery = "SELECT COUNT(*) as count FROM rooms WHERE roomTypeId = '$deleteTypeID'";
    $checkRoomsResult = executeQuery($checkRoomsQuery);
    $roomCount = mysqli_fetch_assoc($checkRoomsResult)['count'];
    
    if ($roomCount > 0) {
        echo '<div class="alert alert-warning alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" 
            role="alert" style="z-index: 99999; max-width: 600px; width: calc(100% - 2rem);">
            Cannot delete this room type. ' . $roomCount . ' room(s) are using it.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    } else {
        $deleteTypeQuery = "DELETE FROM roomTypes WHERE roomTypeID = '$deleteTypeID'";
        if (executeQuery($deleteTypeQuery)) {
            echo '<div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" 
                role="alert" style="z-index: 99999; max-width: 600px; width: calc(100% - 2rem);">
                Room type deleted successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        }
    }
}

$getRooms = "SELECT rooms.*, roomTypes.roomType AS roomTypeName FROM rooms INNER JOIN roomTypes ON rooms.roomTypeId = roomTypes.roomTypeID ORDER BY rooms.roomID ASC";
$rooms = executeQuery($getRooms);

$getRoomTypes = "SELECT * FROM roomTypes ORDER BY roomTypeID";
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
mysqli_data_seek($features, 0);

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TravelMates - Rooms Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
</head>

<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>

            <div class="col-12 col-lg-10 p-3 p-lg-4">
                <div class="page-header d-flex justify-content-between align-items-center">
                    <div>
                        <h2>Rooms Management</h2>
                        <p>Manage hotel rooms and their details</p>
                    </div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                        <i class="bi bi-plus-lg me-2"></i>Add Room
                    </button>
                </div>

                <div class="card mb-4">
                    <div class="card-body p-2">
                        <ul class="nav nav-pills" id="roomTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-room-type="All" type="button" role="tab" onclick="filterRooms('All')">
                                    All Rooms
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-room-type="Basic" type="button" role="tab" onclick="filterRooms('Basic')">
                                    Basic
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-room-type="Family" type="button" role="tab" onclick="filterRooms('Family')">
                                    Family
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-room-type="Suite" type="button" role="tab" onclick="filterRooms('Suite')">
                                    Suite
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-room-type="Deluxe" type="button" role="tab" onclick="filterRooms('Deluxe')">
                                    Deluxe
                                </button>
                            </li>
                            <?php 
                            // Dynamically add tabs for any additional room types beyond the default ones
                            mysqli_data_seek($roomTypes, 0);
                            $defaultTypes = ['Basic', 'Family', 'Suite', 'Deluxe'];
                            while ($type = mysqli_fetch_assoc($roomTypes)) {
                                if (!in_array($type['roomType'], $defaultTypes)) { ?>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-room-type="<?php echo htmlspecialchars($type['roomType']); ?>" type="button" role="tab" onclick="filterRooms('<?php echo htmlspecialchars($type['roomType']); ?>')">
                                            <?php echo htmlspecialchars($type['roomType']); ?>
                                        </button>
                                    </li>
                            <?php }
                            }
                            mysqli_data_seek($roomTypes, 0);
                            ?>
                            <li class="nav-item">
                                <button class="nav-link text-success" type="button" data-bs-toggle="modal" data-bs-target="#addRoomTypeModal">
                                    <i class="bi bi-plus-circle me-1"></i>Add Room Type
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">Room Type</th>
                                        <th class="text-center">Room Name</th>
                                        <th class="text-center">Max Occupancy</th>
                                        <th class="text-center">Features</th>
                                        <th class="text-center">Price</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-center">Image</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="roomsTableBody">
                                <?php while ($row = mysqli_fetch_assoc($rooms)) {
                                    $roomFeaturesQuery = "SELECT f.featureName FROM features f INNER JOIN roomFeatures rf ON f.featureId = rf.featureID  WHERE rf.roomID = " . (int)$row['roomID'] . " ORDER BY f.featureId";
                                    $roomFeaturesResult = executeQuery($roomFeaturesQuery);
                                    $roomFeatures = [];
                                    while ($feature = mysqli_fetch_assoc($roomFeaturesResult)) {
                                        $roomFeatures[] = $feature['featureName'];
                                    }
                                    $roomFeatureIdsQuery = "SELECT featureID FROM roomFeatures WHERE roomID = " . (int)$row['roomID'] . " ORDER BY featureID";
                                    $roomFeatureIdsResult = executeQuery($roomFeatureIdsQuery);
                                    $roomFeatureIds = [];
                                    while ($fid = mysqli_fetch_assoc($roomFeatureIdsResult)) {
                                        $roomFeatureIds[] = $fid['featureID'];
                                    }
                                ?>
                                    <tr data-room-type="<?php echo htmlspecialchars($row['roomTypeName'], ENT_QUOTES); ?>">
                                        <td class="text-center"><?php echo $row['roomID'] ?></td>
                                        <td class="text-center"><span class="badge bg-info"><?php echo $row['roomTypeName'] ?></span></td>
                                        <td class="text-center"><?php echo $row['roomName'] ?></td>
                                        <td class="text-center"><?php echo $row['capacity'] ?> guests</td>
                                        <td class="text-center">
                                            <?php if (!empty($roomFeatures)) {
                                                foreach ($roomFeatures as $featureName) { ?>
                                                    <span class="badge bg-secondary me-1 mb-1"><?php echo htmlspecialchars($featureName); ?></span>
                                                <?php }
                                            } else { ?>
                                                <span class="text-muted">No features</span>
                                            <?php } ?>
                                        </td>
                                        <td class="text-center"><strong>₱<?php echo number_format($row['base_price'], 2) ?></strong></td>
                                        <td class="text-center"><?php echo $row['quantity'] ?></td>
                                        <td class="text-center">
                                            <?php if (!empty($row['imagePath'])) { ?>
                                                <img src="assets/<?php echo $row['imagePath']; ?>" class="rounded" style="width:100px; height:60px; object-fit:cover;">
                                            <?php } else { ?>
                                                <span class="text-muted">No image</span>
                                            <?php } ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['roomID']; ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this room?');">
                                                    <input type="hidden" value="<?php echo $row['roomID'] ?>" name="deleteID">
                                                    <button class="btn btn-outline-danger btn-sm" type="submit">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>

                                            <div class="modal fade" id="editModal<?php echo $row['roomID']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $row['roomID']; ?>" aria-hidden="true">
                                                <div class="modal-dialog modal-xl">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h1 class="modal-title fs-5" id="editModalLabel<?php echo $row['roomID']; ?>"><i class="bi bi-pencil-square me-2"></i>Edit Room Details</h1>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form method="POST" enctype="multipart/form-data">
                                                                <input type="hidden" name="roomID" value="<?php echo $row['roomID']; ?>">
                                                                <div class="row align-items-center">
                                                                    <div class="col-12 col-lg-4 mb-3">
                                                                        <label for="editRoomName<?php echo $row['roomID']; ?>" class="form-label">Room Name</label>
                                                                        <input id="editRoomName<?php echo $row['roomID']; ?>" class="form-control" type="text" name="editRoomName" value="<?php echo htmlspecialchars($row['roomName']); ?>" required>
                                                                    </div>
                                                                    <div class="col-6 col-lg-4 mb-3">
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
                                                                    <div class="col-6 col-md-6 col-lg-4 mb-3">
                                                                        <label for="editCapacity<?php echo $row['roomID']; ?>" class="form-label">Guest Capacity</label>
                                                                        <input id="editCapacity<?php echo $row['roomID']; ?>" class="form-control" type="number" name="editCapacity" value="<?php echo $row['capacity']; ?>" required>
                                                                    </div>
                                                                    <div class="col-6 col-md-6 col-lg-3 mb-3">
                                                                        <label for="editQuantity<?php echo $row['roomID']; ?>" class="form-label">Quantity of Rooms</label>
                                                                        <input id="editQuantity<?php echo $row['roomID']; ?>" class="form-control" type="number" name="editQuantity" value="<?php echo $row['quantity']; ?>" required>
                                                                    </div>
                                                                    <div class="col-6 col-md-6 col-lg-3 mb-3">
                                                                        <label for="editBasePrice<?php echo $row['roomID']; ?>" class="form-label">Price (₱)</label>
                                                                        <input id="editBasePrice<?php echo $row['roomID']; ?>" class="form-control" type="number" step="0.01" name="editBasePrice" value="<?php echo $row['base_price']; ?>" required>
                                                                    </div>
                                                                    <div class="col-12 col-lg-6 mb-3">
                                                                        <label for="editRoomImage<?php echo $row['roomID']; ?>" class="form-label">Room Image</label>
                                                                        <input id="editRoomImage<?php echo $row['roomID']; ?>" class="form-control" type="file" name="editRoomImage" accept="image/*">
                                                                        <?php if (!empty($row['imagePath'])) { ?>
                                                                            <div class="mt-2">
                                                                                <small class="text-muted">Current image:</small>
                                                                                <img src="assets/<?php echo htmlspecialchars($row['imagePath']); ?>" class="img-thumbnail ms-2" style="max-width: 100px; max-height: 60px;">
                                                                            </div>
                                                                        <?php } ?>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Room Features</label>
                                                                        <div id="editRoomFeaturesContainer<?php echo $row['roomID']; ?>">
                                                                            <div class="row justify-content-center">
                                                                                <?php foreach ($featuresByCategory as $category => $categoryFeatures) { ?>
                                                                                <div class="col-12 col-md-6 col-lg-4 mb-3">
                                                                                    <h6 class="text-muted border-bottom pb-1"><i class="bi bi-tag-fill me-1"></i><?php echo htmlspecialchars($category); ?></h6>
                                                                                    <div class="row">
                                                                                        <?php foreach ($categoryFeatures as $feature) {
                                                                                            $checked = in_array($feature['featureId'], $roomFeatureIds) ? 'checked' : '';
                                                                                        ?>
                                                                                            <div class="col-12 col-md-6 col-lg-4">
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
                                                                        </div>
                                                                        <div class="row justify-content-center">
                                                                            <div class="col-12 col-lg-6 m-3 border-top pt-3">
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
                                                                    </div>
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
        </div>
    </div>

    <!-- Add Room Modal -->
    <div class="modal fade" id="addRoomModal" tabindex="-1" aria-labelledby="addRoomModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addRoomModalLabel"><i class="bi bi-plus-circle me-2"></i>Add New Room</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                            <div class="modal-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-lg-4 mb-3">
                                            <label for="roomName" class="form-label">Room Name</label>
                                            <input id="roomName" class="form-control" type="text" name="roomName" placeholder="e.g., Deluxe Suite" required>
                                        </div>
                                        <div class="col-6 col-lg-4 mb-3">
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
                                        <div class="col-6 col-md-6 col-lg-4 mb-3">
                                            <label for="capacity" class="form-label">Guest Capacity</label>
                                            <input id="capacity" class="form-control" type="number" name="capacity" placeholder="number of max capacity" required>
                                        </div>
                                        <div class="col-6 col-md-6 col-lg-3 mb-3">
                                            <label for="quantity" class="form-label">Quantity of Rooms</label>
                                            <input id="quantity" class="form-control" type="number" name="quantity" placeholder="number of rooms" required>
                                        </div>
                                        <div class="col-6 col-md-6 col-lg-3 mb-3">
                                            <label for="base_price" class="form-label">Price (₱)</label>
                                            <input id="base_price" class="form-control" type="number" step="0.01" name="base_price" placeholder="e.g., 1500.00" required>
                                        </div>
                                        <div class="col-12 col-lg-6 mb-3">
                                            <label for="roomImage" class="form-label">Room Image</label>
                                            <input id="roomImage" class="form-control" type="file" name="roomImage" accept="image/*">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Room Features</label>
                                            <div id="addRoomFeaturesContainer">
                                                <div class="row justify-content-center">
                                                    <?php foreach ($featuresByCategory as $category => $categoryFeatures) { ?>
                                                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                                                        <h6 class="text-muted border-bottom pb-1"><i class="bi bi-tag-fill me-1"></i><?php echo htmlspecialchars($category); ?></h6>
                                                        <div class="row">
                                                            <?php foreach ($categoryFeatures as $feature) { ?>
                                                                <div class="col-12 col-md-6 col-lg-4">
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
                                            </div>
                                            <div class="row justify-content-center">
                                                <div class="col-12 col-lg-6 m-3 border-top pt-3">
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
                                                <button type="submit" name="add_room" class="btn btn-primary">Save Room</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

    <!-- Add Room Type Modal - Add this before </body> -->
    <div class="modal fade" id="addRoomTypeModal" tabindex="-1" aria-labelledby="addRoomTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addRoomTypeModalLabel">
                        <i class="bi bi-tags me-2"></i>Manage Room Types
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Add New Room Type Form -->
                    <form method="POST" class="mb-4">
                        <label for="newRoomType" class="form-label">Add New Room Type</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="newRoomType" name="newRoomType" 
                                   placeholder="e.g., Presidential, Economy, VIP" required>
                            <button type="submit" name="add_room_type" class="btn btn-success">
                                <i class="bi bi-plus-lg me-1"></i>Add
                            </button>
                        </div>
                        <small class="text-muted">Enter a unique name for the new room type</small>
                    </form>

                    <!-- Existing Room Types List -->
                    <h6 class="border-bottom pb-2 mb-3">Existing Room Types</h6>
                    <div class="list-group">
                        <?php 
                        mysqli_data_seek($roomTypes, 0);
                        while ($type = mysqli_fetch_assoc($roomTypes)) { 
                            // Count rooms using this type
                            $countQuery = "SELECT COUNT(*) as count FROM rooms WHERE roomTypeId = " . (int)$type['roomTypeID'];
                            $countResult = executeQuery($countQuery);
                            $roomCount = mysqli_fetch_assoc($countResult)['count'];
                        ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-tag-fill me-2 text-primary"></i>
                                    <?php echo htmlspecialchars($type['roomType']); ?>
                                    <span class="badge bg-secondary ms-2"><?php echo $roomCount; ?> room(s)</span>
                                </div>
                                <?php if ($roomCount == 0) { ?>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this room type?');">
                                        <input type="hidden" name="deleteRoomTypeID" value="<?php echo $type['roomTypeID']; ?>">
                                        <button type="submit" name="delete_room_type" class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                <?php } else { ?>
                                    <span class="text-muted small" title="Cannot delete - rooms are using this type">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                <?php } ?>
                            </div>
                        <?php } 
                        mysqli_data_seek($roomTypes, 0);
                        ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

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
                        addFeatureCheckboxToCategory(containerId, data.featureId, data.featureName, data.category, checkboxName, roomId, true);
                        addFeatureToAllContainers(data.featureId, data.featureName, data.category, containerId);
                        input.value = '';
                        showToast('Feature "' + data.featureName + '" added to ' + data.category + ' successfully!', 'success');
                    } else if (data.error === 'Feature already exists') {
                        const existingCheckbox = document.querySelector('#' + containerId + ' input[value="' + data.featureId + '"]');
                        if (existingCheckbox) {
                            existingCheckbox.checked = true;
                            showToast('Feature already exists. It has been selected.', 'info');
                        } else {
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

        function addFeatureCheckboxToCategory(containerId, featureId, featureName, category, checkboxName, roomId = null, isChecked = false) {
            const container = document.getElementById(containerId);
            if (!container) return;

            const existingCheckbox = container.querySelector('input[value="' + featureId + '"]');
            if (existingCheckbox) {
                if (isChecked) existingCheckbox.checked = true;
                return;
            }

            // Find existing category section by looking for h6 with matching text
            let categorySection = null;
            const allSections = container.querySelectorAll('.mb-3');
            allSections.forEach(section => {
                const h6 = section.querySelector('h6');
                if (h6 && h6.textContent.trim() === category) {
                    categorySection = section;
                }
            });

            if (!categorySection) {
                // Create new category section only if it doesn't exist
                categorySection = document.createElement('div');
                categorySection.className = 'col-12 col-md-6 col-lg-4 mb-3';
                categorySection.innerHTML = `
                    <h6 class="text-muted border-bottom pb-1"><i class="bi bi-tag-fill me-1"></i>${escapeHtml(category)}</h6>
                    <div class="row category-features"></div>
                `;
                
                // Insert before the "Add Custom Feature" section if it exists
                const customFeatureSection = container.querySelector('.border-top.pt-3')?.closest('.row.justify-content-center') 
                    || container.querySelector('.mt-3.border-top');
                if (customFeatureSection) {
                    customFeatureSection.parentNode.insertBefore(categorySection, customFeatureSection);
                } else {
                    // For add modal, insert into the row container
                    const rowContainer = container.querySelector('.row.justify-content-center');
                    if (rowContainer) {
                        rowContainer.appendChild(categorySection);
                    } else {
                        container.appendChild(categorySection);
                    }
                }
            }

            // Find the features row within the category section
            let featuresRow = categorySection.querySelector('.category-features') || categorySection.querySelector('.row');
            if (!featuresRow) {
                featuresRow = document.createElement('div');
                featuresRow.className = 'row category-features';
                categorySection.appendChild(featuresRow);
            }

            const checkboxId = roomId ? 'editFeature' + roomId + '_' + featureId : 'feature' + featureId;
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

        function addFeatureCheckbox(containerId, featureId, featureName, checkboxName, roomId = null, isChecked = false) {
            addFeatureCheckboxToCategory(containerId, featureId, featureName, 'General', checkboxName, roomId, isChecked);
        }

        function addFeatureToAllContainers(featureId, featureName, category, excludeContainerId) {
            if (excludeContainerId !== 'addRoomFeaturesContainer') {
                addFeatureCheckboxToCategory('addRoomFeaturesContainer', featureId, featureName, category, 'features[]', null, false);
            }

            document.querySelectorAll('[id^="editRoomFeaturesContainer"]').forEach(container => {
                if (container.id !== excludeContainerId) {
                    const roomId = container.id.replace('editRoomFeaturesContainer', '');
                    addFeatureCheckboxToCategory(container.id, featureId, featureName, category, 'editFeatures[]', roomId, false);
                }
            });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function showToast(message, type = 'success') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
            alertDiv.style.cssText = 'z-index: 99999; max-width: 600px; width: calc(100% - 2rem);';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            document.body.appendChild(alertDiv);
            setTimeout(() => {
                alertDiv.remove();
            }, 3000);
        }
    </script>
</body>

</html>