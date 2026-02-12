<?php

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    exit(0);
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");

require_once '../config/conexion.php';

$data = json_decode(file_get_contents("php://input"));
$id = $data->id ?? null;

if ($id) {
    $pdo = conectar();
    $stmt = $pdo->prepare("DELETE FROM archivos WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(["mensaje" => "Eliminado"]);
} else {
    http_response_code(400);
    echo json_encode(["error" => "ID no válido"]);
}
?>
