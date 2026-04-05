<?php
include "backend/db.php";

$id = $_GET['id'];

$res = $conn->query("SELECT * FROM users WHERE id='$id'");
$row = $res->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mechanic Profile</title>
    <link rel="stylesheet" href="css/common.css">
</head>

<body>

<div class="container">

    <div class="card profile-card">

        <h2>🧑‍🔧 <?php echo $row['name']; ?></h2>

        <p><b>📞 Phone:</b> <?php echo $row['phone']; ?></p>
        <p><b>🔧 Specialization:</b> <?php echo $row['specialization']; ?></p>

        <p><b>⭐ Rating:</b> <?php echo $row['rating']; ?> / 5</p>

        <div class="stars">
            <?php
            $stars = round($row['rating']);
            for($i=1; $i<=5; $i++){
                echo $i <= $stars ? "⭐" : "☆";
            }
            ?>
        </div>

        <p>Total Reviews: <?php echo $row['total_reviews']; ?></p>

        <br>
        <a href="home.php">
            <button>⬅ Back</button>
        </a>

    </div>

</div>

</body>
</html>