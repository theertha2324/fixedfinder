<?php
session_start();
include "db.php";

$request_id = $_POST['request_id'];
$status = $_POST['status'];
$mechanic_id = $_SESSION['user_id'];

// update request
$sql = "UPDATE requests 
        SET status='$status', mechanic_id='$mechanic_id'
        WHERE id='$request_id'";

if($conn->query($sql)){
    header("Location: ../mechanic.php");
} else {
    echo "Error: " . $conn->error;
}
?>