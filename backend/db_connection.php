<?php
$db_host = 'localhost';
$db_user = 'phpmyadmin';
$db_pass = 'josue';
$db_name = 'mysql';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    header('Content-Type: application/json');
    die(json_encode(['error' => 'Error de conexión a BD: ' . $conn->connect_error]));
}

// Configurar charset
$conn->set_charset('utf8mb4');
?>