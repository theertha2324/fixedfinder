<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    echo "Session expired";
    exit();
}

$request_id = $_POST['request_id'];
$rating = $_POST['rating'];
$feedback = $_POST['feedback'];

// ✅ SAVE DATA
$conn->query("
UPDATE requests 
SET rating='$rating', feedback='$feedback'
WHERE id='$request_id'
");

// ✅ RETURN RESPONSE (NO REDIRECT)
echo "success";
?>