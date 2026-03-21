<?php
session_start();

// protect page (no direct access)
if(!isset($_SESSION['name'])){
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
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
        .toggle { background: blue; color: white; }

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
        <button class="toggle" onclick="toggleStatus()">Go Online</button>
        <p id="status">Currently Offline</p>
    </div>

    <!-- Requests -->
    <div class="card">
        <h3>Incoming Requests</h3>

        <div id="requests">
            <p>No requests yet</p>
        </div>
    </div>

    <!-- Complaint System -->
    <div class="card">
        <h3>Raise Complaint</h3>

        <form action="backend/complaint.php" method="POST" enctype="multipart/form-data">

            <label>Complaint:</label><br>
            <textarea name="complaint" required></textarea><br><br>

            <label>Upload Image:</label><br>
            <input type="file" name="image" accept="image/*" required><br><br>

            <button type="submit">Submit Complaint</button>
        </form>
    </div>

</div>

<script>
let online = false;

function toggleStatus() {
    online = !online;

    document.getElementById("status").innerText = online 
        ? "Currently Online" 
        : "Currently Offline";
}
</script>
...
</div> <!-- container ends -->

<script>
let online = false;

function toggleStatus() {
    online = !online;

    let statusText = document.getElementById("status");
    let button = document.querySelector(".toggle");

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
<?php print_r($_SESSION); ?>

</body>
</html>

</body>
</html>