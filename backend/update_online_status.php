<?php
session_start();
include "db.php";

$mechanic_id = $_SESSION['user_id'];
$status = $_POST['status'];

$conn->query("UPDATE users SET status='$status' WHERE id='$mechanic_id'");
?>