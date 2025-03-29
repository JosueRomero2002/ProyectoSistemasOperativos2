<?php
header('Content-Type: application/json');


if (php_sapi_name() !== 'cli') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

#if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
 #   http_response_code(204);
  #  exit;
#}

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

$Original = [];
$Ordenado = [];

// Espacio, coma y punto
$Original[] = Modular(32, 11413, 3533);
$Original[] = Modular(44, 11413, 3533);
$Original[] = Modular(46, 11413, 3533);

for ($i = 48; $i <= 57; $i++) {
    $Original[] = Modular($i, 11413, 3533);
}

for ($i = 65; $i <= 90; $i++) {
    $Original[] = Modular($i, 11413, 3533);
}

for ($i = 97; $i <= 122; $i++) {
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

$supabase_url = 'https://wjnjyfzttifbegqzouoa.supabase.co';
$supabase_key = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Indqbmp5Znp0dGlmYmVncXpvdW9hIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NDI0MjQyNzMsImV4cCI6MjA1ODAwMDI3M30.8DY-Y5huHdK_fZ7GjvfKvE9aBgeQ-xHZ2FQlVml_OQI';

$data = json_encode(['token' => $Resultado]);

$ch = curl_init($supabase_url . '/rest/v1/token');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $data,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'apikey: ' . $supabase_key,
        'Authorization: Bearer ' . $supabase_key,
        'Prefer: return=minimal'
    ]
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code == 201 || $http_code == 200) {
    echo json_encode([
        'numero' => $Codigo,
        'token' => $Resultado,
        'mensaje' => 'Token guardado en Supabase correctamente'
    ]);
} else {
    echo json_encode([
        'error' => 'Error al insertar en Supabase',
        'detalle' => $response
    ]);
}
?>