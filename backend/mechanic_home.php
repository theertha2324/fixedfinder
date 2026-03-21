<?php
session_start();

// Check login
if(!isset($_SESSION['user_id'])){
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mechanic Dashboard</title>
</head>

<body>

<h1>🔧 Mechanic Dashboard</h1>

<h3>Welcome Mechanic</h3>

<!-- Availability -->
<label>Status:</label>
<select id="status">
    <option value="available">Available</option>
    <option value="busy">Busy</option>
</select>

<br><br>

<!-- Incoming Requests -->
<h2>🚗 Service Requests</h2>

<div id="requests">
    <p>No requests yet</p>
</div>

<br>

<a href="logout.php">Logout</a>

</body>
</html>