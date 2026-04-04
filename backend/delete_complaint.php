<?php
include "db.php";

$id = $_POST['id'];

$conn->query("DELETE FROM complaints WHERE id='$id'");

header("Location: ../admin.php");
?>