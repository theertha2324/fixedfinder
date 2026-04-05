<?php
session_start();
include "db.php";

// 🔐 CHECK LOGIN
if(!isset($_SESSION['user_id'])){
    header("Location: ../login.html");
    exit();
}

// 📥 GET DATA
$request_id = $_POST['request_id'] ?? '';
$rating = $_POST['rating'] ?? '';
$feedback = $_POST['feedback'] ?? '';

// ✅ VALIDATION
if(empty($request_id) || empty($rating)){
    echo "Invalid input";
    exit();
}

// ✅ UPDATE REQUEST TABLE
$conn->query("UPDATE requests 
              SET rating='$rating', feedback='$feedback' 
              WHERE id='$request_id'");

// 🔥 GET MECHANIC ID
$res = $conn->query("SELECT mechanic_id FROM requests WHERE id='$request_id'");
$row = $res->fetch_assoc();

$mechanic_id = $row['mechanic_id'];

// 🔥 UPDATE MECHANIC RATING
$res2 = $conn->query("SELECT rating, total_reviews FROM users WHERE id='$mechanic_id'");
$data = $res2->fetch_assoc();

$current_rating = $data['rating'] ?? 0;
$total_reviews = $data['total_reviews'] ?? 0;

// 📊 CALCULATE NEW AVERAGE
$new_total = $total_reviews + 1;
$new_rating = (($current_rating * $total_reviews) + $rating) / $new_total;

// ✅ UPDATE USERS TABLE
$conn->query("UPDATE users 
              SET rating='$new_rating', total_reviews='$new_total' 
              WHERE id='$mechanic_id'");

// 🔁 REDIRECT
header("Location: ../home.php");
exit();
?>