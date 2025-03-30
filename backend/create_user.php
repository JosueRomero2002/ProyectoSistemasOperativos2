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

    // Validaciones
    if (empty($data['username']) || empty($data['password']) || empty($data['email'])) {
        throw new Exception('Todos los campos son requeridos');
    }

    // Generar token
    require __DIR__ . '/generar_token.php';


    // Hashear contraseña
    $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);

    // Insertar usuario con token
  // Modificar la inserción
// Después de generar el token
$token_data = generarToken();

// Insertar usuario con el token_id correcto
$stmt = $conn->prepare("INSERT INTO usuarios 
    (username, password, email, token_id) 
    VALUES (?, ?, ?, ?)");
$stmt->bind_param("sssi", 
    $data['username'],
    $password_hash,
    $data['email'],
    $token_data['token_id'] // Usar el ID del token
);
    if (!$stmt->execute()) {
        throw new Exception('Error al crear usuario: ' . $stmt->error);
    }

    echo json_encode([
        'success' => true,
        'token' => $token_data['token']
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>