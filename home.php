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

    <!-- ✅ COMMON GLASS CSS -->
    <link rel="stylesheet" href="css/common.css">

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <style>
        .mech-card {
            border: 1px solid rgba(255,255,255,0.3);
            padding: 12px;
            margin-top: 12px;
            border-radius: 10px;
        }

        .request-card {
            border: 1px solid rgba(255,255,255,0.3);
            padding: 12px;
            margin-top: 12px;
            border-radius: 10px;
        }

        .view-btn { background: rgba(255,165,0,0.6); }
        .call-btn { background: rgba(0,150,255,0.6); }
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

                echo "<div class='request-card'>";

                echo "<p><b>Problem:</b> ".$row['problem']."</p>";
                echo "<p>Status: ".$row['status']."</p>";

                if($row['status'] == 'accepted'){
                    echo "<p>📞 Mechanic: ".$row['phone']."</p>";

                    echo "<a href='tel:".$row['phone']."'>
                            <button class='call-btn'>Call Now</button>
                          </a>";

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
            <textarea name="complaint" placeholder="Enter your complaint..." required></textarea>
            <input type="file" name="image">
            <button type="submit">Submit Complaint</button>
        </form>
    </div>

</div>

<!-- MAP -->
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

<!-- FIND MECHANICS -->
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
            html = "No mechanics nearby ❌";
        } else {
            data.forEach(m => {

                let marker = L.marker([m.latitude, m.longitude]).addTo(map)
                    .bindPopup(`<b>${m.name}</b><br>${m.distance} km`);

                mechanicMarkers.push(marker);

                html += `
                <div class="mech-card">
                    <p><b>${m.name}</b></p>
                    <p>⭐ ${m.rating} / 5</p>
                    <p>Distance: ${m.distance} km</p>

                    <a href="mechanic_profile.php?id=${m.id}">
                        <button class="view-btn">View Profile</button>
                    </a>

                    <form action="backend/send_request.php" method="POST">
                        <input type="hidden" name="mechanic_id" value="${m.id}">
                        <input type="hidden" name="location" value="${lat},${lng}">
                        <input type="text" name="problem" placeholder="Enter problem" required>

                        <button>Request</button>
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