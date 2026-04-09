<?php
session_start();
include "db.php";

$request_id = $_POST['request_id'];
$status = $_POST['status'];

// ONLY SET mechanic_id when accepting
if($status == 'accepted'){
    $mechanic_id = $_SESSION['user_id'];

    $conn->query("
    UPDATE requests 
    SET status='accepted', mechanic_id='$mechanic_id'
    WHERE id='$request_id'
    ");
}
else{
    // reject OR other updates
    $conn->query("
    UPDATE requests 
    SET status='$status'
    WHERE id='$request_id'
    ");
}
?>