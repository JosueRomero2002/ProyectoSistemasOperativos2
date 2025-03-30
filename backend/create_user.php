<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// Validar datos
if (empty($data['username']) || empty($data['password']) || empty($data['email'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos incompletos']);
    exit;
}

// Generar token
$tokenData = json_decode(file_get_contents('http://localhost:8002/generar_token.php'), true);
if (isset($tokenData['error'])) {
    http_response_code(500);
    echo json_encode(['error' => 'Error generando token: ' . $tokenData['error']]);
    exit;
}

$codigo = $tokenData['numero'];
$token = $tokenData['token'];

// Encriptar contraseña con el código
function encriptarContrasena($password, $codigo) {
    $encriptada = '';
    for ($i = 0; $i < strlen($password); $i++) {
        $ascii = ord($password[$i]);
        $encriptado = Modular($ascii, 11413, $codigo);
        $encriptada .= chr($encriptado);
    }
    return base64_encode($encriptada);
}

$passwordEncriptada = encriptarContrasena($data['password'], $codigo);

// Conexión a BD SquirrelMail
$connSquirrel = new mysqli('localhost', 'root', 'josue', 'squirrelmail_db');
$stmt = $connSquirrel->prepare("INSERT INTO usuarios (username, password, email, token) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $data['username'], $passwordEncriptada, $data['email'], $token);

// Conexión a BD Moodle
$connMoodle = new mysqli('localhost', 'root', 'josue', 'moodle_db');
$time = time();
$stmtMoodle = $connMoodle->prepare("INSERT INTO mdl_user (username, password, email, confirmed, timecreated) VALUES (?, ?, ?, 1, ?)");
$stmtMoodle->bind_param("sssi", $data['username'], $passwordEncriptada, $data['email'], $time);

// Ejecutar inserciones
if (!$stmt->execute() || !$stmtMoodle->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'Error creando usuario']);
} else {
    echo json_encode(['success' => true]);
}

$stmt->close();
$stmtMoodle->close();
$connSquirrel->close();
$connMoodle->close();
?>