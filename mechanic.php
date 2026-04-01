<?php
session_start();

// protect page
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'mechanic'){
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="css/style.css">
    <title>Mechanic Dashboard</title>

    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
            margin: 0;
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

        button {
            padding: 8px 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .accept { background: green; color: white; }
        .reject { background: red; color: white; }

        .toggle {
            background: blue;
            color: white;
        }

        textarea {
            width: 100%;
            height: 80px;
        }

        .logout {
            float: right;
            background: red;
            color: white;
        }
    </style>
</head>

<body>

<header>
    <h2>🧑‍🔧 Mechanic Dashboard</h2>
    <a href="logout.php"><button class="logout">Logout</button></a>
</header>

<div class="container">

    <!-- Profile -->
    <div class="card">
        <h3>Profile</h3>
        <p><b>Name:</b> <?php echo $_SESSION['name']; ?></p>
        <p><b>Phone:</b> <?php echo $_SESSION['phone']; ?></p>
        <p><b>Location:</b> <?php echo $_SESSION['location']; ?></p>
    </div>

    <!-- Status -->
    <div class="card">
        <h3>Status</h3>
        <button id="toggleBtn" class="toggle" onclick="toggleStatus()">Go Online</button>
        <p id="status">Currently Offline</p>
    </div>

    <!-- Requests -->
    <div class="card">
        <h3>Incoming Requests</h3>
        <div class="card">
    <h3>Accepted Requests</h3>

<?php
include "backend/db.php";

$mid = $_SESSION['user_id'];

$result = $conn->query("SELECT * FROM requests 
WHERE mechanic_id='$mid' AND status='accepted'");

if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        echo "<div style='border:1px solid #ccc; padding:10px; margin:10px;'>";

        echo "<p><b>Problem:</b> ".$row['problem']."</p>";
        echo "<p><b>Location:</b> ".$row['location']."</p>";

        echo "<p style='color:green;'>Accepted ✅</p>";

        echo "</div>";
    }
} else {
    echo "No accepted requests";
}
?>
</div>
        <?php
include "backend/db.php";

// get all pending requests
$result = $conn->query("SELECT * FROM requests 
WHERE status='pending' AND mechanic_id = ".$_SESSION['user_id']."");

if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        echo "<div style='border:1px solid #ccc; padding:10px; margin:10px 0;'>";

        echo "<p><b>Problem:</b> ".$row['problem']."</p>";
        echo "<p><b>Location:</b> ".$row['location']."</p>";

        // accept button
        echo "<form action='backend/update_status.php' method='POST' style='display:inline;'>
                <input type='hidden' name='request_id' value='".$row['id']."'>
                <input type='hidden' name='status' value='accepted'>
                <button class='accept'>Accept</button>
              </form>";

        // reject button
        echo "<form action='backend/update_status.php' method='POST' style='display:inline;'>
                <input type='hidden' name='request_id' value='".$row['id']."'>
                <input type='hidden' name='status' value='rejected'>
                <button class='reject'>Reject</button>
              </form>";

        echo "</div>";
    }
} else {
    echo "<p>No requests yet</p>";
}
?>
    </div>
    <div class="card">
    <h3>Repair History</h3>

<?php
$result = $conn->query("SELECT * FROM requests 
WHERE mechanic_id='$mid' AND status='completed'");

if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        echo "<div style='border:1px solid #ccc; padding:10px; margin:10px;'>";

        echo "<p><b>Problem:</b> ".$row['problem']."</p>";
        echo "<p>Rating: ⭐ ".$row['rating']."</p>";

        echo "</div>";
    }
} else {
    echo "No completed work yet";
}
?>
</div>

    <!-- Complaint -->
    <div class="card">
        <h3>Raise Complaint</h3>

        <form action="backend/complaint.php" method="POST" enctype="multipart/form-data">

            <label>Complaint:</label><br>
            <textarea name="complaint" required></textarea><br><br>

            <label>Upload Image:</label><br>
            <input type="file" name="image" required><br><br>

            <button type="submit">Submit Complaint</button>
        </form>
    </div>

</div>

<!-- 🔥 FIXED TOGGLE SCRIPT -->
<script>
let online = false;

function toggleStatus() {
    online = !online;

    let statusText = document.getElementById("status");
    let button = document.getElementById("toggleBtn");

    if (online) {
        statusText.innerText = "Currently Online";
        button.innerText = "Go Offline";
        button.style.background = "red";
    } else {
        statusText.innerText = "Currently Offline";
        button.innerText = "Go Online";
        button.style.background = "blue";
    }
}
</script>

</body>
</html>