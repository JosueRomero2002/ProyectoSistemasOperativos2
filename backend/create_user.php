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


 
// Crear usuario
$username =  $data['username'];
$password =  $password_hash;

// Ejecutar useradd con home directory
exec("sudo useradd -m -s /bin/bash $username");

// Establecer contraseña (requiere privilegios sudo)
exec("echo '$username:$password' | sudo chpasswd");


//Crear usueario en Moodle
// Después de crear usuario en tu sistema
require_once('path/to/moodle/config.php'); // Incluir configuración de Moodle

$moodle_user = [
    'username' => $data['username'],
    'password' => hash_internal_user_password($data['password']),
    'firstname' => explode(' ', $data['fullname'])[0],
    'lastname' => explode(' ', $data['fullname'])[1] ?? 'User',
    'email' => $data['email'],
    'auth' => 'manual',
    'mnethostid' => 1 // ID del host principal
];

try {
    global $DB;
    $user_id = $DB->insert_record('user', (object)$moodle_user);
    
    // Asignar rol de usuario estándar
    $role = $DB->get_record('role', ['shortname' => 'user']);
    role_assign($role->id, $user_id, context_system::instance()->id);
    
    echo json_encode([
        'success' => true,
        'moodle_user_id' => $user_id
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error Moodle: ' . $e->getMessage()]);
}

$result = json_decode($response, true);
if (!empty($result['exception'])) {
    throw new Exception('Error Moodle: '.$result['message']);
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