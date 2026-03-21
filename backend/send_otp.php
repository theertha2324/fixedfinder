<?php
session_start();

$phone = $_POST['phone'];

// generate OTP
$otp = rand(1000, 9999);

// store in session (NOT database)
$_SESSION['otp'] = $otp;
$_SESSION['otp_phone'] = $phone;

// demo display
echo "Your OTP is: " . $otp;
?>