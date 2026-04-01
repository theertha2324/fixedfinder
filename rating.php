<?php
$id = $_GET['id'];
?>

<h2>Rate Mechanic</h2>

<form action="backend/save_rating.php" method="POST">
    <input type="hidden" name="request_id" value="<?php echo $id; ?>">

    <label>Rating (1-5):</label>
    <input type="number" name="rating" min="1" max="5" required><br><br>

    <textarea name="feedback" placeholder="Write feedback"></textarea><br><br>

    <button type="submit">Submit</button>
</form>