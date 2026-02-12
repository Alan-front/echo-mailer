<?php
include '../config/conexion.php';
$conn = conectar();

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}


$input = json_decode(file_get_contents("php://input"), true);

if (
    isset($input['campana_id']) &&
    isset($input['tipo']) &&
    isset($input['url'])
) {
    $campana_id = $input['campana_id'];
    $tipo = $input['tipo'];
    $idioma = $input['idioma'] ?? null;
    $url = $input['url'];

    $stmt = $conn->prepare("INSERT INTO archivos (campaña_id, tipo, idioma, url) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $campana_id, $tipo, $idioma, $url);

    if ($stmt->execute()) {
        echo json_encode(["mensaje" => "Archivo guardado"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error al guardar"]);
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(400);
    echo json_encode(["error" => "Datos incompletos"]);
}
?>
