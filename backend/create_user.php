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

    if (empty($data['username']) || empty($data['password']) || empty($data['email'])) {
        throw new Exception('Todos los campos son requeridos');
    }

    require __DIR__ . '/generar_token.php';

  
$token_data = generarToken();
$codigo_original = $token_data['token']; 
$password_salt = $codigo_original . $data['password'];
$password_hash = password_hash($password_salt, PASSWORD_DEFAULT);

   // $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO usuarios (username, password, email, token_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $data['username'], $password_hash, $data['email'], $token_data['token_id']);
    
    if (!$stmt->execute()) {
        throw new Exception('Error al crear usuario: ' . $stmt->error);
    }

    $username = escapeshellarg($data['username']);
    $password = escapeshellarg($data['password']);

    exec("sudo useradd -m -s /bin/bash $username");
    exec("echo $username:$password | sudo chpasswd");

    // Crear usuario en Moodle
    /*require_once('/var/www/html/moodle/config.php');
    require_once($CFG->dirroot . '/user/lib.php');

    $moodle_user = [
        'username'   => $data['username'],
        'password'   => hash_internal_user_password($data['password']),
        'firstname'  => explode('@', $data['email'])[0],
        'lastname'   => 'Usuario',
        'email'      => $data['email'],
        'auth'       => 'manual',
        'mnethostid' => 1,
        'confirmed'  => 1
    ];
    try {
        $user_id = user_create_user((object)$moodle_user);
        if (!$user_id) {
            throw new Exception('Error al crear usuario en Moodle');
        }
    } catch (Exception $e) {
        error_log("Error al crear usuario en Moodle: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Fallo al crear usuario en Moodle']);
        exit;
    }
    */

    echo json_encode([
        'success' => true,
        'token' => $token_data['token']
        //,
       // 'moodle_user_id' => $user_id
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
