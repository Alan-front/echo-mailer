<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'conexion.php'; 

$input = file_get_contents('php://input');
$data = json_decode($input, true);

file_put_contents('debug.txt', print_r($data, true));

if (!isset($data['id_respuesta'], $data['audio'], $data['video'], $data['ficha'])) {
  echo json_encode(['error' => 'Faltan datos obligatorios']);
  exit;
}

$audio = $data['audio'];
$video = $data['video'];
$ficha = $data['ficha'];
$idRespuesta = $data['id_respuesta'];

$conn = conectar();
if (!$conn) {
  echo json_encode(['error' => 'Error de conexión']);
  exit;
}

$sql = "UPDATE bandejas_campaña SET inc_audio = ?, inc_video = ?, inc_ficha = ?, estado = 0 WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt->execute([$audio, $video, $ficha, $idRespuesta])) {
  echo json_encode(['ok' => true, 'mensaje' => 'Respuesta programada correctamente']);
} else {
  echo json_encode(['error' => 'No se pudo actualizar', 'detalle' => $stmt->errorInfo()]);
}

$stmt = null;
$conn = null;
?>