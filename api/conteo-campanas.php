<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include 'conexion.php';
$con = conectar();

// Consulta SQL
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

GROUP BY c.id, c.artista, c.nombre_lanzamiento, c.tipo_de_lanzamiento
ORDER BY c.id DESC
";

$result = mysqli_query($con, $sql);

$data = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // convertir fechas_envio en array
        $fechas = [];
        if (!empty($row["fechas_envio"])) {
            $fechas = explode(",", $row["fechas_envio"]);
        }

        $data[] = [
            "id_campaña"       => $row["id_campaña"],
            "artista"          => $row["artista"],
            "nombre_lanzamiento"=> $row["nombre_lanzamiento"],
            "tipo_de_lanzamiento"=> $row["tipo_de_lanzamiento"],
            "total_enviados"   => (int)$row["total_enviados"],
            "fechas_envio"     => $fechas,
            "total_respuestas" => (int)$row["total_respuestas"]
        ];
    }
}

echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
