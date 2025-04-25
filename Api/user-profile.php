<?php
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  header("Access-Control-Allow-Origin: *");
  header("Access-Control-Allow-Headers: Content-Type");
  header("Access-Control-Allow-Methods: POST, OPTIONS");
  http_response_code(200);
  exit();
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

// Connect to DB
$conn = new mysqli("localhost", "root", "", "metafit");
if ($conn->connect_error) {
  http_response_code(500);
  echo json_encode(["msg" => "Database connection failed"]);
  exit();
}

// Receive data
$data = json_decode(file_get_contents("php://input"), true);

// Get required fields
$email = $conn->real_escape_string($data["email"]); // Email is used to find the user
$dob = $data["dob"];
$age = (int)$data["age"];
$height = (float)$data["height"];
$weight = (float)$data["weight"];
$target_weight = (float)$data["targetWeight"];
$activity = (float)$data["activityLevel"];
$cal_current = (int)$data["calorieCurrent"];
$cal_target = (int)$data["calorieTarget"];

// Update the user profile
$stmt = $conn->prepare("
  UPDATE users SET
    dob=?, age=?, height=?, weight=?, target_weight=?,
    activity_level=?, calorie_current=?, calorie_target=?
  WHERE email=?
");
$stmt->bind_param(
  "sddddddds",
  $dob, $age, $height, $weight, $target_weight,
  $activity, $cal_current, $cal_target,
  $email
);

if ($stmt->execute()) {
  echo json_encode(["msg" => "User profile updated successfully"]);
} else {
  http_response_code(500);
  echo json_encode(["msg" => "Failed to update user profile"]);
}

$stmt->close();
$conn->close();
?>
