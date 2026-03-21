<?php
session_start();
include 'db.php';

$mechanic_id = $_SESSION['user_id'];
$status = $_POST['status'];

$sql = "UPDATE users SET status='$status' WHERE id='$mechanic_id'";

if($conn->query($sql)){
    header("Location: ../mechanic_home.php");
}
?>