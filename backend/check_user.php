<?php
include 'db.php';

$user = $_POST['user'];

// check email OR phone
$sql = "SELECT * FROM users WHERE email='$user' OR phone='$user'";
$result = $conn->query($sql);

if($result->num_rows > 0){
    echo "exists";
} else {
    echo "not_found";
}
?>