<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include 'conexion.php';
$con = conectar();

// Obtener el ID de la campaña desde GET
$campana_id = isset($_GET['campana_id']) ? (int)$_GET['campana_id'] : null;

if (!$campana_id) {
    echo json_encode(['error' => true, 'message' => 'ID de campaña requerido']);
    exit;
}

// Consulta SQL para una campaña específica
$sql = "
SELECT 
    c.id AS id_campaña,
    c.artista,
    c.nombre_lanzamiento,
    c.tipo_de_lanzamiento,

    COUNT(DISTINCT e.id) AS total_enviados,
    GROUP_CONCAT(DISTINCT e.fecha_envio ORDER BY e.fecha_envio ASC) AS fechas_envio,
    COUNT(DISTINCT b.id) AS total_respuestas

FROM campañas c
LEFT JOIN enviados e 
    ON e.campaña_id = c.id
LEFT JOIN bandejas_campaña b 
    ON b.id_campaña = c.id

WHERE c.id = ?

GROUP BY c.id, c.artista, c.nombre_lanzamiento, c.tipo_de_lanzamiento
";

$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $campana_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    // Convertir fechas_envio en array
    $fechas = [];
    if (!empty($row["fechas_envio"])) {
        $fechas = explode(",", $row["fechas_envio"]);
    }

    // Consulta separada para obtener respuestas por fecha
    $sql_respuestas = "
    SELECT 
        DATE(fecha) as fecha,
        COUNT(*) as cantidad
    FROM bandejas_campaña 
    WHERE id_campaña = ? 
    GROUP BY DATE(fecha)
    ORDER BY fecha
    ";

    $stmt_resp = mysqli_prepare($con, $sql_respuestas);
    mysqli_stmt_bind_param($stmt_resp, "i", $campana_id);
    mysqli_stmt_execute($stmt_resp);
    $result_resp = mysqli_stmt_get_result($stmt_resp);

    $respuestas_por_fecha = [];
    while ($row_resp = mysqli_fetch_assoc($result_resp)) {
        $respuestas_por_fecha[$row_resp['fecha']] = (int)$row_resp['cantidad'];
    }

    $data = [
        "id_campaña"        => $row["id_campaña"],
        "artista"           => $row["artista"],
        "nombre_lanzamiento"=> $row["nombre_lanzamiento"],
        "tipo_de_lanzamiento"=> $row["tipo_de_lanzamiento"],
        "total_enviados"    => (int)$row["total_enviados"],
        "fechas_envio"      => $fechas,
        "total_respuestas"  => (int)$row["total_respuestas"],
        "respuestas_por_fecha" => $respuestas_por_fecha,

        "porcentaje_respuesta" => $row["total_enviados"] > 0 ? 
            round(($row["total_respuestas"] / $row["total_enviados"]) * 100, 2) : 0
    ];

    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['error' => true, 'message' => 'Campaña no encontrada']);
}

mysqli_close($con);
?>