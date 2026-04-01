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
    <link rel="stylesheet" href="css/common.css">
<link rel="stylesheet" href="css/dashboard.css">
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

        .delete-btn {
            background: red;
            color: white;
            padding: 6px 10px;
            border: none;
            cursor: pointer;
            margin-top: 5px;
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

    <!-- 🔥 ALL COMPLAINTS -->
    <div class="card">
        <h3>📩 All Complaints</h3>

        <?php
        $result = $conn->query("
        SELECT c.*, u.name 
        FROM complaints c
        LEFT JOIN users u ON c.user_id = u.id
        ORDER BY c.id DESC
        ");

        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){

                echo "<div style='border:1px solid #ccc; padding:10px; margin:10px;'>";

                echo "<p><b>Name:</b> ".$row['name']."</p>";
                echo "<p><b>Role:</b> ".$row['role']."</p>";
                echo "<p><b>Complaint:</b> ".$row['complaint']."</p>";

                // ✅ FIXED IMAGE PATH
                if(!empty($row['image'])){
                    echo "<img src='".$row['image']."' width='150'>";
                }

                echo "</div>";
            }
        } else {
            echo "<p>No complaints yet</p>";
        }
        ?>
    </div>

    <!-- 🔥 MANAGE USERS -->
    <div class="card">
        <h3>Manage Users & Mechanics</h3>

        <?php
        $result = $conn->query("SELECT * FROM users");

        while($row = $result->fetch_assoc()){

            echo "<div style='border:1px solid #ccc; padding:10px; margin:10px;'>";

            echo "<p><b>Name:</b> ".$row['name']."</p>";
            echo "<p><b>Role:</b> ".$row['role']."</p>";

            echo "<form action='backend/delete_user.php' method='POST'>
                    <input type='hidden' name='id' value='".$row['id']."'>
                    <button class='delete-btn'>Delete</button>
                  </form>";

            echo "</div>";
        }
        ?>
    </div>

</div>

</body>
</html>