<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

$data = json_decode(file_get_contents('php://input'), true);

$name = $data['name'];
$email = $data['email'];
$password = $data['password'];

// Connect to DB and insert user
// Example:
$conn = new mysqli('localhost', 'root', '', 'metafit');
if ($conn->connect_error) {
  echo json_encode(['success' => false, 'message' => 'DB connection failed']);
  exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $hashedPassword);

if ($stmt->execute()) {
  echo json_encode(['success' => true, 'message' => 'User registered.']);
} else {
  echo json_encode(['success' => false, 'message' => 'Email already exists or error.']);
}
?>

<?php
session_start();

// After validating user credentials
$_SESSION['email'] = $email_from_database;
$_SESSION['username'] = $username_from_database;

// No redirect. Just a success message
echo json_encode(["success" => true]);
exit();
?>
