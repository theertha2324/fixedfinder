<?php
session_start();
include "db.php";

$user_id = $_SESSION['user_id'];

$mechanic_id = $_POST['mechanic_id']; // ❌ don't use this now
$location = $_POST['location'];
$problem = $_POST['problem'];

// ✅ INSERT WITHOUT mechanic_id
$conn->query("
INSERT INTO requests (user_id, location, problem, status)
VALUES ('$user_id', '$location', '$problem', 'pending')
");

echo "sent";
?>