<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");


require_once '../config/conexion.php'; 

// obtener datos JSON
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Depuración (puedes borrar luego)
file_put_contents('debug.txt', print_r($data, true));

// Verificar datos
if (!isset($data['id'], $data['tipo'], $data['url'])) {
  echo json_encode(['error' => 'Faltan datos obligatorios']);
  exit;
}

$id = $data['id'];
$tipo = $data['tipo'];
$url = $data['url'];
$idioma = isset($data['idioma']) ? $data['idioma'] : null;

// Conexión
$conn = conectar();
if (!$conn) {
  echo json_encode(['error' => 'Error de conexión']);
  exit;
}

// Preparar SQL
if ($tipo === 'ficha') {
  $sql = "UPDATE archivos SET tipo = ?, idioma = ?, url = ? WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sssi", $tipo, $idioma, $url, $id);
} else {
  $sql = "UPDATE archivos SET tipo = ?, idioma = NULL, url = ? WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ssi", $tipo, $url, $id);
}

// Ejecutar
if ($stmt->execute()) {
  echo json_encode(['ok' => true]);
} else {
  echo json_encode(['error' => 'No se pudo actualizar', 'detalle' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
