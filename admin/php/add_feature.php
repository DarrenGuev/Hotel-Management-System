<?php
include '../connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $featureName = isset($_POST['featureName']) ? trim($_POST['featureName']) : '';
    $category = isset($_POST['category']) ? trim($_POST['category']) : 'General';
    
    if (empty($featureName)) {
        echo json_encode(['success' => false, 'error' => 'Feature name is required']);
        exit;
    }
    
    // Sanitize input
    $featureName = mysqli_real_escape_string($conn, $featureName);
    $category = mysqli_real_escape_string($conn, $category);
    
    // Check if feature already exists
    $checkQuery = "SELECT featureId, category FROM features WHERE featureName = '$featureName'";
    $checkResult = executeQuery($checkQuery);
    
    if (mysqli_num_rows($checkResult) > 0) {
        $existingFeature = mysqli_fetch_assoc($checkResult);
        echo json_encode([
            'success' => false, 
            'error' => 'Feature already exists',
            'featureId' => $existingFeature['featureId'],
            'featureName' => $featureName,
            'category' => $existingFeature['category']
        ]);
        exit;
    }
    
    // Insert new feature with category
    $insertQuery = "INSERT INTO `features`(`featureName`, `category`) VALUES ('$featureName', '$category')";
    
    if (executeQuery($insertQuery)) {
        $newFeatureId = mysqli_insert_id($conn);
        echo json_encode([
            'success' => true,
            'featureId' => $newFeatureId,
            'featureName' => $featureName,
            'category' => $category,
            'message' => 'Feature added successfully'
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to add feature']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>
