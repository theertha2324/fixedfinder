<?php
session_start();
include "db.php";

$user_id = $_SESSION['user_id'];
$problem = $_POST['problem'];
$location = $_POST['location'];

// insert request
$sql = "INSERT INTO requests (user_id, problem, location)
        VALUES ('$user_id', '$problem', '$location')";

if($conn->query($sql)){
    echo "<script>alert('Request Sent Successfully ✅'); window.location='../home.php';</script>";
} else {
    echo "Error: " . $conn->error;
}
?>