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

    <link rel="stylesheet" href="css/common.css">

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
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

                echo "<div class='user-card'>";

                echo "<p><b>Problem:</b> ".$row['problem']."</p>";
                echo "<p>Status: ".$row['status']."</p>";

                if($row['status'] == 'accepted'){
                    echo "<p>📞 Mechanic: ".$row['phone']."</p>";

                    echo "<a href='tel:".$row['phone']."'>
                            <button class='call-btn'>Call Now</button>
                          </a>";

                    echo "<form onsubmit='completeRequest(event, ".$row['id'].")'>
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

<!-- ================= MAP ================= -->
<script>
let map = L.map('map').setView([12.7590, 75.2010], 13);
let marker;

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

// Fix map render
setTimeout(() => map.invalidateSize(), 300);

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

                    <form onsubmit="sendRequest(event, ${m.id}, '${lat},${lng}')">
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

<!-- ================= AJAX FUNCTIONS ================= -->
<script>
// 🔥 SEND REQUEST
function sendRequest(e, mechanicId, location){
    e.preventDefault();

    let problem = e.target.querySelector("input[name='problem']").value;

    fetch("backend/send_request.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "mechanic_id="+mechanicId+"&location="+location+"&problem="+problem
    })
    .then(() => {
        alert("Request Sent ✅");
    });
}

// 🔥 COMPLETE REQUEST
function completeRequest(e, requestId){
    e.preventDefault();

    fetch("backend/complete_request.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "request_id="+requestId
    })
    .then(() => {
        alert("Marked as Completed ✅");
        location.reload();
    });
}
</script>

<!-- ================= SCROLL FIX ================= -->
<script>
// Save scroll
window.onbeforeunload = function() {
    localStorage.setItem("scrollPos", window.scrollY);
};

// Restore scroll
window.onload = function() {
    let scroll = localStorage.getItem("scrollPos");
    if(scroll){
        window.scrollTo(0, scroll);
    }
};
</script>

</body>
</html>