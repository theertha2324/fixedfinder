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
    <style>
        body { font-family: Arial; background:#f4f4f4; }
        .card {
            width: 400px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px gray;
        }
        .stars {
            color: gold;
            font-size: 20px;
        }
    </style>
</head>

<body>

<div class="card">
    <h2>🧑‍🔧 <?php echo $row['name']; ?></h2>

    <p><b>📞 Phone:</b> <?php echo $row['phone']; ?></p>
    <p><b>🔧 Specialization:</b> <?php echo $row['specialization']; ?></p>

    <p><b>⭐ Rating:</b> 
        <?php echo $row['rating']; ?> / 5
    </p>

    <p class="stars">
        <?php
        $stars = round($row['rating']);
        for($i=1; $i<=5; $i++){
            echo $i <= $stars ? "⭐" : "☆";
        }
        ?>
    </p>

    <p>Total Reviews: <?php echo $row['total_reviews']; ?></p>
</div>

</body>
</html>