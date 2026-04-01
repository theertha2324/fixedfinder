<?php
include "db.php";

$id = $_POST['id'];

// delete user or mechanic
$conn->query("DELETE FROM users WHERE id='$id'");

header("Location: ../admin.php");
?>