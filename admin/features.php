<?php
session_start();
include 'connect.php';
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../frontend/login.php?error=Access denied");
    exit();
}

if (isset($_POST['add_feature'])) {
    $featureName = mysqli_real_escape_string($conn, $_POST['featureName']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);

    if (!empty($featureName) && !empty($category)) {
        $insertFeatureQuery = "INSERT INTO `features`(`featureName`, `category`) VALUES ('$featureName', '$category')";
        if (executeQuery($insertFeatureQuery)) {
            echo '<div class="alert alert-success position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 9999;">Feature Added!</div>';
        }
    }
}

if (isset($_POST['deleteFeatureId'])) {
    $deleteFeatureId = (int)$_POST['deleteFeatureId'];
    $deleteFeatureQuery = "DELETE FROM `features` WHERE `featureId` = '$deleteFeatureId'";
    executeQuery($deleteFeatureQuery);
}

if (isset($_POST['update_feature'])) {
    $featureId = (int)$_POST['featureId'];
    $featureName = mysqli_real_escape_string($conn, $_POST['editFeatureName']);
    $category = mysqli_real_escape_string($conn, $_POST['editCategory']);

    if ($featureId && !empty($featureName) && !empty($category)) {
        $updateFeatureQuery = "UPDATE `features` SET `featureName`='$featureName', `category`='$category' WHERE `featureId`='$featureId'";
        if (executeQuery($updateFeatureQuery)) {
            echo '<div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3"
                role="alert" style="z-index: 99999; max-width: 600px; width: calc(100% - 2rem);">
                Feature updated successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        } else {
            echo '<div class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3"
                role="alert" style="z-index: 99999; max-width: 600px; width: calc(100% - 2rem);">
                Error updating feature.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        }
    }
}

$getFeatures = "SELECT * FROM features ORDER BY category, featureId";
$features = executeQuery($getFeatures);
$getCategories = "SELECT DISTINCT category FROM features ORDER BY category";
$categories = executeQuery($getCategories);
$categoryList = ['Beds', 'Rooms', 'Bathroom', 'Amenities', 'Entertainment', 'General']; // Predefined categories

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TravelMates - Features Management</title>
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
                        <h2>Room Features</h2>
                        <p>Manage room features and amenities</p>
                    </div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFeatureModal">
                        <i class="bi bi-plus-lg me-2"></i>Add Feature
                    </button>
                </div>

                <!-- Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">Category</th>
                                        <th class="text-center">Feature Name</th>
                                        <th class="text-center" style="width: 180px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($features)) { ?>
                                        <tr>
                                            <td class="text-center"><?php echo (int)$row['featureId']; ?></td>
                                            <td class="text-center"><span class="badge bg-info"><?php echo htmlspecialchars($row['category'] ?? 'General'); ?></span></td>
                                            <td class="text-center"><?php echo htmlspecialchars($row['featureName']); ?></td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editFeatureModal<?php echo (int)$row['featureId']; ?>">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this feature?');">
                                                        <input type="hidden" name="deleteFeatureId" value="<?php echo (int)$row['featureId']; ?>">
                                                        <button class="btn btn-outline-danger btn-sm" type="submit">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>

                                                <!-- Edit Feature Modal -->
                                                <div class="modal fade" id="editFeatureModal<?php echo (int)$row['featureId']; ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h1 class="modal-title fs-5"><i class="bi bi-pencil me-2"></i>Edit Feature</h1>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body text-start">
                                                                <form method="POST">
                                                                    <input type="hidden" name="featureId" value="<?php echo (int)$row['featureId']; ?>">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Category</label>
                                                                        <select class="form-select" name="editCategory" required>
                                                                            <?php foreach ($categoryList as $cat) { ?>
                                                                                <option value="<?php echo $cat; ?>" <?php echo ($row['category'] == $cat) ? 'selected' : ''; ?>><?php echo $cat; ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Feature Name</label>
                                                                        <input class="form-control" type="text" name="editFeatureName" value="<?php echo htmlspecialchars($row['featureName']); ?>" required>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                        <button type="submit" name="update_feature" class="btn btn-primary">Save Changes</button>
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
    </div>

    <!-- Add Feature Modal -->
    <div class="modal fade" id="addFeatureModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5"><i class="bi bi-plus-circle me-2"></i>Add Feature</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category" required>
                                <option value="" selected disabled>-- Select Category --</option>
                                <?php foreach ($categoryList as $cat) { ?>
                                    <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Feature Name</label>
                            <input class="form-control" type="text" name="featureName" placeholder="e.g., Free Wi-Fi" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="add_feature" class="btn btn-primary">Save Feature</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
