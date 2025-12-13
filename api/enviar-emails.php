<?php
require __DIR__ . '/vendor/autoload.php';
require 'conexion.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['correos']) || !is_array($data['correos'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos inválidos']);
    exit;
}

$firma   = $data['firma'];
$correos = $data['correos'];

$con = conectar();
$sql = "SELECT * FROM email_accounts WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $firma);
$stmt->execute();
$result = $stmt->get_result();

if (!$row = $result->fetch_assoc()) {
    http_response_code(404);
    echo json_encode(['error' => 'No se encontró la cuenta de correo.']);
    exit;
}

$sender_name = $row['sender_name'];
$email_user  = $row['email'];
$email_pass  = $row['password'];
$smtp_host   = $row['smtp_host'];
$smtp_port   = $row['smtp_port'];
$encryption  = $row['encryption'];

$enviados = [];
$errores = [];

// Mapa de emails de contactos
$sqlEmail = $con->query("SELECT id, test_email FROM media_contacts");
$mapaEmails = [];
while ($r = $sqlEmail->fetch_assoc()) {
    $mapaEmails[$r['id']] = $r['test_email'];
}

// Configuración de rate limiting para Gmail
$isGmail = strpos($smtp_host, 'gmail.com') !== false;
$pausaEnvio = $isGmail ? 2 : 1; // 2 segundos para Gmail, 1 para otros
$loteSize = $isGmail ? 10 : 20;  // Cada 10 emails pausa más larga en Gmail

// Inicializar PHPMailer una sola vez
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = $smtp_host;
    $mail->SMTPAuth   = true;
    $mail->Username   = $email_user;
    $mail->Password   = $email_pass;
    $mail->SMTPSecure = $encryption;
    $mail->Port       = $smtp_port;
    $mail->CharSet    = 'UTF-8';
    $mail->Encoding   = 'base64';
    $mail->setFrom($email_user, $sender_name);
    $mail->isHTML(true);
    
    // Configuración adicional para Gmail
    if ($isGmail) {
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
    }

    foreach ($correos as $index => $item) {
        $id = $item['idContact'];
        if (!isset($mapaEmails[$id])) continue;
        
        $email = $mapaEmails[$id];
        
        try {
            $mail->clearAddresses();
            $mail->addAddress($email);
            $mail->Subject = $item['tradAsunto'];
            $mail->Body    = $item['tradMensaje'];
            
            if ($mail->send()) {
                $enviados[] = $email;
                error_log("Email enviado exitosamente a: " . $email);
            }
            
        } catch (Exception $e) {
            $errores[] = ['email' => $email, 'error' => $e->getMessage()];
            error_log("Error enviando a {$email}: " . $e->getMessage());
        }
        
        // Aplicar pausas para evitar rate limiting
        if ($index + 1 < count($correos)) { // No pausar después del último
            if ($isGmail && ($index + 1) % $loteSize === 0) {
                // Pausa más larga cada cierto número de emails en Gmail
                sleep(5);
                error_log("Pausa larga aplicada después de " . ($index + 1) . " emails");
            } else {
                // Pausa normal entre emails
                sleep($pausaEnvio);
            }
        }
    }
    
} catch (Exception $e) {
    error_log("Error SMTP general: " . $mail->ErrorInfo);
    $errores[] = ['error' => $mail->ErrorInfo];
}

// Respuesta con información detallada
echo json_encode([
    'enviados' => $enviados,
    'total_enviados' => count($enviados),
    'total_correos' => count($correos),
    'errores' => $errores
]);
?>