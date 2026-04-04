<?php
session_start();

// 🔐 PROTECT PAGE
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'mechanic'){
    header("Location: login.html");
    exit();
}

include "backend/db.php";

// 🔥 GET MECHANIC LOCATION
$mid = $_SESSION['user_id'];
$res = $conn->query("SELECT latitude, longitude FROM users WHERE id='$mid'");
$userData = $res->fetch_assoc();

$mechLat = $userData['latitude'] ?? 0;
$mechLng = $userData['longitude'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mechanic Dashboard - FixedFinder</title>

    <!-- ✅ GLASS CSS -->
    <link rel="stylesheet" href="css/common.css">

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <style>
        .request-card {
            border: 1px solid rgba(255,255,255,0.3);
            padding: 12px;
            margin-top: 12px;
            border-radius: 10px;
        }

        .accept { background: rgba(0,255,0,0.4); }
        .reject { background: rgba(255,0,0,0.4); }

        .toggle-online { background: rgba(0,200,0,0.5); }
        .toggle-offline { background: rgba(255,0,0,0.5); }

        .call-btn { background: rgba(0,150,255,0.6); }

        #map {
            height: 220px;
            border-radius: 10px;
            margin-top: 10px;
        }
    </style>
</head>

<body>

<header>
    <h2>🧑‍🔧 Mechanic Dashboard</h2>
    <a href="logout.php"><button class="logout">Logout</button></a>
</header>

<div class="container">

    <!-- PROFILE -->
    <div class="card">
        <h3>👤 Profile</h3>
        <p><b>Name:</b> <?php echo $_SESSION['name']; ?></p>
        <p><b>Phone:</b> <?php echo $_SESSION['phone']; ?></p>
    </div>

    <!-- STATUS -->
    <div class="card">
        <h3>📡 Availability</h3>
        <button id="toggleBtn" class="toggle-online" onclick="toggleStatus()">Go Online</button>
        <p id="status">Currently Offline 🔴</p>
    </div>

    <!-- PENDING -->
    <div class="card">
        <h3>📥 Incoming Requests</h3>

        <?php
        $result = $conn->query("SELECT * FROM requests 
        WHERE status='pending' AND mechanic_id='$mid'");

        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){

                echo "<div class='request-card'>";

                echo "<p><b>Problem:</b> ".$row['problem']."</p>";
                echo "<p>📍 Location: Puttur</p>";

                echo "<form action='backend/update_status.php' method='POST'>
                        <input type='hidden' name='request_id' value='".$row['id']."'>
                        <input type='hidden' name='status' value='accepted'>
                        <button class='accept'>Accept</button>
                      </form>";

                echo "<form action='backend/update_status.php' method='POST'>
                        <input type='hidden' name='request_id' value='".$row['id']."'>
                        <input type='hidden' name='status' value='rejected'>
                        <button class='reject'>Reject</button>
                      </form>";

                echo "</div>";
            }
        } else {
            echo "<p>No pending requests</p>";
        }
        ?>
    </div>

    <!-- ACCEPTED -->
    <div class="card">
        <h3>✅ Accepted Requests</h3>

        <?php
        $result = $conn->query("SELECT * FROM requests 
        WHERE mechanic_id='$mid' AND status='accepted'");

        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){

                list($userLat, $userLng) = explode(",", $row['location']);

                echo "<div class='request-card'>";

                echo "<p><b>Problem:</b> ".$row['problem']."</p>";
                echo "<p>📍 Location: Puttur</p>";
                echo "<p><b>📞 User:</b> ".$row['user_phone']."</p>";

                echo "<a href='tel:".$row['user_phone']."'>
                        <button class='call-btn'>Call User</button>
                      </a>";

                echo "<p style='color:lightgreen;'>Accepted ✔</p>";

                echo "<div id='map".$row['id']."'></div>";

                echo "<script>
                setTimeout(function(){
                    showRoute(
                        'map".$row['id']."',
                        $mechLat,
                        $mechLng,
                        $userLat,
                        $userLng
                    );
                }, 300);
                </script>";

                echo "</div>";
            }
        } else {
            echo "<p>No accepted requests</p>";
        }
        ?>
    </div>

    <!-- COMPLAINT -->
    <div class="card">
        <h3>⚠️ Raise Complaint</h3>

        <form action="backend/complaint.php" method="POST" enctype="multipart/form-data">
            <textarea name="complaint" placeholder="Enter complaint..." required></textarea>
            <input type="file" name="image">
            <button type="submit">Submit</button>
        </form>
    </div>

</div>

<!-- TOGGLE -->
<script>
let online = false;

function toggleStatus() {
    online = !online;
    let status = online ? "online" : "offline";

    fetch("backend/update_status.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "status=" + status
    });

    let txt = document.getElementById("status");
    let btn = document.getElementById("toggleBtn");

    if (online) {
        txt.innerText = "Currently Online 🟢";
        btn.innerText = "Go Offline";
        btn.className = "toggle-offline";
    } else {
        txt.innerText = "Currently Offline 🔴";
        btn.innerText = "Go Online";
        btn.className = "toggle-online";
    }
}
</script>

<!-- MAP -->
<script>
function showRoute(mapId, mechLat, mechLng, userLat, userLng){

    if(!mechLat || !mechLng) return;

    let map = L.map(mapId).setView([mechLat, mechLng], 13);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    L.marker([mechLat, mechLng]).addTo(map).bindPopup("You");
    L.marker([userLat, userLng]).addTo(map).bindPopup("Customer");

    L.polyline([[mechLat, mechLng],[userLat, userLng]], {
        color: 'cyan',
        weight: 4
    }).addTo(map);
}
</script>

</body>
</html>