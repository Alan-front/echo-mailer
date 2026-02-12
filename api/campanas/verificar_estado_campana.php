<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include '../config/conexion.php';
$con = conectar();

// obtener el id de la campaña
$campana_id = isset($_GET['campana_id']) ? intval($_GET['campana_id']) : 0;

if ($campana_id === 0) {
    echo json_encode([
        "success" => false,
        "error" => "ID de campaña no proporcionado"
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// consultar cuántos enviados tienen estado = 0 (pendientes)
$sql = "SELECT COUNT(*) as pendientes 
        FROM enviados 
        WHERE campaña_id = ? AND estado = 0";

$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $campana_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

$pendientes = (int)$row['pendientes'];

// si hay pendientes, estado = 3 (En proceso)
// si no hay pendientes, estado = 1 (Activa)
$estado_calculado = ($pendientes > 0) ? 3 : 1;

echo json_encode([
    "success" => true,
    "campana_id" => $campana_id,
    "estado_calculado" => $estado_calculado,
    "pendientes" => $pendientes
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

mysqli_stmt_close($stmt);
mysqli_close($con);
?>