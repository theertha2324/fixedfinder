<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'mechanic') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$mechanic_id = $_SESSION['user_id'];
$complaint = $_POST['complaint'];

if (empty($complaint)) {
    echo json_encode(['success' => false, 'message' => 'Complaint cannot be empty']);
    exit();
}

$image_path = null;

// Handle image upload
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    $file_type = $_FILES['image']['type'];
    
    if (in_array($file_type, $allowed_types)) {
        $upload_dir = '../uploads/complaints/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file_name = time() . '_' . $mechanic_id . '.' . $file_extension;
        $upload_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
            $image_path = 'uploads/complaints/' . $file_name;
        }
    }
}

$insert_query = "INSERT INTO complaints (mechanic_id, complaint, image_path, status) VALUES (?, ?, ?, 'pending')";
$insert_stmt = $conn->prepare($insert_query);
$insert_stmt->bind_param("iss", $mechanic_id, $complaint, $image_path);

if ($insert_stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Complaint submitted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to submit complaint']);
}
?>