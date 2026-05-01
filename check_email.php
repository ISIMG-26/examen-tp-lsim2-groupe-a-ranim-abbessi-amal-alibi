<?php
header('Content-Type: application/json');
session_start();
include '../includes/config.php';

$response = ['exists' => false];

if (isset($_POST['email'])) {
    $email = $_POST['email'];
    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $response['exists'] = true;
    }
}

echo json_encode($response);
?>