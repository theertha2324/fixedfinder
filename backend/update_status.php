<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    echo "Not logged in";
    exit();
}

$user_id = $_SESSION['user_id'];

// ==========================
// CASE 1: MECHANIC ONLINE/OFFLINE
// ==========================
if(isset($_POST['status']) && !isset($_POST['request_id'])){

    $status = $_POST['status'];

    $conn->query("UPDATE users SET status='$status' WHERE id='$user_id'");

    echo "Status updated to " . $status;
    exit();
}

// ==========================
// CASE 2: REQUEST ACCEPT/REJECT
// ==========================
if(isset($_POST['request_id']) && isset($_POST['status'])){

    $request_id = $_POST['request_id'];
    $status = $_POST['status'];

    $sql = "UPDATE requests 
            SET status='$status', mechanic_id='$user_id'
            WHERE id='$request_id'";

    if($conn->query($sql)){
        header("Location: ../mechanic.php");
    } else {
        echo "Error: " . $conn->error;
    }

    exit();
}

// ==========================
// INVALID REQUEST
// ==========================
echo "Invalid request";
?>