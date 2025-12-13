<?php
require_once __DIR__ . '/../conexion.php'; // Conexión a la base de datos

use PhpImap\Mailbox;
require_once __DIR__ . '/../vendor/autoload.php';

$mailbox = new Mailbox(
    '{imap.gmail.com:993/imap/ssl}INBOX',
    'alanm.prod@gmail.com',
    'tu_contraseña_aqui',
    __DIR__
);

$mailsIds = $mailbox->searchMailbox('UNSEEN');
$respuestas = [];

foreach ($mailsIds as $mailId) {
    $mail = $mailbox->getMail($mailId);
    $fromEmail = $mail->fromAddress;
    $asunto = $mail->subject;
    $mensaje = $mail->textPlain ?: $mail->textHtml;
    $fecha = date('j-M-Y H:i:s', strtotime($mail->date));
    $cuenta = 'alanm.prod@gmail.com';

    // Buscar id_contacto por email
    $stmt = $conn->prepare("SELECT id FROM contactos WHERE email = ?");
    $stmt->bind_param("s", $fromEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    $id_contacto = null;
    if ($row = $result->fetch_assoc()) {
        $id_contacto = $row['id'];
    }

    // Insertar solo si se encontró el contacto
    if ($id_contacto !== null) {
        // Verificar si ya existe este mensaje exacto
        $check = $conn->prepare("SELECT id FROM respuestas WHERE id_contacto = ? AND asunto = ? AND mensaje = ? AND cuenta = ?");
        $check->bind_param("isss", $id_contacto, $asunto, $mensaje, $cuenta);
        $check->execute();
        $exists = $check->get_result()->fetch_assoc();

        if (!$exists) {
            $insert = $conn->prepare("INSERT INTO respuestas (id_contacto, asunto, mensaje, fecha, cuenta) VALUES (?, ?, ?, ?, ?)");
            $insert->bind_param("issss", $id_contacto, $asunto, $mensaje, $fecha, $cuenta);
            $insert->execute();
        }

        // Obtener datos del contacto para mostrar en respuesta
        $info = $conn->prepare("SELECT nombre, idioma FROM contactos WHERE id = ?");
        $info->bind_param("i", $id_contacto);
        $info->execute();
        $data = $info->get_result()->fetch_assoc();

        $respuestas[] = [
            "nombre" => $data['nombre'],
            "asunto" => $asunto,
            "idioma" => $data['idioma'],
            "fecha" => $fecha,
            "mensaje" => $mensaje,
            "id_contacto" => $id_contacto,
            "estado" => 0,
            "campaña" => null,
            "cuenta" => $cuenta
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($respuestas, JSON_UNESCAPED_UNICODE);
