<?php
include 'db.php';

$user = $_POST['user'] ?? '';
$password = $_POST['password'] ?? '';

// ✅ VALIDATION FIRST
if(empty($user) || empty($password)){
    echo "Missing data!";
    exit();
}

// ✅ HASH AFTER VALIDATION
$newpass = password_hash($password, PASSWORD_DEFAULT);

// ✅ PREPARED STATEMENT
$stmt = $conn->prepare("UPDATE users SET password=? WHERE email=? OR phone=?");
$stmt->bind_param("sss", $newpass, $user, $user);

if ($stmt->execute()) {

    if ($stmt->affected_rows > 0){
        echo "Password Updated Successfully ✅";
    } else {
        echo "User not found!";
    }

} else {
    echo "Error updating password!";
}

$stmt->close();
$conn->close();
?>