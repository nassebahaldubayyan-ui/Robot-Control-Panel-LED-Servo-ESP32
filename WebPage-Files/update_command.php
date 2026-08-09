<?php
include 'db.php';

$type = isset($_POST['type']) ? $_POST['type'] : '';
$value = isset($_POST['value']) ? $_POST['value'] : '';

$allowed_types = ['command', 'led', 'servo'];
if (!in_array($type, $allowed_types)) {
    http_response_code(400);
    echo json_encode(["error" => "invalid type"]);
    exit;
}

if ($type === 'led') {
    if ($value !== '0' && $value !== '1') {
        http_response_code(400);
        echo json_encode(["error" => "invalid led value"]);
        exit;
    }
    $stmt = $conn->prepare("UPDATE robot_state SET led_state = ?, updated_at = NOW() WHERE id = 1");
    $stmt->bind_param("s", $value);
}
elseif ($type === 'servo') {
    $allowed_servo = ['backward', 'normal', 'forward'];
    if (!in_array($value, $allowed_servo)) {
        http_response_code(400);
        echo json_encode(["error" => "invalid servo value"]);
        exit;
    }
    $stmt = $conn->prepare("UPDATE robot_state SET servo_state = ?, updated_at = NOW() WHERE id = 1");
    $stmt->bind_param("s", $value);
}
else { // 'command'
    $stmt = $conn->prepare("UPDATE robot_state SET command = ?, updated_at = NOW() WHERE id = 1");
    $stmt->bind_param("s", $value);
}

if ($stmt->execute()) {
    // ---- Write current combined state to a static text file ----
    $result = $conn->query("SELECT led_state, servo_state FROM robot_state WHERE id = 1");
    if ($row = $result->fetch_assoc()) {
        $led = $row['led_state'] == 1 ? "1" : "0";

        $servoLetter = "N";
        if ($row['servo_state'] === "backward") $servoLetter = "B";
        elseif ($row['servo_state'] === "forward") $servoLetter = "F";
        elseif ($row['servo_state'] === "normal") $servoLetter = "N";

        file_put_contents("state.txt", $led . $servoLetter);
    }

    echo json_encode(["status" => "ok", "type" => $type, "value" => $value]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "db update failed"]);
}

$stmt->close();
$conn->close();
?>