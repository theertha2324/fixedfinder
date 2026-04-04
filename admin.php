<?php
session_start();
include "backend/db.php";

// 🔐 PROTECT ADMIN
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.html");
    exit();
}

$search = $_GET['search'] ?? '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>

    <!-- ✅ COMMON GLASS CSS -->
    <link rel="stylesheet" href="css/common.css">

    <style>
        /* Only small custom tweaks (no override) */

        .search-box {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .user-card {
            border: 1px solid rgba(255,255,255,0.3);
            padding: 10px;
            margin-top: 10px;
            border-radius: 10px;
        }

        img {
            width: 150px;
        }
    </style>
</head>

<body>

<header>
    <h2>👨‍💼 Admin Dashboard</h2>
    <a href="logout.php"><button class="logout">Logout</button></a>
</header>

<div class="container">

    <!-- SEARCH -->
    <div class="card">
        <h3>🔍 Search User</h3>

        <form method="GET" class="search-box">
            <input type="text" name="search" placeholder="Enter User ID (FFXXXX)" value="<?php echo $search; ?>">
            <button class="search-btn">Search</button>
        </form>
    </div>

    <!-- USERS -->
    <div class="card">
        <h3>👥 Manage Users & Mechanics</h3>

        <?php
        if(!empty($search)){
            $query = "SELECT * FROM users 
                      WHERE user_key LIKE '%$search%' 
                      AND role != 'admin'";
        } else {
            $query = "SELECT * FROM users WHERE role != 'admin'";
        }

        $result = $conn->query($query);

        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){

                echo "<div class='user-card'>";

                echo "<p><b>Name:</b> ".$row['name']."</p>";
                echo "<p><b>User ID:</b> ".$row['user_key']."</p>";
                echo "<p><b>Role:</b> ".$row['role']."</p>";

                echo "<form action='backend/delete_user.php' method='POST'>
                        <input type='hidden' name='id' value='".$row['id']."'>
                        <button class='delete-btn'>Delete</button>
                      </form>";

                echo "</div>";
            }
        } else {
            echo "<p>No user found ❌</p>";
        }
        ?>
    </div>

    <!-- COMPLAINTS -->
    <div class="card">
        <h3>📩 Complaints</h3>

        <?php
        $result = $conn->query("
        SELECT c.*, u.name, u.role 
        FROM complaints c
        LEFT JOIN users u ON c.user_id = u.id
        ORDER BY c.id DESC
        ");

        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){

                echo "<div class='user-card'>";

                echo "<p><b>Name:</b> ".$row['name']."</p>";
                echo "<p><b>Role:</b> ".$row['role']."</p>";
                echo "<p><b>Complaint:</b> ".$row['complaint']."</p>";

                if(!empty($row['image'])){
                    echo "<img src='".$row['image']."'>";
                }

                // 🔥 REVIEW / DELETE LOGIC
                if($row['reviewed'] == 0){

                    echo "<form action='backend/update_complaint.php' method='POST'>
                            <input type='hidden' name='id' value='".$row['id']."'>
                            <button class='review-btn'>Review</button>
                          </form>";

                } else {

                    echo "<form action='backend/delete_complaint.php' method='POST'>
                            <input type='hidden' name='id' value='".$row['id']."'>
                            <button class='delete-btn'>Delete</button>
                          </form>";
                }

                echo "</div>";
            }
        } else {
            echo "<p>No complaints yet</p>";
        }
        ?>
    </div>

</div>

</body>
</html>