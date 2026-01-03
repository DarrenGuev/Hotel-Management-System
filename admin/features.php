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

// Handle adding new category
if (isset($_POST['add_category'])) {
    $newCategory = mysqli_real_escape_string($conn, trim($_POST['newCategory']));
    
    if (!empty($newCategory)) {
        // Check if category already exists in featureCategories table
        $checkQuery = "SELECT categoryID FROM featureCategories WHERE categoryName = '$newCategory'";
        $checkResult = executeQuery($checkQuery);
        
        if (mysqli_num_rows($checkResult) > 0) {
            echo '<div class="alert alert-warning alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" 
                role="alert" style="z-index: 99999; max-width: 600px; width: calc(100% - 2rem);">
                Category "' . htmlspecialchars($newCategory) . '" already exists!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        } else {
            // Insert into featureCategories table
            $insertQuery = "INSERT INTO featureCategories (categoryName) VALUES ('$newCategory')";
            if (executeQuery($insertQuery)) {
                echo '<div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" 
                    role="alert" style="z-index: 99999; max-width: 600px; width: calc(100% - 2rem);">
                    Category "' . htmlspecialchars($newCategory) . '" added successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            } else {
                echo '<div class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" 
                    role="alert" style="z-index: 99999; max-width: 600px; width: calc(100% - 2rem);">
                    Error adding category. Please try again.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            }
        }
    }
}

// Handle deleting category
if (isset($_POST['delete_category'])) {
    $deleteCategoryID = (int)$_POST['deleteCategoryID'];
    
    // Get category name first
    $getCatQuery = "SELECT categoryName FROM featureCategories WHERE categoryID = '$deleteCategoryID'";
    $getCatResult = executeQuery($getCatQuery);
    $catRow = mysqli_fetch_assoc($getCatResult);
    $deleteCategory = $catRow ? $catRow['categoryName'] : '';
    
    // Count features in this category
    $countQuery = "SELECT COUNT(*) as count FROM features WHERE category = '$deleteCategory'";
    $countResult = executeQuery($countQuery);
    $featureCount = mysqli_fetch_assoc($countResult)['count'];
    
    if ($featureCount > 0) {
        echo '<div class="alert alert-warning alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" 
            role="alert" style="z-index: 99999; max-width: 600px; width: calc(100% - 2rem);">
            Cannot delete category "' . htmlspecialchars($deleteCategory) . '". ' . $featureCount . ' feature(s) are using it. Please reassign or delete those features first.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    } else {
        $deleteQuery = "DELETE FROM featureCategories WHERE categoryID = '$deleteCategoryID'";
        if (executeQuery($deleteQuery)) {
            echo '<div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" 
                role="alert" style="z-index: 99999; max-width: 600px; width: calc(100% - 2rem);">
                Category "' . htmlspecialchars($deleteCategory) . '" deleted successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        }
    }
}

// Handle renaming category
if (isset($_POST['rename_category'])) {
    $categoryID = (int)$_POST['categoryID'];
    $newCategoryName = mysqli_real_escape_string($conn, trim($_POST['newCategoryName']));
    
    // Get old category name
    $getOldQuery = "SELECT categoryName FROM featureCategories WHERE categoryID = '$categoryID'";
    $oldResult = executeQuery($getOldQuery);
    $oldRow = mysqli_fetch_assoc($oldResult);
    $oldCategory = $oldRow ? $oldRow['categoryName'] : '';
    
    if (!empty($newCategoryName) && $categoryID > 0) {
        // Check if new name already exists (excluding current category)
        $checkQuery = "SELECT categoryID FROM featureCategories WHERE categoryName = '$newCategoryName' AND categoryID != '$categoryID'";
        $checkResult = executeQuery($checkQuery);
        
        if (mysqli_num_rows($checkResult) > 0) {
            echo '<div class="alert alert-warning alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" 
                role="alert" style="z-index: 99999; max-width: 600px; width: calc(100% - 2rem);">
                Category "' . htmlspecialchars($newCategoryName) . '" already exists!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        } else {
            // Update featureCategories table
            $updateCatQuery = "UPDATE featureCategories SET categoryName = '$newCategoryName' WHERE categoryID = '$categoryID'";
            // Also update all features with the old category name
            $updateFeaturesQuery = "UPDATE features SET category = '$newCategoryName' WHERE category = '$oldCategory'";
            
            if (executeQuery($updateCatQuery) && executeQuery($updateFeaturesQuery)) {
                echo '<div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" 
                    role="alert" style="z-index: 99999; max-width: 600px; width: calc(100% - 2rem);">
                    Category renamed from "' . htmlspecialchars($oldCategory) . '" to "' . htmlspecialchars($newCategoryName) . '" successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            } else {
                echo '<div class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" 
                    role="alert" style="z-index: 99999; max-width: 600px; width: calc(100% - 2rem);">
                    Error renaming category. Please try again.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            }
        }
    }
}

