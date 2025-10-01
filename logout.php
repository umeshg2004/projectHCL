<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$sessionToken = $_POST['session_token'] ?? '';

if (empty($sessionToken)) {
    echo json_encode(['success' => false, 'message' => 'Session token required']);
    exit;
}

try {
    $db = new Database();
    $db->redis_conn->del("session:{$sessionToken}");
    echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Logout failed']);
}
?>