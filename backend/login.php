<?php
require __DIR__ . '/db_connection.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (empty($data['username']) || empty($data['password'])) {
        throw new Exception('Usuario y contraseña requeridos');
    }

    // Obtener usuario
    $stmt = $conn->prepare("
        SELECT u.*, t.token 
        FROM usuarios u
        JOIN tokens t ON u.token_id = t.id
        WHERE u.username = ?
    ");
    $stmt->bind_param("s", $data['username']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Credenciales inválidas');
    }

    $user = $result->fetch_assoc();

    // Verificar contraseña
    if (!password_verify($data['password'], $user['password'])) {
        throw new Exception('Credenciales inválidas');
    }

    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'username' => $user['username'],
        'email' => $user['email'],
        'squirrelmail_url' => "http://localhost/squirrelmail?token={$user['token']}",
        'moodle_url' => "http://localhost/moodle?token={$user['token']}"
    ]);

} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['error' => $e->getMessage()]);
}
?>