<?php
include "db.php";

$user_lat = $_POST['lat'];
$user_lng = $_POST['lng'];

$sql = "SELECT * FROM users 
        WHERE role='mechanic' 
        AND status='online'
        AND latitude IS NOT NULL";
$result = $conn->query($sql);

$mechanics = [];

while($row = $result->fetch_assoc()){

    $distance = getDistance($user_lat, $user_lng, $row['latitude'], $row['longitude']);

    if($distance <= 10){ // within 10 km
        $row['distance'] = round($distance,2);
        $mechanics[] = $row;
    }
}

echo json_encode($mechanics);


// distance function
function getDistance($lat1, $lon1, $lat2, $lon2) {
    $earth_radius = 6371;

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat/2) * sin($dLat/2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon/2) * sin($dLon/2);

    $c = 2 * atan2(sqrt($a), sqrt(1-$a));

    return $earth_radius * $c;
}
?>