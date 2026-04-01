<?php
session_start();
include 'db.php';

// check login
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo "<script>alert('Unauthorized'); window.location='../login.html';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];   // user OR mechanic
$complaint = $_POST['complaint'];

// validation
if (empty($complaint)) {
    echo "<script>alert('Complaint cannot be empty'); window.history.back();</script>";
    exit();
}

$image_path = "";

// 🔥 Image Upload
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {

    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    $file_type = $_FILES['image']['type'];

    if (in_array($file_type, $allowed_types)) {

        $upload_dir = "../uploads/complaints/";

        // create folder if not exists
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file_name = time() . "_" . $user_id . "." . $file_ext;

        $full_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $full_path)) {
            $image_path = "uploads/complaints/" . $file_name;
        }
    }
}

// 🔥 Insert Complaint (UPDATED STRUCTURE)
$sql = "INSERT INTO complaints (user_id, role, complaint, image)
        VALUES ('$user_id', '$role', '$complaint', '$image_path')";

if ($conn->query($sql)) {

    // redirect based on role
    if($role == "mechanic"){
        header("Location: ../mechanic.php");
    } else {
        header("Location: ../home.php");
    }

} else {
    echo "Error: " . $conn->error;
}
?>