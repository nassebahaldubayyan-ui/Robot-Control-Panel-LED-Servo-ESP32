<?php
include 'db.php';

$sql = "SELECT led_state, servo_state FROM robot_state WHERE id = 1";
$result = $conn->query($sql);

if ($row = $result->fetch_assoc()) {
    $led = $row['led_state'] == 1 ? "1" : "0";

    $servoLetter = "N"; // default normal
    if ($row['servo_state'] === "backward") $servoLetter = "B";
    elseif ($row['servo_state'] === "forward") $servoLetter = "F";
    elseif ($row['servo_state'] === "normal") $servoLetter = "N";

    echo $led . $servoLetter;
} else {
    http_response_code(500);
    echo "ERR";
}

$conn->close();
?>