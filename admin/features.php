<?php
include 'connect.php';

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

// Get distinct categories for dropdown
$getCategories = "SELECT DISTINCT category FROM features ORDER BY category";
$categories = executeQuery($getCategories);
$categoryList = ['Beds', 'Rooms', 'Bathroom', 'Amenities', 'Entertainment', 'General']; // Predefined categories

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Features</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="HOTEL-MANAGEMENT-SYSTEM/css/style.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container p-5 mt-5">
        <div class="row">
            <div class="col-12 d-flex align-items-center justify-content-between mb-3">
                <h4 class="m-0">Room Features</h4>
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addFeatureModal">
                    Add Feature
                </button>
            </div>

            <div class="col-12">
                <table class="table table-hover table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Category</th>
                            <th scope="col">Feature Name</th>
                            <th scope="col" style="width: 180px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($features)) { ?>
                            <tr>
                                <td><?php echo (int)$row['featureId']; ?></td>
                                <td><span class="badge bg-info"><?php echo htmlspecialchars($row['category'] ?? 'General'); ?></span></td>
                                <td><?php echo htmlspecialchars($row['featureName']); ?></td>
                                <td class="text-center">
                                    <form method="POST" style="display:inline-block;">
                                        <input type="hidden" name="deleteFeatureId" value="<?php echo (int)$row['featureId']; ?>">
                                        <button class="btn btn-outline-danger btn-sm" type="submit">Delete</button>
                                    </form>

                                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editFeatureModal<?php echo (int)$row['featureId']; ?>">
                                        Edit
                                    </button>

                                    <div class="modal fade" id="editFeatureModal<?php echo (int)$row['featureId']; ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5">Edit Feature</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
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

    <div class="modal fade" id="addFeatureModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Add Feature</h1>
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
                            <button type="submit" name="add_feature" class="btn btn-warning">Save Feature</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>
