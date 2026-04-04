<?php
include "db.php";

$id = $_POST['id'];

// toggle reviewed
$conn->query("UPDATE complaints SET reviewed = 1 WHERE id='$id'");

header("Location: ../admin.php");
?>