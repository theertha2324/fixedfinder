<?php
include 'db.php';

$user = $_POST['user'] ?? '';
$newpass = $_POST['password'] ?? '';

if(empty($user) || empty($newpass)){
    echo "Missing data!";
    exit();
}

// Update using email OR phone
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