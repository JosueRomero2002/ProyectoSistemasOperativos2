<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Configuración de la base de datos
$db_host = 'localhost';
$db_user = 'phpmyadmin';
$db_pass = 'josue';
$db_name = 'sistema_usuarios';

// Funciones de encriptación
function de2bi($x) {
    $b = [];
    while ($x > 0) {
        $mod = $x % 2;
        $b[] = $mod;
        $x = (int)($x / 2);
    }
    return $b;
}

function Modular($b, $N, $n) {
    $a = 1;
    $b = $b % $N;
    $d = de2bi($n);
    for ($j = 0; $j < count($d); $j++) {
        if ($d[$j] == 1) {
            $a = ($a * $b) % $N;
        }
        $b = $b * $b;
        $b = $b % $N;
    }
    return $a % $N;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validar datos de entrada
    if (empty($data['username']) || empty($data['password'])) {
        throw new Exception('Usuario y contraseña requeridos');
    }

    $username = filter_var($data['username'], FILTER_SANITIZE_STRING);
    $password = filter_var($data['password'], FILTER_SANITIZE_STRING);

    // Conexión a la base de datos
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
  // Verifica la conexión
if ($conn->connect_error) {
    error_log("Error de conexión: " . $conn->connect_error);
    die(json_encode(['error' => 'Error de base de datos']));
}

// Modifica la consulta para debugging
error_log("Consulta ejecutada: SELECT u.password, t.codigo FROM users u INNER JOIN tokens t ON u.token_id = t.id WHERE u.username = '$username'");

// Agrega logging de la contraseña
error_log("Contraseña recibida: $password");
error_log("Contraseña almacenada: " . $user['password']);
error_log("Código usado: " . $codigo);
    // Obtener usuario y token asociado
    $stmt = $conn->prepare("
        SELECT u.password, t.codigo 
        FROM users u
        INNER JOIN tokens t ON u.token_id = t.id
        WHERE u.username = ?
    ");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Credenciales inválidas');
    }

    $user = $result->fetch_assoc();
    $stored_password = $user['password'];
    $codigo = $user['codigo'];

    // Encriptar contraseña recibida
    $encrypted_password = '';
    for ($i = 0; $i < strlen($password); $i++) {
        $ascii = ord($password[$i]);
        $encrypted_char = Modular($ascii, 11413, $codigo);
        $encrypted_password .= chr($encrypted_char);
    }
    $encrypted_password = base64_encode($encrypted_password);

    // Verificar contraseña
    if ($encrypted_password !== $stored_password) {
        throw new Exception('Credenciales inválidas');
    }

    // Crear sesión (opcional)
    session_start();
    $_SESSION['user'] = [
        'username' => $username,
        'logged_in' => true,
        'expire' => time() + 3600 // 1 hora
    ];

    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'redirect' => 'http://localhost/squirrelmail/src/redirect.php',
        'user' => [
            'username' => $username,
            'email' => $user['email'] // Si está disponible
        ]
    ]);

} catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'detalle' => 'Error en el proceso de autenticación'
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>