<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require 'conexion_prom.php';

$importar = "SELECT test_email, name, media_type, music_genre, secondary_language FROM media_contacts WHERE active = 1";
$resultado = mysqli_query($conexion, $importar);

if (!$resultado) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la consulta: ' . mysqli_error($conexion)]);
    exit;
}

$destinatarios = [];
while ($fila = mysqli_fetch_assoc($resultado)) {
    $destinatarios[] = $fila;
}

echo json_encode($destinatarios);

mysqli_close($conexion);
?>