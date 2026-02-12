<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

// Responder rápido si es preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

header('Content-Type: application/json');
include '../config/conexion.php';
$con = conectar();

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id_campaña']) || !is_numeric($data['id_campaña'])) {
    echo json_encode(["ok" => false, "error" => "ID de campaña inválido"]);
    exit;
}

$id_campaña = intval($data['id_campaña']);

$sql = "UPDATE bandejas_campaña
        SET estado = NULL,
            inc_audio = 0,
            inc_video = 0,
            inc_ficha = 0
        WHERE id_campaña = ?";

$stmt = $con->prepare($sql);
$stmt->bind_param("i", $id_campaña);

if ($stmt->execute()) {
    echo json_encode(["ok" => true, "id_campaña" => $id_campaña]);
} else {
    echo json_encode(["ok" => false, "error" => $stmt->error]);
}

$stmt->close();
$con->close();
?>
