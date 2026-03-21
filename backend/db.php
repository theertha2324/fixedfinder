<?php
$conn = new mysqli("localhost", "root", "", "fixedfinder");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>