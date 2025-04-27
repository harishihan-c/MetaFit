<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");

// Connect to DB
$conn = new mysqli('localhost', 'root', '', 'metafit');
if ($conn->connect_error) {
  echo json_encode(['success' => false, 'message' => 'DB connection failed']);
  exit;
}

// Get incoming data
$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['name'], $data['email'], $data['password'])) {
  echo json_encode(['success' => false, 'message' => 'Invalid input']);
  exit;
}

$name = $conn->real_escape_string($data['name']);
$email = $conn->real_escape_string($data['email']);
$password = password_hash($data['password'], PASSWORD_DEFAULT);

// Check if email already exists
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
  echo json_encode(['success' => false, 'message' => 'Email already exists']);
  $check->close();
  $conn->close();
  exit;
}
$check->close();

// Insert new user
$stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $password);

if ($stmt->execute()) {
  echo json_encode(['success' => true, 'message' => 'User registered']);
} else {
  echo json_encode(['success' => false, 'message' => 'Registration failed']);
}

$stmt->close();
$conn->close();
?>
