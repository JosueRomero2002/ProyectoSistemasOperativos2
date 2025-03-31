<?php
require __DIR__ . '/db_connection.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Max-Age: 86400");

// Manejar preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (empty($data['token'])) {
        throw new Exception('Token requerido');
    }

    $stmt = $conn->prepare("
        SELECT u.* 
        FROM usuarios u
        JOIN tokens t ON u.token_id = t.id
        WHERE t.token = ? AND t.expiration > NOW()
    ");
    $stmt->bind_param("s", $data['token']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Token inválido o expirado');
    }

    $user = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'user' => [
            'username' => $user['username'],
            'email' => $user['email'],
            'token' => $data['token']
        ]
    ]);

} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['error' => $e->getMessage()]);
}
?>