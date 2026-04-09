<?php
$msg = strtolower($_POST['msg'] ?? '');

$response = "Sorry, I couldn't understand. Please contact a mechanic.";

// 🔥 RULE-BASED AI
if(strpos($msg, "not starting") !== false){
    $response = "Check battery connection, fuel level, and ignition switch.";
}
elseif(strpos($msg, "battery") !== false){
    $response = "Battery may be dead. Try jump-start or replace battery.";
}
elseif(strpos($msg, "overheating") !== false){
    $response = "Check coolant level and radiator fan.";
}
elseif(strpos($msg, "brake") !== false){
    $response = "Brake pads may be worn out. Avoid driving and check immediately.";
}
elseif(strpos($msg, "engine") !== false){
    $response = "Engine issue detected. Check oil level and cooling system.";
}

echo $response;
?>