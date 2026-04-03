<?php
session_start();

$phone = $_POST['phone'];

// generate OTP
$otp = rand(1000, 9999);

// store in session
$_SESSION['otp'] = $otp;
$_SESSION['otp_phone'] = $phone;

// demo response
echo "OTP: $otp";
?>