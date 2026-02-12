<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if (isset($_GET['pretty'])) {
    header("Content-Type: text/plain");
}

// Manejar preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

include '../config/conexion.php';

$con = conectar();

$id_campana = intval($_GET['id_campana']);




$sql = "SELECT 
            b.id_contacto, 
            m.email, 
            b.id, 
            b.asunto, 
            b.idioma, 
            b.mensaje,
            r.prefijo,
            r.respuesta,
            r.despedida,
            audio.url AS audio_url,
            video.url AS video_url,
            ficha.url AS ficha_url
        FROM bandejas_campaña b
        LEFT JOIN media_contacts m 
            ON m.id = b.id_contacto 
        LEFT JOIN respuestas_plantillas r 
            ON CASE b.idioma
                WHEN 'French' THEN 'fr'
                WHEN 'Spanish' THEN 'esp'
                WHEN 'English' THEN 'en'
                WHEN 'Korean' THEN 'ko'
                WHEN 'Russian' THEN 'ru'
                WHEN 'Japanese' THEN 'jp'
                WHEN 'Ukrainian' THEN 'uk'
              END = r.idioma
        LEFT JOIN archivos AS audio
            ON audio.campaña_id = b.id_campaña
           AND audio.tipo = 'audio'
           AND b.inc_audio = 1
        LEFT JOIN archivos AS video
            ON video.campaña_id = b.id_campaña
           AND video.tipo = 'video'
           AND b.inc_video = 1
        LEFT JOIN archivos AS ficha
            ON ficha.campaña_id = b.id_campaña
           AND ficha.tipo = 'ficha'
           AND b.inc_ficha = 1
           AND ficha.idioma = CASE b.idioma
                WHEN 'French' THEN 'fr'
                WHEN 'Spanish' THEN 'esp'
                WHEN 'English' THEN 'en'
                WHEN 'Korean' THEN 'ko'
                WHEN 'Russian' THEN 'ru'
                WHEN 'Japanese' THEN 'jp'
                WHEN 'Ukrainian' THEN 'uk'
           END
        WHERE b.estado = 0 
          AND b.id_campaña = ?";








if (!$con) {
	http_response_code(500);
	echo json_encode(['error' => true, 'message' => 'Error de conexión a la base de datos'], JSON_PRETTY_PRINT);
	exit;
}

// ejecutar consulta
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $id_campana);
$stmt->execute();
$resultado = $stmt->get_result();

$dataFiles = [];

while ($row = $resultado->fetch_assoc()) {
    // mensaje base
    $mensaje = trim($row['respuesta'] . ' ' . $row['despedida']);

    // concatenar enlaces si existen
    if ($row['audio_url']) {
        $mensaje .= "\n\n" . $row['audio_url'];
    }
    if ($row['video_url']) {
        $mensaje .= "\n\n" . $row['video_url'];
    }
    if ($row['ficha_url']) {
        $mensaje .= "\n\n" . $row['ficha_url'];
    }

    $objeto = [
        'email' => $row['email'],       
        'id_contacto' => $row['id_contacto'],
        'id_bandeja' => $row['id'],
        'asunto' => trim($row['prefijo'] . ' ' . $row['asunto']),
        'idioma' => $row['idioma'],
        'mensaje' => $mensaje
    ];
    
    $dataFiles[] = $objeto;  
}



		// mailing

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__.'/../vendor/autoload.php';

$id_campana = intval($_GET['id_campana']);

$sqlCred = "SELECT ea.smtp_host, ea.smtp_port, ea.email, ea.password, ea.encryption, ea.sender_name
            FROM campañas c
            INNER JOIN email_accounts ea ON ea.id = c.id_email_account
            WHERE c.id = ?";
$stmtCred = $con->prepare($sqlCred);
$stmtCred->bind_param("i", $id_campana);
$stmtCred->execute();
$cred = $stmtCred->get_result()->fetch_assoc();



// echo "<pre>";
// print_r($cred);
// echo "</pre>";

// --- CONFIGURAR PHPMailer ---
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = $cred['smtp_host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $cred['email'];
    $mail->Password   = $cred['password'];
    $mail->SMTPSecure = $cred['encryption'];
    $mail->Port       = $cred['smtp_port'];
    $mail->CharSet    = 'UTF-8';

    foreach ($dataFiles as $d) {
    $mail->clearAddresses();
    $mail->setFrom($cred['email'], $cred['sender_name']);
    $mail->addAddress($d['email']);
    $mail->Subject = $d['asunto'];
    $mail->Body    = $d['mensaje'];
    $mail->send();

    // actualizar estado
    $stmtUpd = $con->prepare("UPDATE bandejas_campaña SET estado = 1 WHERE id = ?");
    $stmtUpd->bind_param("i", $d['id_bandeja']);
    $stmtUpd->execute();
}


// refrescar bandeja
$stmtRef = $con->prepare("SELECT id_contacto, asunto, idioma, mensaje, id FROM bandejas_campaña WHERE id_campaña=? AND estado=0");
$stmtRef->bind_param("i", $id_campana);
$stmtRef->execute();
$resRef = $stmtRef->get_result();

$bandejaActualizada = [];
while ($row = $resRef->fetch_assoc()) {
    $bandejaActualizada[] = $row;
}

    echo json_encode([
    'data' => $bandejaActualizada,
    'error' => false,
    'message' => 'Emails enviados y bandeja recargada'
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


} catch (Exception $e) {
    echo json_encode([
        'data' => $dataFiles,
        'error' => true,
        'message' => "Error al enviar: {$mail->ErrorInfo}"
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}






?>