<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

$conn = new mysqli('localhost', 'root', '', 'chat_db');

if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed']));
}

$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'];
$password = password_hash($data['password'], PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (username, password, settings) VALUES (?, ?, ?)");
$settings = json_encode([
    'theme' => 'light',
    'notifications' => true,
    'language' => 'en'
]);

$stmt->bind_param("sss", $username, $password, $settings);

try {
    $stmt->execute();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    if ($e->getCode() == 1062) { // Duplicate entry
        echo json_encode(['error' => 'Username already exists']);
    } else {
        echo json_encode(['error' => 'Registration failed']);
    }
}

$stmt->close();
$conn->close();
?>