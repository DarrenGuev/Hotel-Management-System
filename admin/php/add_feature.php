<?php
include '../connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $featureName = isset($_POST['featureName']) ? trim($_POST['featureName']) : '';
    
    if (empty($featureName)) {
        echo json_encode(['success' => false, 'error' => 'Feature name is required']);
        exit;
    }
    
    // Sanitize input
    $featureName = mysqli_real_escape_string($conn, $featureName);
    
    // Check if feature already exists
    $checkQuery = "SELECT featureId FROM features WHERE featureName = '$featureName'";
    $checkResult = executeQuery($checkQuery);
    
    if (mysqli_num_rows($checkResult) > 0) {
        $existingFeature = mysqli_fetch_assoc($checkResult);
        echo json_encode([
            'success' => false, 
            'error' => 'Feature already exists',
            'featureId' => $existingFeature['featureId'],
            'featureName' => $featureName
        ]);
        exit;
    }
    
    // Insert new feature
    $insertQuery = "INSERT INTO `features`(`featureName`) VALUES ('$featureName')";
    
    if (executeQuery($insertQuery)) {
        $newFeatureId = mysqli_insert_id($conn);
        echo json_encode([
            'success' => true,
            'featureId' => $newFeatureId,
            'featureName' => $featureName,
            'message' => 'Feature added successfully'
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to add feature']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>
