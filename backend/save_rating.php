<?php
include "db.php";

$id = $_POST['request_id'];
$rating = $_POST['rating'];
$feedback = $_POST['feedback'];

$conn->query("UPDATE requests 
SET rating='$rating', feedback='$feedback' 
WHERE id='$id'");

header("Location: ../home.php");
?>