<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.html");
    exit();
}

$id = $_GET['id'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rate Mechanic</title>

    <!-- ✅ COMMON CSS -->
    <link rel="stylesheet" href="css/common.css">
</head>

<body>

<div class="container">

    <div class="card rating-card">

        <h2>⭐ Rate Mechanic</h2>

        <form action="backend/save_rating.php" method="POST">

            <!-- hidden request id -->
            <input type="hidden" name="request_id" value="<?php echo $id; ?>">

            <!-- ⭐ STAR RATING -->
            <div class="star-container">
                <span class="star" onclick="setRating(1)">☆</span>
                <span class="star" onclick="setRating(2)">☆</span>
                <span class="star" onclick="setRating(3)">☆</span>
                <span class="star" onclick="setRating(4)">☆</span>
                <span class="star" onclick="setRating(5)">☆</span>
            </div>

            <!-- hidden rating input -->
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
function setRating(value){
    document.getElementById("ratingInput").value = value;

    let stars = document.querySelectorAll(".star");

    stars.forEach((star, index) => {
        star.innerText = index < value ? "⭐" : "☆";
    });
}
</script>

</body>
</html>