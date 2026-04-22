<?php
session_start();
include "db.php";

// =======================
// GET FORM DATA SAFELY
// =======================
$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$password_raw = $_POST['password'] ?? '';
$role = $_POST['type'] ?? '';
$user_otp = $_POST['otp'] ?? '';

$garage = trim($_POST['garage_location'] ?? '');
$spec = trim($_POST['specialization'] ?? '');
$lat = ($_POST['latitude'] ?? '') === '' ? 0 : $_POST['latitude'];
$lng = ($_POST['longitude'] ?? '') === '' ? 0 : $_POST['longitude'];


// =======================
// VALIDATION (STRONG)
// =======================

// NAME (only letters, no only spaces, min 3 chars)
if(empty($name) || !preg_match("/^[A-Za-z ]+$/", $name) || strlen(str_replace(' ', '', $name)) < 3){
    die("<script>alert('Name must contain only letters and at least 3 characters ❌'); window.history.back();</script>");
}

// PHONE (exact 10 digits)
if(!preg_match("/^[0-9]{10}$/", $phone)){
    die("<script>alert('Invalid Phone Number ❌'); window.history.back();</script>");
}

// EMAIL (strict validation)
if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)){
    die("<script>alert('Invalid Email Format ❌'); window.history.back();</script>");
}

// PASSWORD (min 6 chars)
if(strlen($password_raw) < 6){
    die("<script>alert('Password must be at least 6 characters ❌'); window.history.back();</script>");
}

// ROLE
if(empty($role)){
    die("<script>alert('Please select role ❌'); window.history.back();</script>");
}


// =======================
// OTP CHECK
// =======================
if(!isset($_SESSION['otp']) || !isset($_SESSION['otp_phone'])){
    die("<script>alert('Please generate OTP first ❌'); window.location='../signup.html';</script>");
}

// VERIFY OTP
if((string)$user_otp !== (string)$_SESSION['otp']){
    die("<script>alert('Wrong OTP ❌'); window.location='../signup.html';</script>");
}

// PHONE MATCH
if($phone !== $_SESSION['otp_phone']){
    die("<script>alert('Phone mismatch ❌'); window.location='../signup.html';</script>");
}


// =======================
// MECHANIC LOCATION CHECK
// =======================
if($role === "mechanic"){
    if(empty($lat) || empty($lng)){
        die("<script>alert('Please select location on map ❌'); window.history.back();</script>");
    }
}


// =======================
// CHECK DUPLICATES (SECURE)
// =======================
$stmt = $conn->prepare("SELECT id FROM users WHERE phone=? OR email=?");
$stmt->bind_param("ss", $phone, $email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    die("<script>alert('Email or Phone already exists ❌'); window.location='../signup.html';</script>");
}


// =======================
// GENERATE USER KEY
// =======================
function generateUserKey(){
    return "FF" . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
}

$user_key = generateUserKey();


// =======================
// HASH PASSWORD
// =======================
$password = password_hash($password_raw, PASSWORD_DEFAULT);


// =======================
// XSS PROTECTION
// =======================
$name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
$garage = htmlspecialchars($garage, ENT_QUOTES, 'UTF-8');
$spec = htmlspecialchars($spec, ENT_QUOTES, 'UTF-8');


// =======================
// INSERT USER (SECURE)
// =======================
$stmt = $conn->prepare("
    INSERT INTO users 
    (name, phone, email, password, role, user_key, location, garage_location, specialization, latitude, longitude)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "sssssssssss",
    $name,
    $phone,
    $email,
    $password,
    $role,
    $user_key,
    $garage,   // keeping your logic
    $garage,
    $spec,
    $lat,
    $lng
);

if($stmt->execute()){

    // CLEAR OTP SESSION
    unset($_SESSION['otp']);
    unset($_SESSION['otp_phone']);

    echo "<script>
        alert('Signup Successful ✅\\nYour ID: $user_key');
        window.location='../login.html';
    </script>";

} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>