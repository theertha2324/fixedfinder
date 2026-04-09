<?php
session_start();

// 🔐 PROTECT PAGE
if(!isset($_SESSION['user_id'])){
    header("Location: login.html");
    exit();
}

// ✅ GET REQUEST ID SAFELY
$id = $_GET['id'] ?? 0;

if(!$id){
    echo "Invalid request";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rate Mechanic</title>

    <link rel="stylesheet" href="css/common.css">

    <style>
        .star-container {
            font-size: 30px;
            cursor: pointer;
            margin-bottom: 10px;
        }

        .star {
            margin: 5px;
        }

        textarea {
            width: 100%;
            padding: 8px;
            border-radius: 6px;
        }

        button {
            padding: 8px 15px;
            border: none;
            background: orange;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background: #ff9800;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="card rating-card">

        <h2>⭐ Rate Mechanic</h2>

        <!-- ✅ FORM -->
        <form id="ratingForm">

            <input type="hidden" name="request_id" value="<?php echo $id; ?>">

            <!-- ⭐ STARS -->
            <div class="star-container">
                <span class="star" onclick="setRating(1)">☆</span>
                <span class="star" onclick="setRating(2)">☆</span>
                <span class="star" onclick="setRating(3)">☆</span>
                <span class="star" onclick="setRating(4)">☆</span>
                <span class="star" onclick="setRating(5)">☆</span>
            </div>

            <!-- hidden -->
            <input type="hidden" name="rating" id="ratingInput" required>

            <!-- feedback -->
            <label>Feedback:</label>
            <textarea name="feedback" placeholder="Write your feedback..."></textarea>

            <br><br>

            <button type="submit">Submit</button>

        </form>

    </div>

</div>

<!-- ⭐ SCRIPT -->
<script>

// ⭐ SELECT RATING
function setRating(value){
    document.getElementById("ratingInput").value = value;

    let stars = document.querySelectorAll(".star");

    stars.forEach((star, index) => {
        star.innerText = index < value ? "⭐" : "☆";
    });
}


// ✅ SUBMIT WITHOUT LOGOUT / RELOAD ISSUE
document.getElementById("ratingForm").addEventListener("submit", function(e){
    e.preventDefault();

    let rating = document.getElementById("ratingInput").value;

    if(!rating){
        alert("Please select rating ⭐");
        return;
    }

    let formData = new FormData(this);

    fetch("backend/save_rating.php", {
        method: "POST",
        body: formData
    })
    .then(() => {
        alert("Rating submitted successfully ⭐");

        // ✅ REDIRECT BACK
        window.location.href = "home.php";
    });
});

</script>

</body>
</html>