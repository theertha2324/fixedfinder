<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Trouble Assist</title>
    <link rel="stylesheet" href="css/common.css">

    <style>
        .chat-box {
            height: 300px;
            overflow-y: auto;
            background: #111;
            color: #fff;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .user-msg { color: #00e676; }
        .bot-msg { color: #00b0ff; }

        .chat-input {
            display: flex;
            gap: 10px;
        }

        .chat-input input {
            flex: 1;
            padding: 10px;
            border-radius: 8px;
            border: none;
        }

        .chat-input button {
            padding: 10px 15px;
            border: none;
            background: #00bcd4;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            .no-solution-btn {
    background: #f44336;
    color: white;
    padding: 10px 18px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
}

.no-solution-btn:hover {
    background: #d32f2f;
}
        }
    </style>
</head>

<body>

<header>
    <h2>🛠 Trouble Assistant</h2>
    <a href="home.php"><button class="logout">⬅ Back</button></a>
</header>

<div class="container">

    <!-- VEHICLE -->
    <div class="card">
        <h3>Select Vehicle</h3>

        <select id="vehicle">
            <option value="bike">Bike</option>
            <option value="car">Car</option>
            <option value="truck">Truck</option>
        </select>

        <h3>Select Issue</h3>

        <select id="issue">
            <option value="not starting">Not Starting</option>
            <option value="engine overheating">Engine Overheating</option>
            <option value="battery problem">Battery Problem</option>
            <option value="brake issue">Brake Issue</option>
        </select>

        <br><br>

        <button onclick="askQuick()">Get Solution ⚡</button>
    </div>

    <!-- CHAT -->
    <div class="card">
        <h3>AI Assistant 💬</h3>

        <div id="chatBox" class="chat-box"></div>

        <div class="chat-input">
            <input type="text" id="msg" placeholder="Ask anything...">
            <button onclick="sendMsg()">Send</button>
        </div>
    </div>

</div>
<div style="text-align:center; margin-top:20px;">
    <a href="home.php">
        <button class="no-solution-btn" onclick="goHome()">❌ Not Getting Solution</button>
    </a>
</div>


<script>
// quick query
function askQuick(){
    let v = document.getElementById("vehicle").value;
    let i = document.getElementById("issue").value;

    let q = `My ${v} has problem: ${i}. Give solution.`;

    sendToGPT(q);
}

// manual chat
function sendMsg(){
    let msg = document.getElementById("msg").value;

    if(!msg) return;

    sendToGPT(msg);
    document.getElementById("msg").value = "";
}

// main function
function sendToGPT(message){

    let chatBox = document.getElementById("chatBox");

    chatBox.innerHTML += `<div class="user-msg">You: ${message}</div>`;

    fetch("backend/chatgpt.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "msg=" + encodeURIComponent(message)
    })
    .then(res => res.text())
    .then(reply => {
        chatBox.innerHTML += `<div class="bot-msg">AI: ${reply}</div>`;
        chatBox.scrollTop = chatBox.scrollHeight;
    });
}
function goHome(){
    if(confirm("Do you want to find a mechanic instead?")){
        window.location.href = "home.php";
    }
}
</script>

</body>
</html>