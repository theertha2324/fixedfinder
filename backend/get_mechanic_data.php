<?php
session_start();
include "db.php";

$mid = $_SESSION['user_id'];

// ================= PENDING =================
$pending = [];
$res1 = $conn->query("
    SELECT * FROM requests 
    WHERE mechanic_id IS NULL 
    AND status='pending'
");

while($row = $res1->fetch_assoc()){
    $pending[] = $row;
}

// ================= ACCEPTED =================
$accepted = [];
$res2 = $conn->query("
    SELECT r.*, u.phone as user_phone 
    FROM requests r
    JOIN users u ON r.user_id = u.id
    WHERE r.mechanic_id='$mid'
    AND r.status='accepted'
");

while($row = $res2->fetch_assoc()){
    $accepted[] = $row;
}

// ================= HISTORY (🔥 FIX) =================
$history = [];
$res3 = $conn->query("
    SELECT r.*, u.user_key 
    FROM requests r
    JOIN users u ON r.user_id = u.id
    WHERE r.mechanic_id='$mid'
    AND r.status='completed'
");

while($row = $res3->fetch_assoc()){
    $history[] = $row;
}

// ================= OUTPUT =================
echo json_encode([
    "pending" => $pending,
    "accepted" => $accepted,
    "history" => $history
]);
?>