<?php
session_start();
include "db.php";

$mid = $_SESSION['user_id'] ?? 0;

// 🔥 PENDING (show all or nearby — keep simple)
$pending = $conn->query("
SELECT * FROM requests 
WHERE status='pending'
ORDER BY id DESC
");

// 🔥 ACCEPTED
$accepted = $conn->query("
SELECT * FROM requests 
WHERE mechanic_id='$mid' AND status='accepted'
ORDER BY id DESC
");

// 🔥 WORK HISTORY (VERY IMPORTANT FIX)
$history = $conn->query("
SELECT r.*, 
       COALESCE(u.user_key, 'Unknown') AS user_key
FROM requests r
LEFT JOIN users u ON r.user_id = u.id
WHERE r.mechanic_id='$mid' 
AND r.status='completed'
ORDER BY r.id DESC
");

// ✅ OUTPUT JSON
echo json_encode([
    "pending" => $pending->fetch_all(MYSQLI_ASSOC),
    "accepted" => $accepted->fetch_all(MYSQLI_ASSOC),
    "history" => $history->fetch_all(MYSQLI_ASSOC)
]);
?>