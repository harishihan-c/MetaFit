<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

// If it's a preflight request, return immediately
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    // Exit after sending the necessary headers
    exit(0);
}

// DB setup
$conn = new mysqli("localhost", "root", "", "metafit");
if ($conn->connect_error) {
  http_response_code(500);
  echo json_encode(["error" => "Database connection failed"]);
  exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$email = $conn->real_escape_string($data["email"] ?? '');

if (!$email) {
  http_response_code(400);
  echo json_encode(["error" => "Email is required"]);
  exit;
}

$sql = "SELECT name, age, height, weight, target_weight, calorie_current, calorie_target FROM users WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  echo json_encode($result->fetch_assoc());
} else {
  http_response_code(404);
  echo json_encode(["error" => "User not found"]);
}

$conn->close();
?>
