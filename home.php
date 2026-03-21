<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['user_id'])){
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home - Fixed Finder</title>
</head>

<body>

<h1>Welcome to Fixed Finder 🚗</h1>

<button onclick="location.href='map.html'">
    My Vehicle Broke Down
</button>

<br><br>

<a href="logout.php">Logout</a>

</body>
</html>