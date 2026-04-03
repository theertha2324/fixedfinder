<?php
session_start();
include "db.php";

// =======================
// GET FORM DATA
// =======================
$name = $_POST['name'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$role = $_POST['type'];
$user_otp = $_POST['otp'];

// mechanic fields (optional)
$garage = $_POST['garage_location'] ?? '';
$spec = $_POST['specialization'] ?? '';
$lat = $_POST['latitude'] ?? '';
$lng = $_POST['longitude'] ?? '';


// =======================
// STEP 1: OTP CHECK
// =======================
if(!isset($_SESSION['otp']) || !isset($_SESSION['otp_phone'])){
    echo "<script>alert('Please generate OTP first'); window.location='../signup.html';</script>";
    exit();
}


// =======================
// STEP 2: VERIFY OTP
// =======================
if((string)$user_otp !== (string)$_SESSION['otp']){
    echo "<script>alert('Wrong OTP'); window.location='../signup.html';</script>";
    exit();
}


// =======================
// STEP 3: PHONE MATCH
// =======================
if($phone != $_SESSION['otp_phone']){
    echo "<script>alert('Phone number mismatch ❌'); window.location='../signup.html';</script>";
    exit();
}


// =======================
// STEP 4: CHECK DUPLICATES
// =======================
$check = "SELECT * FROM users WHERE phone='$phone' OR email='$email'";
$result = $conn->query($check);

if($result->num_rows > 0){
    echo "<script>alert('Email or Phone already exists ❌'); window.location='../signup.html';</script>";
    exit();
}


// =======================
// STEP 5: GENERATE USER KEY
// =======================
function generateUserKey(){
    return "FF" . strtoupper(substr(md5(uniqid()), 0, 8));
}

$user_key = generateUserKey();


// =======================
// STEP 6: INSERT USER
// =======================
$sql = "INSERT INTO users 
(name, phone, email, password, role, user_key, location, garage_location, specialization, latitude, longitude)
VALUES 
('$name','$phone','$email','$password','$role','$user_key','$garage','$garage','$spec','$lat','$lng')";


if($conn->query($sql)){

    // clear OTP session
    unset($_SESSION['otp']);
    unset($_SESSION['otp_phone']);

    echo "<script>
        alert('Signup Successful ✅\\nYour ID: $user_key');
        window.location='../login.html';
    </script>";

} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>