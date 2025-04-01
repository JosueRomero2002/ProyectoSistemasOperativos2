<?php
require __DIR__ . '/db_connection.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

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

function generarToken() {
    global $conn;
    
    $Original = [];
    $Ordenado = [];

    // Generar caracteres
    $Original[] = Modular(32, 11413, 3533); // Espacio
    $Original[] = Modular(44, 11413, 3533); // Coma
    $Original[] = Modular(46, 11413, 3533); // Punto

    for ($i = 48; $i <= 57; $i++) { // Números
        $Original[] = Modular($i, 11413, 3533);
    }

    for ($i = 65; $i <= 90; $i++) { // Mayúsculas
        $Original[] = Modular($i, 11413, 3533);
    }

    for ($i = 97; $i <= 122; $i++) { // Minúsculas
        $Original[] = Modular($i, 11413, 3533);
    }

    $Ordenado = $Original;
    sort($Ordenado);

  
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


$stmt = $conn->prepare("INSERT INTO tokens (codigo, token) VALUES (?, ?)");
$stmt->bind_param("is", $Codigo, $Resultado);

if (!$stmt->execute()) {
    throw new Exception('Error al guardar token: ' . $stmt->error);
}

$token_id = $conn->insert_id; 

return [
    'numero' => $Codigo,
    'token' => $Resultado,
    'token_id' => $token_id 
];

}


if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    try {
        $token_data = generarToken();
        echo json_encode($token_data);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>