<?php
session_start();
include 'db.php';

$email = $_POST['email'];
$password = $_POST['password'];
$role = $_POST['role'];   // ✅ correct

// allow login using email OR phone
$sql = "SELECT * FROM users 
        WHERE (email='$email' OR phone='$email') 
        AND password='$password'
        AND role='$role'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {

    $user = $result->fetch_assoc();

    // store session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['phone'] = $user['phone'];
    $_SESSION['location'] = $user['location'];

    // redirect
    if ($role == 'user') {
        header("Location: ../home.php");
    } 
    else if ($role == 'mechanic') {
        header("Location: ../mechanic.php");
    } 
    else if ($role == 'admin') {
        header("Location: ../admin.php");
    }

} else {
    echo "<script>alert('Invalid Login'); window.location='../login.html';</script>";
}
?>