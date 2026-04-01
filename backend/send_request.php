<?php
session_start();
include "db.php";

$user_id = $_SESSION['user_id'];
$mechanic_id = $_POST['mechanic_id'];
$problem = $_POST['problem'];
$location = $_POST['location'];

$sql = "INSERT INTO requests (user_id, mechanic_id, problem, location)
        VALUES ('$user_id','$mechanic_id','$problem','$location')";

if($conn->query($sql)){
    header("Location: ../home.php");
} else {
    echo "Error: " . $conn->error;
}
?>