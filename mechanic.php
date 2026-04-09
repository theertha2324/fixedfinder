<?php
session_start();

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'mechanic'){
    header("Location: login.html");
    exit();
}

include "backend/db.php";

$mid = $_SESSION['user_id'];

$res = $conn->query("SELECT latitude, longitude, status FROM users WHERE id='$mid'");
$userData = $res->fetch_assoc();

$currentStatus = $userData['status'] ?? 'offline';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mechanic Dashboard</title>

    <link rel="stylesheet" href="css/common.css">
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

        <button id="toggleBtn"
        class="<?php echo ($currentStatus == 'online') ? 'online-btn' : 'offline-btn'; ?>"
        onclick="toggleStatus()">
        <?php echo ($currentStatus == 'online') ? "Go Offline" : "Go Online"; ?>
        </button>

        <p id="statusText">
            Currently <?php echo ucfirst($currentStatus); ?>
            <?php echo ($currentStatus == 'online') ? "🟢" : "🔴"; ?>
        </p>
    </div>

    <!-- PENDING -->
    <div class="card">
        <h3>📥 Incoming Requests</h3>
        <div id="pendingBox"></div>
    </div>

    <!-- ACCEPTED -->
    <div class="card">
        <h3>✅ Accepted Requests</h3>
        <div id="acceptedBox"></div>
    </div>

    <!-- HISTORY + SEARCH -->
    <div class="card">
        <h3>📜 Work History</h3>

        <input type="text" id="searchInput" placeholder="Search by User ID..." oninput="filterHistory()">

        <ul id="historyBox"></ul>
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

<!-- 🔥 TOGGLE -->
<script>
function toggleStatus(){

    let statusText = document.getElementById("statusText");
    let btn = document.getElementById("toggleBtn");

    let isOnline = statusText.innerText.includes("Online");
    let newStatus = isOnline ? "offline" : "online";

    fetch("backend/update_online_status.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "status=" + newStatus
    }).then(() => {

        if(newStatus === "online"){
            btn.className = "online-btn";
            btn.innerText = "Go Offline";
            statusText.innerText = "Currently Online 🟢";
        } else {
            btn.className = "offline-btn";
            btn.innerText = "Go Online";
            statusText.innerText = "Currently Offline 🔴";
        }

    });
}
</script>

<!-- 🔁 LIVE DATA -->
<script>
let fullHistory = [];

function updateRequest(id, status){
    fetch("backend/update_request_status.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: `request_id=${id}&status=${status}`
    });
}

function loadData(){

    fetch("backend/get_mechanic_data.php")
    .then(res => res.json())
    .then(data => {

        fullHistory = data.history;

        // PENDING
        let pHTML = "";
        data.pending.forEach(r => {
            pHTML += `
            <div class="user-card">
                <p><b>Problem:</b> ${r.problem}</p>
                <button onclick="updateRequest(${r.id}, 'accepted')">Accept</button>
                <button onclick="updateRequest(${r.id}, 'rejected')">Reject</button>
            </div>`;
        });
        document.getElementById("pendingBox").innerHTML = pHTML;

        // ACCEPTED (🔥 UPDATED HERE)
        let aHTML = "";
        data.accepted.forEach(r => {

            let loc = r.location.split(",");
            let lat = loc[0];
            let lng = loc[1];

            let gmap = `https://www.google.com/maps?q=${lat},${lng}`;

            aHTML += `
            <div class="user-card">
                <p>${r.problem}</p>
                <p>📞 ${r.user_phone}</p>

                <a href="tel:${r.user_phone}">
                    <button class="call-btn">Call User</button>
                </a>

                <!-- ✅ GOOGLE MAP BUTTON -->
                <a href="${gmap}" target="_blank">
                    <button class="map-btn">View in Google Maps 📍</button>
                </a>
            </div>`;
        });

        document.getElementById("acceptedBox").innerHTML = aHTML;

        // HISTORY
        filterHistory();
    });
}

// 🔍 FILTER
function filterHistory(){

    let search = document.getElementById("searchInput").value.trim().toLowerCase();

    let filtered = fullHistory.filter(r => 
        (r.user_key && r.user_key.toLowerCase().includes(search))
    );

    if(filtered.length === 0){
        document.getElementById("historyBox").innerHTML = "<p>No results ❌</p>";
        return;
    }

    renderHistory(filtered);
}

// 📜 RENDER
function renderHistory(data){

    let hHTML = "";

    data.forEach(r => {
        hHTML += `<li>
    <b>${r.user_key}</b> | ${r.problem} | ⭐ ${r.rating ?? 'N/A'} 
    <br><small>${r.feedback ?? ''}</small>
</li>`;
    });

    document.getElementById("historyBox").innerHTML = hHTML;
}

// 🔁 AUTO REFRESH
setInterval(loadData, 2000);
loadData();
</script>

</body>
</html>