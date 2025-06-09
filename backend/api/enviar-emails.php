<?php

// require 'phpmailer/PHPMailer.php';
// require 'phpmailer/SMTP.php';
// require 'phpmailer/Exception.php';

require __DIR__ . '/vendor/autoload.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Habilitar CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$env = parse_ini_file('.env');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Leer datos desde Vue
$data = json_decode(file_get_contents('php://input'), true);

// Validar datos
if (!$data || !is_array($data)) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos inválidos']);
    exit;
}

$enviados = [];

foreach ($data as $item) {
    if (empty($item['email'])) continue;

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $env['MAIL_USERNAME'];
        $mail->Password = $env['MAIL_PASSWORD']; 
        $mail->SMTPSecure = 'tls'; // ssl si es un host con candado
        $mail->Port = 587;

        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        $mail->setFrom($env['MAIL_USERNAME'], $env['MAIL_FROM_NAME']);
        $mail->addAddress($item['email']);
        $mail->Subject = $item['tradAsunto'];
        $mail->Body = $item['tradMensaje'];
        $mail->isHTML(true); 

        $mail->send();
        $enviados[] = $item['email'];

    } catch (Exception $e) {
        error_log("Error al enviar a {$item['email']}: " . $mail->ErrorInfo);
    }
}

echo json_encode(['enviados' => $enviados]);
