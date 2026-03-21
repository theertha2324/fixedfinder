<?php
include 'db.php';

$request_id = $_POST['request_id'];
$action = $_POST['action'];

if($action == 'accept'){
    $status = 'accepted';
} else {
    $status = 'declined';
}

$sql = "UPDATE requests SET status='$status' WHERE id='$request_id'";

if($conn->query($sql)){
    header("Location: ../mechanic_home.php");
}
?>