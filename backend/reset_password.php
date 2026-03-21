<?php
include 'db.php';

$phone = $_POST['phone'];
$newpass = $_POST['password'];

// Debug check (IMPORTANT)
if(empty($phone) || empty($newpass)){
    echo "Missing data!";
    exit();
}

// Update password
$sql = "UPDATE users SET password='$newpass' WHERE phone='$phone'";

if ($conn->query($sql) === TRUE) {

    if($conn->affected_rows > 0){
        echo "Password Updated Successfully ✅";
    } else {
        echo "User not found!";
    }

} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>