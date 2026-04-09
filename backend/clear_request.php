<?php
include "db.php";

$request_id = $_POST['request_id'] ?? 0;

// 🔥 OPTION 1: DELETE REQUEST
$conn->query("DELETE FROM requests WHERE id='$request_id'");

// 🔥 OPTION 2 (SAFE): mark as cleared instead
// $conn->query("UPDATE requests SET status='cleared' WHERE id='$request_id'");

echo "done";
?>