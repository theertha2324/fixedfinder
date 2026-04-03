<?php
session_start();

// 🔐 PROTECT PAGE
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'user'){
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard - FixedFinder</title>

    <!-- CSS -->
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/dashboard.css">

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
        }

        header {
            background: #222;
            color: white;
            padding: 15px;
            text-align: center;
            position: relative;
        }

        .logout {
            position: absolute;
            right: 20px;
            top: 15px;
            background: red;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
        }

        .container {
            width: 85%;
            margin: auto;
            padding: 20px;
        }

        .card {
            background: white;
            padding: 15px;
            margin-top: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.2);
        }

        button {
            padding: 8px 15px;
            border: none;
            background: green;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }

        #map {
            height: 300px;
            margin-top: 10px;
            border-radius: 10px;
        }

        textarea {
            width: 100%;
            height: 80px;
            padding: 10px;
        }
    </style>
</head>

<body>

<header>
    <h2>👤 FixedFinder Dashboard</h2>
    <a href="logout.php"><button class="logout">Logout</button></a>
</header>

<div class="container">

    <!-- PROFILE -->
    <div class="card">
        <h3>Welcome, <?php echo $_SESSION['name']; ?> 👋</h3>
        <p><b>Phone:</b> <?php echo $_SESSION['phone']; ?></p>
        <p><b>User ID:</b> <?php echo $_SESSION['user_key']; ?> 🔑</p>
    </div>

    <!-- MAP -->
    <div class="card">
        <h3>📍 Find Nearby Mechanics</h3>

        <div id="map"></div>

        <input type="hidden" id="lat">
        <input type="hidden" id="lng">

        <br>
        <button onclick="findMechanics()">Find Mechanics</button>

        <div id="mechanicsList"></div>
    </div>

    <!-- REQUESTS -->
    <div class="card">
        <h3>📦 Your Requests</h3>

        <?php
        include "backend/db.php";

        $uid = $_SESSION['user_id'];

        $result = $conn->query("SELECT r.*, u.phone 
        FROM requests r
        LEFT JOIN users u ON r.mechanic_id = u.id
        WHERE r.user_id='$uid'
        ORDER BY r.created_at DESC");

        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){

                echo "<div style='border:1px solid #ccc; padding:10px; margin:10px;'>";

                echo "<p><b>Problem:</b> ".$row['problem']."</p>";
                echo "<p>Status: ".$row['status']."</p>";

                if($row['status'] == 'accepted'){
                    echo "<p>Mechanic Phone: ".$row['phone']."</p>";

                    echo "<form action='backend/complete_request.php' method='POST'>
                            <input type='hidden' name='request_id' value='".$row['id']."'>
                            <button>Mark as Repaired</button>
                          </form>";
                }

                echo "</div>";
            }
        } else {
            echo "<p>No requests yet</p>";
        }
        ?>
    </div>

    <!-- COMPLAINT -->
    <div class="card">
        <h3>⚠️ Raise Complaint</h3>

        <form action="backend/complaint.php" method="POST" enctype="multipart/form-data">
            <textarea name="complaint" placeholder="Enter your complaint" required></textarea><br><br>
            <input type="file" name="image"><br><br>
            <button type="submit">Submit Complaint</button>
        </form>
    </div>

</div>

<!-- ================= MAP ================= -->
<script>
let map = L.map('map').setView([12.9716, 77.5946], 13);
let marker;

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

map.on('click', function(e){

    let lat = e.latlng.lat;
    let lng = e.latlng.lng;

    document.getElementById("lat").value = lat;
    document.getElementById("lng").value = lng;

    if(marker){
        map.removeLayer(marker);
    }

    marker = L.marker([lat, lng]).addTo(map)
        .bindPopup("Your Location")
        .openPopup();
});
</script>

<!-- ================= FIND MECHANICS ================= -->
<script>
let mechanicMarkers = [];

function findMechanics(){

    let lat = document.getElementById("lat").value;
    let lng = document.getElementById("lng").value;

    if(lat === ""){
        alert("Select location on map");
        return;
    }

    map.setView([lat, lng], 13);

    fetch("backend/get_mechanics.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "lat="+lat+"&lng="+lng
    })
    .then(res => res.json())
    .then(data => {

        let html = "";

        mechanicMarkers.forEach(m => map.removeLayer(m));
        mechanicMarkers = [];

        if(data.length === 0){
            html = "No mechanics nearby";
        } else {
            data.forEach(m => {

                let marker = L.marker([m.latitude, m.longitude]).addTo(map)
                    .bindPopup(`<b>${m.name}</b><br>${m.distance} km`);

                mechanicMarkers.push(marker);

                html += `
                    <div style="border:1px solid #ccc; padding:10px; margin:10px;">
                        <p><b>${m.name}</b></p>
                        <p>Distance: ${m.distance} km</p>

                        <form action="backend/send_request.php" method="POST">
                            <input type="hidden" name="mechanic_id" value="${m.id}">
                            <input type="hidden" name="location" value="${lat},${lng}">
                            <input type="text" name="problem" placeholder="Enter problem" required>

                            <button type="submit">Request</button>
                        </form>
                    </div>
                `;
            });
        }

        document.getElementById("mechanicsList").innerHTML = html;
    });
}
</script>

</body>
</html>