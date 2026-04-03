<?php
session_start();
include 'db.php';

// =======================
// GET FORM DATA
// =======================
$email = $_POST['email'];     // email OR phone
$password = $_POST['password'];
$role = $_POST['type'];


// =======================
// VALIDATION
// =======================
if(empty($email) || empty($password) || empty($role)){
    echo "<script>alert('Please fill all fields ⚠️'); window.location='../login.html';</script>";
    exit();
}


// =======================
// CHECK USER
// =======================
$sql = "SELECT * FROM users 
        WHERE (email='$email' OR phone='$email') 
        AND role='$role'";

$result = $conn->query($sql);


// =======================
// LOGIN CHECK
// =======================
if ($result->num_rows > 0) {

    $user = $result->fetch_assoc();

    // 🔐 PASSWORD VERIFY (IMPORTANT)
    if(password_verify($password, $user['password'])){

        // =======================
        // SET SESSION
        // =======================
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_key'] = $user['user_key']; // 🔥 added
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['phone'] = $user['phone'];
        $_SESSION['location'] = $user['location'];

        // =======================
        // REDIRECT BASED ON ROLE
        // =======================
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
        echo "<script>alert('Wrong Password ❌'); window.location='../login.html';</script>";
    }

} else {
    echo "<script>alert('User not found ❌'); window.location='../login.html';</script>";
}

$conn->close();
?>