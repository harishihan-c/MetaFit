<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "metafit");
if ($conn->connect_error) {
  http_response_code(500);
  echo json_encode(["msg" => "Database connection failed"]);
  exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$name = $conn->real_escape_string($data["name"]);
$email = $conn->real_escape_string($data["email"]);
$password = password_hash($data["password"], PASSWORD_BCRYPT);

// Check if email exists
$check = $conn->query("SELECT id FROM users WHERE email='$email'");
if ($check->num_rows > 0) {
  http_response_code(400);
  echo json_encode(["msg" => "Email already registered"]);
  exit();
}

// Insert new user
$stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $password);

if ($stmt->execute()) {
  echo json_encode(["msg" => "Registration successful"]);
} else {
  http_response_code(500);
  echo json_encode(["msg" => "Registration failed"]);
}

$stmt->close();
$conn->close();
?>
