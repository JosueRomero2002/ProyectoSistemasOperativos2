<?php
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
    
    // Validación de datos
    if (empty($data['username']) || empty($data['password'])) {
        throw new Exception('Usuario y contraseña requeridos');
    }

    $username = filter_var($data['username'], FILTER_SANITIZE_STRING);
    $password = filter_var($data['password'], FILTER_SANITIZE_STRING);

    // Consulta a Supabase
    $supabase_url = 'https://wjnjyfzttifbegqzouoa.supabase.co/rest/v1/users?select=*&username=eq.' . urlencode($username);
    $supabase_key = 'TU_API_KEY';

    $ch = curl_init($supabase_url);
    curl_setopt_array($ch, [
        CURLOPT_HTTPHEADER => [
            'apikey: ' . $supabase_key,
            'Authorization: Bearer ' . $supabase_key
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FAILONERROR => true
    ]);

    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        throw new Exception('Error de conexión: ' . curl_error($ch));
    }

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $users = json_decode($response, true);

    if (empty($users)) {
        throw new Exception('Credenciales inválidas');
    }

    if (!password_verify($password, $users[0]['password_hash'])) {
        throw new Exception('Credenciales inválidas');
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['error' => $e->getMessage()]);
}