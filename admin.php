<?php
session_start();
include "backend/db.php";

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

    <link rel="stylesheet" href="css/common.css">

    <style>
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

        img { width: 150px; }
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

        <div id="usersBox">

        <?php
        if(!empty($search)){
            $query = "SELECT * FROM users 
                      WHERE user_key LIKE '%$search%' 
                      AND role != 'admin'";
        } else {
            $query = "SELECT * FROM users WHERE role != 'admin'";
        }

        $result = $conn->query($query);

        while($row = $result->fetch_assoc()){

            echo "<div class='user-card' id='user".$row['id']."'>";

            echo "<p><b>Name:</b> ".$row['name']."</p>";
            echo "<p><b>User ID:</b> ".$row['user_key']."</p>";
            echo "<p><b>Role:</b> ".$row['role']."</p>";

            echo "<button class='delete-btn' onclick='deleteUser(".$row['id'].")'>Delete</button>";

            echo "</div>";
        }
        ?>

        </div>
    </div>

    <!-- COMPLAINTS -->
    <div class="card">
        <h3>📩 Complaints</h3>

        <div id="complaintBox">

        <?php
        $result = $conn->query("
        SELECT c.*, u.name, u.role 
        FROM complaints c
        LEFT JOIN users u ON c.user_id = u.id
        ORDER BY c.id DESC
        ");

        while($row = $result->fetch_assoc()){

            echo "<div class='user-card' id='comp".$row['id']."'>";

            echo "<p><b>Name:</b> ".$row['name']."</p>";
            echo "<p><b>Role:</b> ".$row['role']."</p>";
            echo "<p><b>Complaint:</b> ".$row['complaint']."</p>";

            if(!empty($row['image'])){
                echo "<img src='".$row['image']."'>";
            }

            if($row['reviewed'] == 0){
                echo "<button class='review-btn' onclick='reviewComplaint(".$row['id'].")'>Review</button>";
            } else {
                echo "<button class='delete-btn' onclick='deleteComplaint(".$row['id'].")'>Delete</button>";
            }

            echo "</div>";
        }
        ?>

        </div>
    </div>

</div>

<!-- 🔥 JS (NO RELOAD ACTIONS) -->
<script>

// 🗑 DELETE USER
function deleteUser(id){

    fetch("backend/delete_user.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "id=" + id
    }).then(() => {
        document.getElementById("user"+id).remove();
    });
}

// ✅ REVIEW COMPLAINT
function reviewComplaint(id){

    fetch("backend/update_complaint.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "id=" + id
    }).then(() => {
        let btn = document.querySelector("#comp"+id+" button");
        btn.innerText = "Delete";
        btn.className = "delete-btn";
        btn.onclick = () => deleteComplaint(id);
    });
}

// 🗑 DELETE COMPLAINT
function deleteComplaint(id){

    fetch("backend/delete_complaint.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "id=" + id
    }).then(() => {
        document.getElementById("comp"+id).remove();
    });
}

</script>

</body>
</html>