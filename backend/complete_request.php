<?php
include "db.php";

$request_id = $_POST['request_id'];

$conn->query("UPDATE requests 
              SET status='completed', completed_at=NOW() 
              WHERE id='$request_id'");

header("Location: ../home.php");
?>