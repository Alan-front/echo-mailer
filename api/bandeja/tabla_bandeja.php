<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Manejar preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

include '../config/conexion.php';

try {
    $con = conectar();
    
    if (!$con) {
        throw new Exception("Error de conexión a la base de datos");
    }

    // Leer los datos del POST (JSON)
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data || !isset($data['id'])) {
        throw new Exception("ID de campaña no proporcionado");
    }
    
    $idCampania = intval($data['id']);
    
    // Usar prepared statement para evitar SQL injection
    $stmt = $con->prepare("SELECT * FROM bandejas_campaña WHERE id_campaña = ?");
    $stmt->bind_param("i", $idCampania);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $dataFiles = [];
    
    while($row = $result->fetch_assoc()){
        $dataFiles[] = $row;
    }
    
    $stmt->close();
    $con->close();
    
    echo json_encode($dataFiles, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
?>