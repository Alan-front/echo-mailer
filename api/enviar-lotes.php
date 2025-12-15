<?php
require __DIR__ . '/vendor/autoload.php';
require 'conexion.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

set_time_limit(300); // 5 minutos para evitar timeout

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$connec = conectar();

// ============================================
// buscar la primera campaña activa = 3
// ============================================
$sqlCampana = "SELECT id, tipo_de_lanzamiento, enlace, activa, id_email_account 
               FROM campañas 
               WHERE activa = 3
               LIMIT 1";
$campanaResult = $connec->query($sqlCampana);
$campana = $campanaResult->fetch_assoc();

if (!$campana) {
    echo json_encode(['mensaje' => 'No hay campañas con estado 3']);
    exit;
}

$idCampana = $campana['id'];

// ============================================
// traer credenciales smtp
// ============================================
$sqlCred = "SELECT * FROM email_accounts WHERE id = ?";
$stmtCred = $connec->prepare($sqlCred);
$stmtCred->bind_param("i", $campana['id_email_account']);
$stmtCred->execute();
$credenciales = $stmtCred->get_result()->fetch_assoc();
$stmtCred->close();

if (!$credenciales) {
    http_response_code(404);
    echo json_encode(['error' => 'Credenciales de email no encontradas']);
    exit;
}

// ============================================
// conseguir contactos pendientes con estado = 0
// ============================================
$sql = "SELECT e.id, e.id_contacto, e.estado,
               c.secondary_language, c.media_type, c.test_email, c.name
        FROM enviados e
        INNER JOIN media_contacts c ON e.id_contacto = c.id
        WHERE e.campaña_id = ? AND e.estado = 0 
        LIMIT 20";

$stmt = $connec->prepare($sql);
$stmt->bind_param("i", $idCampana);
$stmt->execute();
$result = $stmt->get_result();

$contactos = [];
while ($row = $result->fetch_assoc()) {
    $contactos[] = $row;
}
$stmt->close();

if (empty($contactos)) {
    echo json_encode([
        'mensaje' => 'No hay contactos pendientes para enviar',
        'total' => 0
    ]);
    exit;
}

// ============================================
// traducciones para los idiomas
// ============================================
$tradLanzamiento = [
    'Album' => ['French'=>'album','English'=>'album','Russian'=>'альбом','Ukrainian'=>'альбом','Korean'=>'앨범','Japanese'=>'アルバム','Spanish'=>'álbum'],
    'EP' => ['French'=>'EP','English'=>'EP','Russian'=>'EP','Ukrainian'=>'EP','Korean'=>'EP','Japanese'=>'EP','Spanish'=>'EP'],
    'Single' => ['French'=>'single','English'=>'single','Russian'=>'сингл','Ukrainian'=>'сингл','Korean'=>'싱글','Japanese'=>'シングル','Spanish'=>'sencillo']
];
$traduccionesMedia = [
    'Radio'=>['French'=>'programme','English'=>'program','Russian'=>'программа','Ukrainian'=>'програма','Korean'=>'프로그램','Japanese'=>'番組','Spanish'=>'programa'],
    'TV'=>['French'=>'programme','English'=>'program','Russian'=>'программа','Ukrainian'=>'програма','Korean'=>'프로그램','Japanese'=>'番組','Spanish'=>'programa'],
    'Magazine'=>['French'=>'magazine','English'=>'magazine','Russian'=>'журнал','Ukrainian'=>'журнал','Korean'=>'잡지','Japanese'=>'雑誌','Spanish'=>'revista']
];
$tradAsunto = [
    'French'=>'Nouveau lancement de','English'=>'New release of','Russian'=>'Новый релиз','Ukrainian'=>'Новий реліз',
    'Korean'=>'의 새 출시','Japanese'=>'の新リリース','Spanish'=>'Nuevo lanzamiento de'
];
function obtenerMensaje($idioma, $mediaTraducido, $enlace){
    $mensajes = [
        'French'=>"<p>Bonjour, il y a quelque temps, j'ai découvert votre {$mediaTraducido} et j'aime beaucoup.<br> Je vous informe que j'ai un projet et j'aimerais savoir si je pourrais avoir une opportunité.</p><p><a href=\"{$enlace}\">{$enlace}</a></p><p>Merci pour votre temps.</p>",
        'English'=>"<p>Hello, some time ago I discovered your {$mediaTraducido} and I like it a lot.<br> I want to tell you I have a project and would like to know if I could have an opportunity.</p><p><a href=\"{$enlace}\">{$enlace}</a></p><p>Thank you for your time.</p>",
        'Russian'=>"<p>Здравствуйте, некоторое время назад я узнал о вашем {$mediaTraducido} и мне очень понравилось.<br> Хочу сообщить, что у меня есть проект, и я хотел бы узнать, есть ли возможность.</p><p><a href=\"{$enlace}\">{$enlace}</a></p><p>Спасибо за ваше время.</p>",
        'Ukrainian'=>"<p>Привіт, деякий час тому я дізнався про ваш {$mediaTraducido} і він мені дуже подобається.<br> Хочу повідомити, що у мене є проєкт, і я хотів би дізнатись, чи можу я мати можливість.</p><p><a href=\"{$enlace}\">{$enlace}</a></p><p>Дякую за ваш час.</p>",
        'Korean'=>"<p>안녕하세요, 얼마 전에 귀하의 {$mediaTraducido}를 알게 되었고 매우 좋아합니다.<br> 프로젝트가 있어 기회가 있을지 알고 싶습니다.</p><p><a href=\"{$enlace}\">{$enlace}</a></p><p>시간 내주셔서 감사합니다.</p>",
        'Japanese'=>"<p>こんにちは、しばらく前にあなたの{$mediaTraducido}を知り、とても気に入っています.<br>プロジェクトがあり、チャンスがあるかどうか知りたいです。</p><p><a href=\"{$enlace}\">{$enlace}</a></p><p>お時間をいただきありがとうございます。</p>",
        'Spanish'=>"<p>Hola, hace un tiempo conocí su {$mediaTraducido} y me gusta mucho.<br> Le comento que tengo un proyecto y me gustaría saber si podría tener alguna oportunidad<br></p><p><a href=\"{$enlace}\">{$enlace}</a></p><p>gracias por su tiempo.</p>"
    ];
    return $mensajes[$idioma] ?? $mensajes['English'];
}

