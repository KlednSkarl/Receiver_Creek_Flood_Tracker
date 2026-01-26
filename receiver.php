<?php
header("Content-Type: application/json");


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $logFile = 'data.log';
    if (!file_exists($logFile)) {
        echo json_encode(["error" => "No data available"]);
        exit;
    }
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $lastLine = end($lines);
    if (!$lastLine) {
        echo json_encode(["error" => "No data available"]);
        exit;
    }
    // Parse log line: "2026-01-26 08:12:23 | ESP32-001 | 28.5 | 67.02"
    [$datetime, $deviceId, $temperature, $humidity] = preg_split('/\s*\|\s*/', $lastLine);
    echo json_encode([
        "device_id" => $deviceId,
        "temperature" => $temperature,
        "humidity" => $humidity,
        "timestamp" => $datetime
    ]);
    exit;
}









// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "POST only"]);
    exit;
}

// Get raw input
$rawData = file_get_contents("php://input");

// Decode JSON
$data = json_decode($rawData, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid JSON"]);
    exit;
}

// Example expected fields
$deviceId = $data['device_id'] ?? null;
$temperature = $data['temperature'] ?? null;
$humidity = $data['humidity'] ?? null;

// Basic validation
if (!$deviceId || !$temperature) {
    http_response_code(422);
    echo json_encode(["error" => "Missing data"]);
    exit;
}

// Save to log file (for testing)
$log = date("Y-m-d H:i:s") . " | $deviceId | $temperature | $humidity\n";
file_put_contents("data.log", $log, FILE_APPEND);

// Response to ESP32
echo json_encode([
    "status" => "success",
    "message" => "Data received",
    "device ID" => $deviceId,
    "temperature" => $temperature,
    "Humidity" => $humidity



]);
