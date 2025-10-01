<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$sessionToken = '';

if ($method === 'GET') {
    $sessionToken = $_GET['session_token'] ?? '';
} elseif ($method === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    $sessionToken = $input['session_token'] ?? '';
}

if (empty($sessionToken)) {
    echo json_encode(['success' => false, 'message' => 'Session token required']);
    exit;
}

try {
    $db = new Database();
    
    $sessionData = $db->redis_conn->get("session:{$sessionToken}");
    
    if (!$sessionData) {
        echo json_encode(['success' => false, 'message' => 'Invalid session']);
        exit;
    }
    
    $session = json_decode($sessionData, true);
    $userId = $session['user_id'];
    
    if ($method === 'GET') {
        $stmt = $db->mysql_conn->prepare("SELECT username, email FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit;
        }
        
        try {
            $filter = ['user_id' => (int)$userId];
            $options = [];
            
            $query = new MongoDB\Driver\Query($filter, $options);
            $cursor = $db->mongodb_conn->executeQuery("{$db->mongodb_database}.profiles", $query);
            $profiles = $cursor->toArray();
            
            $profileData = !empty($profiles) ? (array)$profiles[0] : [];
            
            $responseData = array_merge($user, [
                'age' => $profileData['age'] ?? '',
                'dob' => $profileData['dob'] ?? '',
                'contact' => $profileData['contact'] ?? '',
                'address' => $profileData['address'] ?? ''
            ]);
            
            echo json_encode(['success' => true, 'data' => $responseData]);
            
        } catch (Exception $e) {
            $responseData = $user;
            echo json_encode(['success' => true, 'data' => $responseData]);
        }
        
    } elseif ($method === 'PUT') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $profileData = [
            'user_id' => (int)$userId,
            'age' => (int)($input['age'] ?? 0),
            'dob' => $input['dob'] ?? '',
            'contact' => $input['contact'] ?? '',
            'address' => $input['address'] ?? '',
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ];
        
        try {
            $filter = ['user_id' => (int)$userId];
            $update = ['$set' => $profileData];
            $options = ['upsert' => true];
            
            $bulk = new MongoDB\Driver\BulkWrite();
            $bulk->update($filter, $update, $options);
            
            $result = $db->mongodb_conn->executeBulkWrite("{$db->mongodb_database}.profiles", $bulk);
            
            echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
        }
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error occurred']);
}
?>