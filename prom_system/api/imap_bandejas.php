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
header("Content-Type: application/json; charset=UTF-8");

require __DIR__ . '/vendor/autoload.php';
require 'conexion.php';

$con = conectar();
$con->set_charset("utf8mb4");

// --- 1. Obtener todas las cuentas de email que tengan campañas activas ---
$sqlCuentas = "
    SELECT ea.* 
    FROM email_accounts ea
    WHERE ea.id IN (
        SELECT DISTINCT id_email_account 
        FROM `campañas`
        WHERE activa = 1
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

    // --- 2. Calcular fecha mínima desde la campaña más antigua de esta cuenta ---
    $stmtMinFecha = $con->prepare("SELECT MIN(fecha_creacion) AS min_fecha FROM `campañas` WHERE id_email_account = ? AND activa = 1");
    $stmtMinFecha->bind_param("i", $cuenta['id']);
    $stmtMinFecha->execute();
    $minFechaRow = $stmtMinFecha->get_result()->fetch_assoc();
    $fechaIMAP = date('j-M-Y', strtotime($minFechaRow['min_fecha']));

    // --- 3. Conexión IMAP ---
    $inbox = @imap_open($mailbox, $imap_user, $imap_password);
    if (!$inbox) {
        continue;
    }

    // --- 4. Buscar emails desde la fecha más antigua ---
    $emails = imap_search($inbox, 'SINCE "' . $fechaIMAP . '"');
    if (!$emails) {
        imap_close($inbox);
        continue;
    }

    foreach ($emails as $email_number) {
        $overview = imap_fetch_overview($inbox, $email_number, 0);
        $rawMessage = imap_fetchbody($inbox, $email_number, 1);

        $message = limpiarMensaje(decodificarMensaje($rawMessage));

        $asunto = isset($overview[0]->subject) ? $overview[0]->subject : '';
        $asuntoDecodificado = imap_mime_header_decode($asunto);
        $asunto = '';
        foreach ($asuntoDecodificado as $elemento) {
            $asunto .= $elemento->text;
        }

        $remitente = isset($overview[0]->from) ? $overview[0]->from : '';
        $fecha_email = isset($overview[0]->date) ? $overview[0]->date : '';
        $fechaEmailIMAP = date('j-M-Y', strtotime($fecha_email));
        $fechaEmailFull = date('Y-m-d H:i:s', strtotime($fecha_email));

        $emailRemitente = $remitente;
        if (preg_match('/<(.+)>/', $remitente, $matches)) {
            $emailRemitente = $matches[1];
        }

        // --- 5. Buscar contacto ---
        $stmt = $con->prepare("SELECT id, name, secondary_language FROM media_contacts WHERE test_email = ?");
        $stmt->bind_param("s", $emailRemitente);
        $stmt->execute();
        $contacto = $stmt->get_result()->fetch_assoc();

        $idContacto = $contacto ? $contacto['id'] : null;
        $nameContact = $contacto ? $contacto['name'] : null;
        $idioma = $contacto ? $contacto['secondary_language'] : null;

        if ($idContacto !== null) {
            // --- 6. Buscar TODAS las campañas activas de este contacto en esta cuenta ---
            $stmtCamp = $con->prepare("
                SELECT c.id AS id_campana, c.fecha_creacion
                FROM enviados e
                JOIN `campañas` c ON e.campaña_id = c.id
                WHERE e.id_contacto = ? AND c.id_email_account = ? AND c.activa = 1
                ORDER BY c.fecha_creacion DESC
            ");
            $stmtCamp->bind_param("ii", $idContacto, $cuenta['id']);
            $stmtCamp->execute();
            $resCamp = $stmtCamp->get_result();

            $idCampanaAsignada = null;
            while ($rowCamp = $resCamp->fetch_assoc()) {
                if (strtotime($rowCamp['fecha_creacion']) <= strtotime($fechaEmailFull)) {
                    $idCampanaAsignada = $rowCamp['id_campana'];
                    break;
                }
            }

            if ($idCampanaAsignada !== null) {
                $resultado[] = [
                    'nombre'      => $nameContact,
                    'asunto'      => $asunto,
                    'idioma'      => $idioma,
                    'fecha'       => $fechaEmailFull,
                    'mensaje'     => $message,
                    'id_contacto' => $idContacto,
                    'id_campana'  => $idCampanaAsignada
                ];
            }
        }
    }
    imap_close($inbox);
}

// --- 7. Insertar en la tabla bandejas_campaña evitando duplicados ---
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
        // Contar cuántos emails ya tiene este contacto en esta campaña para calcular replica
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

        // Insertar nuevo registro
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
exit;


// --- Funciones auxiliares ---
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