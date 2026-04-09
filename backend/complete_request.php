<?php
include "db.php";

$request_id = $_POST['request_id'];

$conn->query("
UPDATE requests 
SET status='completed'
WHERE id='$request_id'
");

echo "done";
?>