$getFeatures = "SELECT * FROM features ORDER BY category, featureId";
$features = executeQuery($getFeatures);

// Get all categories from featureCategories table
$getCategoriesQuery = "SELECT * FROM featureCategories ORDER BY categoryName";
$categoriesResult = executeQuery($getCategoriesQuery);
$categoryList = [];
while ($cat = mysqli_fetch_assoc($categoriesResult)) {
    $categoryList[] = $cat;
}

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

                <!-- Category Filter Tabs -->
                <div class="card mb-4">
                    <div class="card-body p-2">
                        <ul class="nav nav-pills" id="categoryTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-category="All" type="button" role="tab" onclick="filterFeatures('All')">
                                    All Features
                                </button>
                            </li>
                            <?php 
                            // Get categories with feature counts
                            $catCountQuery = "SELECT category, COUNT(*) as count FROM features GROUP BY category ORDER BY category";
                            $catCountResult = executeQuery($catCountQuery);
                            $categoriesWithCount = [];
                            while ($catRow = mysqli_fetch_assoc($catCountResult)) {
                                $categoriesWithCount[$catRow['category']] = $catRow['count'];
                            }
                            
                            foreach ($categoryList as $cat) { 
                                $catName = $cat['categoryName'];
                                $count = isset($categoriesWithCount[$catName]) ? $categoriesWithCount[$catName] : 0;
                            ?>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-category="<?php echo htmlspecialchars($catName); ?>" type="button" role="tab" onclick="filterFeatures('<?php echo htmlspecialchars($catName); ?>')">
                                        <?php echo htmlspecialchars($catName); ?>
                                        <span class="badge bg-secondary ms-1"><?php echo $count; ?></span>
                                    </button>
                                </li>
                            <?php } ?>
                            <li class="nav-item">
                                <button class="nav-link text-success" type="button" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                    <i class="bi bi-plus-circle me-1"></i>Add Category
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link text-danger" type="button" data-bs-toggle="modal" data-bs-target="#manageCategoriesModal">
                                    <i class="bi bi-pencil-square me-1"></i>Manage Categories
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Table -->
                <div class="card">
                    <div class="card-body">
                        <!-- Pagination Info & Controls -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="text-muted" id="paginationInfo">
                                Showing <span id="showingStart">1</span>-<span id="showingEnd">7</span> of <span id="totalFeatures">0</span> features
                            </div>
                            <nav aria-label="Feature pagination">
                                <ul class="pagination pagination-sm mb-0" id="paginationControls">
                                    <!-- Pagination buttons will be generated by JavaScript -->
                                </ul>
                            </nav>
                        </div>
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
                                <tbody id="featuresTableBody">
                                    <?php while ($row = mysqli_fetch_assoc($features)) { ?>
                                        <tr data-category="<?php echo htmlspecialchars($row['category'] ?? 'General', ENT_QUOTES); ?>">
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
                                                                                <option value="<?php echo htmlspecialchars($cat['categoryName']); ?>" <?php echo ($row['category'] == $cat['categoryName']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['categoryName']); ?></option>
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
                                    <!-- Empty State Row (hidden by default, shown when no features in category) -->
                                    <tr id="noFeaturesRow" style="display: none;">
                                        <td colspan="4" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                                <h5>No Features Found</h5>
                                                <p class="mb-0" id="noFeaturesMessage">There are no features in this category yet.</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- Bottom Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted" id="paginationInfoBottom">
                                Showing <span id="showingStartBottom">1</span>-<span id="showingEndBottom">7</span> of <span id="totalFeaturesBottom">0</span> features
                            </div>
                            <nav aria-label="Feature pagination bottom">
                                <ul class="pagination pagination-sm mb-0" id="paginationControlsBottom">
                                    <!-- Pagination buttons will be generated by JavaScript -->
                                </ul>
                            </nav>
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
                                    <option value="<?php echo htmlspecialchars($cat['categoryName']); ?>"><?php echo htmlspecialchars($cat['categoryName']); ?></option>
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

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addCategoryModalLabel">
                        <i class="bi bi-tags me-2"></i>Add New Category
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="newCategory" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="newCategory" name="newCategory" 
                                   placeholder="e.g., Kitchen, Outdoor, Safety" required>
                            <small class="text-muted">Enter a unique name for the new category</small>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="add_category" class="btn btn-success">
                                <i class="bi bi-plus-lg me-1"></i>Add Category
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Manage Categories Modal -->
    <div class="modal fade" id="manageCategoriesModal" tabindex="-1" aria-labelledby="manageCategoriesModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h1 class="modal-title fs-5" id="manageCategoriesModalLabel">
                        <i class="bi bi-pencil-square me-2"></i>Manage Categories
                    </h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">Edit or delete feature categories. Categories with features cannot be deleted.</p>
                    
                    <div class="list-group">
                        <?php 
                        // Fetch all categories with feature counts
                        $catModalQuery = "SELECT fc.categoryID, fc.categoryName, 
                                          (SELECT COUNT(*) FROM features WHERE category = fc.categoryName) as count 
                                          FROM featureCategories fc ORDER BY fc.categoryName";
                        $catModalResult = executeQuery($catModalQuery);
                        while ($catRow = mysqli_fetch_assoc($catModalResult)) { 
                            $catID = $catRow['categoryID'];
                            $catName = $catRow['categoryName'];
                            $catCount = $catRow['count'];
                        ?>
                            <div class="list-group-item" id="categoryItem<?php echo $catID; ?>">
                                <!-- Display Mode -->
                                <div class="d-flex justify-content-between align-items-center" id="displayMode<?php echo $catID; ?>">
                                    <div>
                                        <i class="bi bi-tag-fill me-2 text-primary"></i>
                                        <span id="catName<?php echo $catID; ?>"><?php echo htmlspecialchars($catName); ?></span>
                                        <span class="badge <?php echo $catCount > 0 ? 'bg-warning text-dark' : 'bg-success'; ?> ms-2">
                                            <?php echo $catCount; ?> feature(s)
                                        </span>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" 
                                                onclick="enableCategoryEditMode('<?php echo $catID; ?>', '<?php echo htmlspecialchars($catName, ENT_QUOTES); ?>')" 
                                                title="Edit Name">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <?php if ($catCount == 0) { ?>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete the category: <?php echo htmlspecialchars($catName, ENT_QUOTES); ?>?');">
                                                <input type="hidden" name="deleteCategoryID" value="<?php echo $catID; ?>">
                                                <button type="submit" name="delete_category" class="btn btn-danger" title="Delete">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        <?php } else { ?>
                                            <button type="button" class="btn btn-outline-secondary" disabled title="Cannot delete - has <?php echo $catCount; ?> feature(s)">
                                                <i class="bi bi-lock"></i> Protected
                                            </button>
                                        <?php } ?>
                                    </div>
                                </div>
                                <!-- Edit Mode (hidden by default) -->
                                <div class="d-none" id="editMode<?php echo $catID; ?>">
                                    <form method="POST" class="d-flex align-items-center gap-2">
                                        <input type="hidden" name="categoryID" value="<?php echo $catID; ?>">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                            <input type="text" class="form-control" name="newCategoryName" 
                                                   id="editInput<?php echo $catID; ?>" 
                                                   value="<?php echo htmlspecialchars($catName); ?>" required>
                                        </div>
                                        <button type="submit" name="rename_category" class="btn btn-success btn-sm" title="Save Changes">
                                            <i class="bi bi-check-lg"></i> Save
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" 
                                                onclick="cancelCategoryEditMode('<?php echo $catID; ?>')" title="Cancel">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    
                    <div class="alert alert-warning small mt-3 mb-0">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        <strong>Note:</strong> Categories with existing features are protected. Reassign or delete those features first to delete a category.
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
        // Pagination and filter variables
        const featuresPerPage = 7;
        let currentPage = 1;
        let currentFilter = 'All';

        // Category edit mode functions
        function enableCategoryEditMode(catId, currentName) {
            document.getElementById('displayMode' + catId).classList.add('d-none');
            document.getElementById('editMode' + catId).classList.remove('d-none');
            const input = document.getElementById('editInput' + catId);
            input.value = currentName;
            input.focus();
            input.select();
        }
        
        function cancelCategoryEditMode(catId) {
            document.getElementById('displayMode' + catId).classList.remove('d-none');
            document.getElementById('editMode' + catId).classList.add('d-none');
        }

        function filterFeatures(category) {
            currentFilter = category;
            currentPage = 1; // Reset to first page when filter changes
            
            // Update active tab
            document.querySelectorAll('#categoryTabs .nav-link').forEach(tab => {
                tab.classList.remove('active');
            });
            const activeTab = document.querySelector(`#categoryTabs [data-category="${category}"]`);
            if (activeTab) {
                activeTab.classList.add('active');
            }

            applyPagination();
        }

        function getFilteredRows() {
            const tableRows = document.querySelectorAll('#featuresTableBody tr:not(#noFeaturesRow)');
            const filtered = [];
            tableRows.forEach(row => {
                if (currentFilter === 'All' || row.dataset.category === currentFilter) {
                    filtered.push(row);
                }
            });
            return filtered;
        }

        function applyPagination() {
            const filteredRows = getFilteredRows();
            const totalFeatures = filteredRows.length;
            const totalPages = Math.ceil(totalFeatures / featuresPerPage);
            
            // Ensure current page is valid
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;
            
            const startIndex = (currentPage - 1) * featuresPerPage;
            const endIndex = Math.min(startIndex + featuresPerPage, totalFeatures);
            
            // Hide all rows first
            document.querySelectorAll('#featuresTableBody tr').forEach(row => {
                row.style.display = 'none';
            });
            
            // Show only rows for current page
            filteredRows.forEach((row, index) => {
                if (index >= startIndex && index < endIndex) {
                    row.style.display = '';
                }
            });
            
            // Show/hide empty state row
            const noFeaturesRow = document.getElementById('noFeaturesRow');
            const noFeaturesMessage = document.getElementById('noFeaturesMessage');
            if (totalFeatures === 0) {
                noFeaturesRow.style.display = '';
                if (currentFilter === 'All') {
                    noFeaturesMessage.textContent = 'There are no features available. Add a new feature to get started.';
                } else {
                    noFeaturesMessage.textContent = `There are no features in the "${currentFilter}" category yet.`;
                }
            } else {
                noFeaturesRow.style.display = 'none';
            }
            
            // Update pagination info (top and bottom)
            updatePaginationInfo(totalFeatures > 0 ? startIndex + 1 : 0, endIndex, totalFeatures);
            
            // Generate pagination controls
            generatePaginationControls(totalPages);
        }

        function updatePaginationInfo(start, end, total) {
            // Top pagination info
            document.getElementById('showingStart').textContent = start;
            document.getElementById('showingEnd').textContent = end;
            document.getElementById('totalFeatures').textContent = total;
            
            // Bottom pagination info
            document.getElementById('showingStartBottom').textContent = start;
            document.getElementById('showingEndBottom').textContent = end;
            document.getElementById('totalFeaturesBottom').textContent = total;
        }

        function generatePaginationControls(totalPages) {
            const paginationHTML = generatePaginationHTML(totalPages);
            document.getElementById('paginationControls').innerHTML = paginationHTML;
            document.getElementById('paginationControlsBottom').innerHTML = paginationHTML;
        }

        function generatePaginationHTML(totalPages) {
            if (totalPages <= 1) {
                return '';
            }
            
            let html = '';
            
            // Previous button
            html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="goToPage(${currentPage - 1}); return false;" aria-label="Previous">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>`;
            
            // Page numbers
            const maxVisiblePages = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
            
            if (endPage - startPage < maxVisiblePages - 1) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }
            
            // First page and ellipsis
            if (startPage > 1) {
                html += `<li class="page-item">
                    <a class="page-link" href="#" onclick="goToPage(1); return false;">1</a>
                </li>`;
                if (startPage > 2) {
                    html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                }
            }
            
            // Page numbers
            for (let i = startPage; i <= endPage; i++) {
                html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="goToPage(${i}); return false;">${i}</a>
                </li>`;
            }
            
            // Last page and ellipsis
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                }
                html += `<li class="page-item">
                    <a class="page-link" href="#" onclick="goToPage(${totalPages}); return false;">${totalPages}</a>
                </li>`;
            }
            
            // Next button
            html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="goToPage(${currentPage + 1}); return false;" aria-label="Next">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>`;
            
            return html;
        }

        function goToPage(page) {
            const filteredRows = getFilteredRows();
            const totalPages = Math.ceil(filteredRows.length / featuresPerPage);
            
            if (page < 1 || page > totalPages) return;
            
            currentPage = page;
            applyPagination();
        }

        // Initialize pagination on page load
        document.addEventListener('DOMContentLoaded', function() {
            filterFeatures('All');
        });
    </script>
</body>

</html>
