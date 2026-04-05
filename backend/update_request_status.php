<?php
session_start();
include "db.php";

$request_id = $_POST['request_id'];
$status = $_POST['status'];
$mechanic_id = $_SESSION['user_id'];

$conn->query("UPDATE requests 
              SET status='$status', mechanic_id='$mechanic_id' 
              WHERE id='$request_id'");

header("Location: ../mechanic.php");
?>