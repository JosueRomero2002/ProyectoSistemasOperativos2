<?php
header('Content-Type: application/json');

// Configuración de CORS
if (php_sapi_name() !== 'cli') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

// Configuración de la base de datos
$db_host = 'localhost';
$db_user = 'tu_usuario_db';
$db_pass = 'tu_contraseña_db';
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

// Generación del token
try {
    $Original = [];
    $Ordenado = [];

    // Caracteres permitidos
    $Original[] = Modular(32, 11413, 3533);  // Espacio
    $Original[] = Modular(44, 11413, 3533);  // Coma
    $Original[] = Modular(46, 11413, 3533);  // Punto

    // Números (48-57)
    for ($i = 48; $i <= 57; $i++) {
        $Original[] = Modular($i, 11413, 3533);
    }

    // Mayúsculas (65-90)
    for ($i = 65; $i <= 90; $i++) {
        $Original[] = Modular($i, 11413, 3533);
    }

    // Minúsculas (97-122)
    for ($i = 97; $i <= 122; $i++) {
        $Original[] = Modular($i, 11413, 3533);
    }

    $Ordenado = $Original;
    sort($Ordenado);

    // Generar código y token
    $Codigo = rand(100000, 999999);
    $Texto = strval($Codigo);
    $Resultado = "";

    for ($i = 0; $i < strlen($Texto); $i++) {
        $Letra = $Texto[$i];
        $Numero = ord($Letra);
        $Numero = Modular($Numero, 11413, 3533);
        
        $indice = array_search($Numero, $Original);
        if ($indice !== false) {
            $Numero = $Ordenado[$indice];
            $Numero = Modular($Numero, 11413, 6597);
            $Letra = chr($Numero);
            $Resultado .= $Letra;
        }
    }

    // Conexión a MySQL
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    if ($conn->connect_error) {
        throw new Exception('Error de conexión a la base de datos: ' . $conn->connect_error);
    }

    // Insertar en base de datos
    $stmt = $conn->prepare("INSERT INTO tokens (codigo, token) VALUES (?, ?)");
    $stmt->bind_param("ss", $Codigo, $Resultado);
    
    if (!$stmt->execute()) {
        throw new Exception('Error al guardar el token: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();

    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'numero' => $Codigo,
        'token' => $Resultado,
        'mensaje' => 'Token generado y almacenado correctamente'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'detalle' => 'Error en el servidor al generar el token'
    ]);
}
?>