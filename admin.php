<?php
session_start();
include "backend/db.php";

// protect admin page
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>

    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
        }

        header {
            background: #333;
            color: white;
            padding: 15px;
            text-align: center;
        }

        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
        }

        .card {
            background: white;
            padding: 15px;
            margin-top: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px gray;
        }

        img {
            margin-top: 10px;
            border-radius: 5px;
        }

        .logout {
            float: right;
            background: red;
            color: white;
            border: none;
            padding: 8px;
            cursor: pointer;
        }
    </style>
</head>

<body>

<header>
    <h2>👨‍💼 Admin Dashboard</h2>
    <a href="logout.php"><button class="logout">Logout</button></a>
</header>

<div class="container">

    <!-- Welcome -->
    <div class="card">
        <h3>Welcome, <?php echo $_SESSION['name']; ?> 👋</h3>
    </div>

    <!-- Complaints Section -->
    <div class="card">
        <h3>📩 Complaints</h3>

        <?php
        $result = $conn->query("SELECT * FROM complaints ORDER BY created_at DESC");

        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                echo "<p><b>Mechanic ID:</b> ".$row['mechanic_id']."</p>";
                echo "<p><b>Complaint:</b> ".$row['complaint']."</p>";

                if($row['image']){
                    echo "<img src='uploads/".$row['image']."' width='150'>";
                }

                echo "<hr>";
            }
        } else {
            echo "<p>No complaints yet</p>";
        }
        ?>
    </div>

</div>

</body>
</html>