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

session_start();  // 👈 Add this!

$conn = new mysqli("localhost", "root", "", "metafit");
if ($conn->connect_error) {
  http_response_code(500);
  echo json_encode(["msg" => "Database connection failed"]);
  exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$email = $conn->real_escape_string($data["email"]);
$password = $data["password"];

// Find user
$result = $conn->query("SELECT * FROM users WHERE email='$email'");
if ($result->num_rows === 0) {
  http_response_code(400);
  echo json_encode(["msg" => "Invalid email or password"]);
  exit();
}

$user = $result->fetch_assoc();
if (!password_verify($password, $user["password"])) {
  http_response_code(400);
  echo json_encode(["msg" => "Invalid email or password"]);
  exit();
}

// ✅ SAVE LOGIN INFO IN SESSION
$_SESSION['email'] = $user["email"];
$_SESSION['username'] = $user["name"];
$_SESSION['user_id'] = $user["id"];

// On success
echo json_encode([
  "msg" => "Login successful",
  "user" => [
    "id" => $user["id"],
    "name" => $user["name"],
    "email" => $user["email"]
  ]
]);

$conn->close();
?>
