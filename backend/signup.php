<?php
session_start();
include "db.php";

// get form data
$name = $_POST['name'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$password = $_POST['password'];
$role = $_POST['role'];
$user_otp = $_POST['otp'];

// mechanic fields (may be empty for users)
$garage = $_POST['garage_location'] ?? '';
$spec = $_POST['specialization'] ?? '';
$lat = $_POST['latitude'] ?? '';
$lng = $_POST['longitude'] ?? '';

// 🔥 STEP 1: CHECK OTP GENERATED
if(!isset($_SESSION['otp']) || !isset($_SESSION['otp_phone'])){
    echo "<script>alert('Please generate OTP first'); window.location='../signup.html';</script>";
    exit();
}

// 🔥 STEP 2: VERIFY OTP
if($user_otp != $_SESSION['otp']){
    echo "<script>alert('Wrong OTP ❌'); window.location='../signup.html';</script>";
    exit();
}

// 🔥 STEP 3: CHECK PHONE MATCH
if($phone != $_SESSION['otp_phone']){
    echo "<script>alert('Phone number mismatch ❌'); window.location='../signup.html';</script>";
    exit();
}

// 🔥 STEP 4: INSERT USER INTO DB
$sql = "INSERT INTO users 
(name, phone, email, password, role, location, garage_location, specialization, latitude, longitude)
VALUES 
('$name','$phone','$email','$password','$role','$garage','$garage','$spec','$lat','$lng')";

if($conn->query($sql)){
    
    // clear OTP after success
    unset($_SESSION['otp']);
    unset($_SESSION['otp_phone']);

    echo "<script>alert('Signup Successful ✅'); window.location='../login.html';</script>";
} else {
    echo "Error: " . $conn->error;
}
?>