// ============================================
// armamos los mensajes para cada contacto
// ============================================
$mensajesParaEnviar = [];
foreach($contactos as $contacto){
    $idioma = $contacto['secondary_language'];
    $mediaType = $contacto['media_type'];
    $lanzTraducido = $tradLanzamiento[$campana['tipo_de_lanzamiento']][$idioma] ?? $campana['tipo_de_lanzamiento'];
    $mediaTraducido = $traduccionesMedia[$mediaType][$idioma] ?? $mediaType;
    if($idioma==='Korean'||$idioma==='Japanese'){
        $asunto = $lanzTraducido.$tradAsunto[$idioma];
    }else{
        $asunto = $tradAsunto[$idioma].' '.$lanzTraducido;
    }
    $mensaje = obtenerMensaje($idioma,$mediaTraducido,$campana['enlace']);
    $mensajesParaEnviar[] = [
        'id_enviado'=>$contacto['id'],
        'destinatario'=>$contacto['test_email'],
        'nombre'=>$contacto['name'],
        'asunto'=>$asunto,
        'mensaje'=>$mensaje
    ];
}

// ============================================
// configurar phpmailer y empezar a enviar
// ============================================
$mail = new PHPMailer(true);
$enviados = [];
$errores = [];
$isGmail = strpos($credenciales['smtp_host'],'gmail.com')!==false;
$pausaEnvio = $isGmail?2:1;
$loteSize = $isGmail?10:20;

try{
    $mail->isSMTP();
    $mail->Host = $credenciales['smtp_host'];
    $mail->SMTPAuth = true;
    $mail->Username = $credenciales['email'];
    $mail->Password = $credenciales['password'];
    $mail->SMTPSecure = $credenciales['encryption'];
    $mail->Port = $credenciales['smtp_port'];
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    $mail->setFrom($credenciales['email'],$credenciales['sender_name']);
    $mail->isHTML(true);
    if($isGmail){
        $mail->SMTPOptions = ['ssl'=>['verify_peer'=>false,'verify_peer_name'=>false,'allow_self_signed'=>true]];
    }

    foreach($mensajesParaEnviar as $index=>$item){
        try{
            $mail->clearAddresses();
            $mail->addAddress($item['destinatario']);
            $mail->Subject = $item['asunto'];
            $mail->Body = $item['mensaje'];
            if($mail->send()){
                $enviados[] = ['email'=>$item['destinatario'],'nombre'=>$item['nombre']];
                // actualizar estado a enviado
                $sqlUpdate = "UPDATE enviados SET estado=1 WHERE id=?";
                $stmtUpdate = $connec->prepare($sqlUpdate);
                $stmtUpdate->bind_param("i",$item['id_enviado']);
                $stmtUpdate->execute();
                $stmtUpdate->close();
                error_log("Email enviado a: ".$item['destinatario']);
            }
        }catch(Exception $e){
            $errores[]=['email'=>$item['destinatario'],'error'=>$e->getMessage()];
            error_log("Error enviando a {$item['destinatario']}: ".$e->getMessage());
        }
        // pausas entre envios
        if($index+1<count($mensajesParaEnviar)){
            if($isGmail && ($index+1)%$loteSize===0){ sleep(5); }else{ sleep($pausaEnvio); }
        }
    }
}catch(Exception $e){
    error_log("Error SMTP general: ".$mail->ErrorInfo);
    $errores[]=['error'=>$mail->ErrorInfo];
}

$connec->close();

// ============================================
// devolver respuesta json
// ============================================
echo json_encode([
    'success'=>true,
    'campana_id'=>$idCampana,
    'total_procesados'=>count($mensajesParaEnviar),
    'total_enviados'=>count($enviados),
    'enviados'=>$enviados,
    'errores'=>$errores
],JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);

?>