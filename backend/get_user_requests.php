<?php
session_start();
include "db.php";

$uid = $_SESSION['user_id'];

$result = $conn->query("
SELECT r.*, u.phone 
FROM requests r
LEFT JOIN users u ON r.mechanic_id = u.id
WHERE r.user_id='$uid'
ORDER BY r.created_at DESC
");

echo "<h3>📦 Your Requests</h3>";

while($row = $result->fetch_assoc()){

    $status = strtolower(trim($row['status']));

    echo "<div class='user-card'>";

    echo "<p><b>Problem:</b> ".$row['problem']."</p>";
    echo "<p>Status: ".$row['status']."</p>";

    // ✅ ACCEPTED
    if($status === 'accepted'){

        echo "<p>📞 ".$row['phone']."</p>";

        echo "<a href='tel:".$row['phone']."'>
                <button class='call-btn'>Call Now</button>
              </a>";

        echo "<button onclick='completeRequest(".$row['id'].")' class='complete-btn'>
                Mark as Repaired
              </button>";
    }

    // ✅ COMPLETED
    if($status === 'completed'){

        echo "<button class='done-btn' disabled>
                ✔ Work Completed
              </button>";

        if(empty($row['rating'])){
            echo "<a href='rating.php?id=".$row['id']."'>
                    <button>Rate ⭐</button>
                  </a>";
        } else {
            echo "<p>⭐ Rated: ".$row['rating']." / 5</p>";
        }
    }

    echo "</div>";
}
?>