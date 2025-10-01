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

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Username and password are required']);
    exit;
}

try {
    $db = new Database();
    
    $stmt = $db->mysql_conn->prepare("SELECT id, username, email, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user || !password_verify($password, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
        exit;
    }
    
    $sessionToken = bin2hex(random_bytes(32));
    $sessionData = json_encode([
        'user_id' => $user['id'],
        'username' => $user['username'],
        'email' => $user['email'],
        'created_at' => time()
    ]);
    
    $db->redis_conn->setex("session:{$sessionToken}", 3600, $sessionData);
    
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'session_token' => $sessionToken,
        'username' => $user['username']
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Login failed']);
}
?>