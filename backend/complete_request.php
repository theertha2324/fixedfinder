<?php
session_start();
include "db.php";

$request_id = $_POST['request_id'];

// mark completed
$conn->query("UPDATE requests SET status='completed' WHERE id='$request_id'");

// redirect to rating page
header("Location: ../rating.php?id=".$request_id);
?>