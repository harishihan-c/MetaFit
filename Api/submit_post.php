<?php
session_start();
header('Content-Type: application/json');

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "metafit";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed"]);
    exit();
}

if (!isset($_SESSION['email']) || !isset($_SESSION['username'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$content = $conn->real_escape_string($data['content']);

$email = $conn->real_escape_string($_SESSION['email']);
$username = $conn->real_escape_string($_SESSION['username']);

$sql = "INSERT INTO forum_posts (username, email, content) VALUES ('$username', '$email', '$content')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => $conn->error]);
}

$conn->close();
?>
