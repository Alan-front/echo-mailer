<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit;
}



require_once 'conexion.php';
$conn = conectar();

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id']) || !isset($data['activa'])) {
  echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
  exit;
}

$id = intval($data['id']);
$activa = intval($data['activa']);

$stmt = $conn->prepare("UPDATE campañas SET activa = ? WHERE id = ?");
$stmt->bind_param("ii", $activa, $id);

if ($stmt->execute()) {
  echo json_encode(['success' => true]);
} else {
  echo json_encode(['success' => false, 'error' => 'Error al ejecutar query']);
}

$stmt->close();
$conn->close();
?>
