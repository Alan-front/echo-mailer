<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
header('Content-Type: application/json; charset=UTF-8');

ob_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
set_time_limit(300); 
ini_set('default_socket_timeout', 60); 
header("Content-Type: application/json; charset=UTF-8");

require __DIR__ . '/vendor/autoload.php';
require 'conexion.php';

$con = conectar();
$con->set_charset("utf8mb4");

// obtener cuentas con campañas activas
$sqlCuentas = "
    SELECT ea.* 
    FROM email_accounts ea
    WHERE ea.id IN (
        SELECT DISTINCT id_email_account 
        FROM `campañas`
        WHERE activa IN (1, 3)
    )
";
$resCuentas = $con->query($sqlCuentas);

if (!$resCuentas || $resCuentas->num_rows === 0) {
    echo json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

$resultado = [];

while ($cuenta = $resCuentas->fetch_assoc()) {

    $imap_host       = $cuenta['imap_host'];
    $imap_port       = $cuenta['imap_port'];
    $imap_user       = $cuenta['email'];
    $imap_password   = $cuenta['password'];
    $imap_encryption = $cuenta['encryption'];
    $imap_folder     = "INBOX";

    $mailbox = "{" . $imap_host . ":" . $imap_port . "/imap/" . $imap_encryption . "/novalidate-cert}" . $imap_folder;

    // traer todos los contactos con campañas activas de esta cuenta una sola vez
    $stmtContactos = $con->prepare("
        SELECT DISTINCT 
            mc.test_email,
            mc.id,
            mc.name,
            mc.secondary_language,
            c.id AS id_campana,
            c.fecha_creacion
        FROM enviados e
        JOIN media_contacts mc ON e.id_contacto = mc.id
        JOIN `campañas` c ON e.campaña_id = c.id
        WHERE c.id_email_account = ? AND c.activa IN (1, 3)
        ORDER BY c.fecha_creacion DESC
    ");
    $stmtContactos->bind_param("i", $cuenta['id']);
    $stmtContactos->execute();
    $resContactos = $stmtContactos->get_result();

    // crear mapa de contactos por email para busqueda rapida
    $contactosMap = [];
    while ($row = $resContactos->fetch_assoc()) {
        $email = strtolower($row['test_email']);
        if (!isset($contactosMap[$email])) {
            $contactosMap[$email] = [];
        }
        $contactosMap[$email][] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'idioma' => $row['secondary_language'],
            'id_campana' => $row['id_campana'],
            'fecha_creacion' => $row['fecha_creacion']
        ];
    }

    // calcular fecha minima
    $stmtMinFecha = $con->prepare("SELECT MIN(fecha_creacion) AS min_fecha FROM `campañas` WHERE id_email_account = ? AND activa IN (1, 3)");
    $stmtMinFecha->bind_param("i", $cuenta['id']);
    $stmtMinFecha->execute();
    $minFechaRow = $stmtMinFecha->get_result()->fetch_assoc();
    $fechaIMAP = date('j-M-Y', strtotime($minFechaRow['min_fecha']));

    // conectar imap
    $inbox = imap_open($mailbox, $imap_user, $imap_password);
    if (!$inbox) {
        error_log("Error IMAP cuenta {$cuenta['id']}: " . imap_last_error());
        continue;
    }

    // buscar emails desde fecha
    $emails = imap_search($inbox, 'SINCE "' . $fechaIMAP . '"');
    if (!$emails) {
        imap_close($inbox);
        continue;
    }




// LIMITAR A 15 MAS RECIENTES
rsort($emails); // ordenar descendente (más recientes primero)
$emails = array_slice($emails, 0, 15);

    foreach ($emails as $email_number) {
        $overview = imap_fetch_overview($inbox, $email_number, 0);
        
        // extraer remitente
        $remitente = isset($overview[0]->from) ? $overview[0]->from : '';
        $emailRemitente = $remitente;
        if (preg_match('/<(.+)>/', $remitente, $matches)) {
            $emailRemitente = $matches[1];
        }
        $emailRemitente = strtolower(trim($emailRemitente));

        // verificar si esta en el mapa
        if (!isset($contactosMap[$emailRemitente])) {
            continue;
        }

        // ahora si leer el mensaje
        $rawMessage = imap_fetchbody($inbox, $email_number, 1);
        $message = limpiarMensaje(decodificarMensaje($rawMessage));

        // extraer asunto
        $asunto = isset($overview[0]->subject) ? $overview[0]->subject : '';
        $asuntoDecodificado = imap_mime_header_decode($asunto);
        $asunto = '';
        foreach ($asuntoDecodificado as $elemento) {
            $asunto .= $elemento->text;
        }

        // extraer fecha
        $fecha_email = isset($overview[0]->date) ? $overview[0]->date : '';
        $fechaEmailFull = date('Y-m-d H:i:s', strtotime($fecha_email));

        // buscar campaña correcta para este contacto
        $campanasContacto = $contactosMap[$emailRemitente];
        $idCampanaAsignada = null;
        $contactoData = null;

        foreach ($campanasContacto as $camp) {
            if (strtotime($camp['fecha_creacion']) <= strtotime($fechaEmailFull)) {
                $idCampanaAsignada = $camp['id_campana'];
                $contactoData = $camp;
                break;
            }
        }

        if ($idCampanaAsignada !== null) {
            $resultado[] = [
                'nombre'      => $contactoData['name'],
                'asunto'      => $asunto,
                'idioma'      => $contactoData['idioma'],
                'fecha'       => $fechaEmailFull,
                'mensaje'     => $message,
                'id_contacto' => $contactoData['id'],
                'id_campana'  => $idCampanaAsignada
            ];
        }
    }
    
    imap_close($inbox, CL_EXPUNGE);
imap_errors(); // limpiar buffer de errores
imap_alerts(); // limpiar alertas
}

// insertar en bandejas_campaña evitando duplicados
foreach ($resultado as $m) {
    $stmtCheck = $con->prepare("
        SELECT id 
        FROM bandejas_campaña 
        WHERE id_campaña = ? AND id_contacto = ? AND fecha = ?
        LIMIT 1
    ");
    $stmtCheck->bind_param("iis", $m['id_campana'], $m['id_contacto'], $m['fecha']);
    $stmtCheck->execute();
    $exists = $stmtCheck->get_result()->fetch_assoc();

    if (!$exists) {
        // contar emails existentes para calcular replica
        $stmtCount = $con->prepare("
            SELECT COUNT(*) as total 
            FROM bandejas_campaña 
            WHERE id_campaña = ? AND id_contacto = ?
        ");
        $stmtCount->bind_param("ii", $m['id_campana'], $m['id_contacto']);
        $stmtCount->execute();
        $countResult = $stmtCount->get_result()->fetch_assoc();
        $replicaValue = $countResult['total'];
        $estadoValue = ($replicaValue > 0) ? 2 : NULL;

        // insertar nuevo registro
        $stmtInsert = $con->prepare("
            INSERT INTO bandejas_campaña 
            (id_campaña, id_contacto, nombre, asunto, idioma, fecha, mensaje, replica, estado, inc_audio, inc_video, inc_ficha) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0, 0)
        ");
        $stmtInsert->bind_param(
            "iisssssis",
            $m['id_campana'],
            $m['id_contacto'],
            $m['nombre'],
            $m['asunto'],
            $m['idioma'],
            $m['fecha'],
            $m['mensaje'],
            $replicaValue,
            $estadoValue
        );
        $stmtInsert->execute();
    }
}

echo json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);


@imap_errors();
@imap_alerts();

exit;


function decodificarMensaje($rawMessage) {
    $decoded = quoted_printable_decode($rawMessage);
    if (preg_match('/^[A-Za-z0-9+\/=\s]+$/', trim($rawMessage))) {
        $cleanBase64 = preg_replace('/\s+/', '', $rawMessage);
        $base64Decoded = base64_decode($cleanBase64, true);
        if ($base64Decoded !== false) {
            return $base64Decoded;
        }
    }
    return $decoded;
}

function limpiarMensaje($message) {
    $patronesLimpiar = [
        '/Enviado desde el correo electrónico seguro de \[Proton Mail\]\(.*?\)\.?/i',
        '/Sent from \[Proton Mail\]\(.*?\)\.?/i',
        '/Envoyé depuis \[Proton Mail\]\(.*?\)\.?/i',
        '/\[Proton Mail\]\(https:\/\/proton\.me\/mail\/home\)/i',
        '/https:\/\/proton\.me\/mail\/home/i'
    ];
    foreach ($patronesLimpiar as $patron) {
        $message = preg_replace($patron, '', $message);
    }
    $separadores = ['________________________________', 'From:', 'De:', '-----Original Message-----', 'El ', 'On '];
    foreach ($separadores as $sep) {
        $parte = strstr($message, $sep, true);
        if ($parte !== false) {
            $message = trim($parte);
            break;
        }
    }
    $message = preg_replace('/\n\s*\n/', "\n", $message);
    return trim($message);
}