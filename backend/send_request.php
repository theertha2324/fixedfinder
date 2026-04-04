<?php
session_start();
include "db.php";

$user_id = $_SESSION['user_id'];
$mechanic_id = $_POST['mechanic_id'];
$problem = $_POST['problem'];
$location = $_POST['location'];

// 🔥 GET USER PHONE
$res = $conn->query("SELECT phone FROM users WHERE id='$user_id'");
$user = $res->fetch_assoc();
$user_phone = $user['phone'];

// INSERT
$sql = "INSERT INTO requests (user_id, mechanic_id, problem, location, user_phone)
        VALUES ('$user_id', '$mechanic_id', '$problem', '$location', '$user_phone')";

$conn->query($sql);

header("Location: ../home.php");
?>