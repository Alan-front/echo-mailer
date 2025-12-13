<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");


if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
     http_response_code(200);
    exit;
}

include 'conex-campanas.php';

$con = conectar();

if (!$con) {
    echo "Error en la conexión: " . mysqli_connect_error();
    // exit;
}



	$input = file_get_contents("php://input");
	$data = json_decode($input, true);

	if ($data === null) {
    echo "No se pudo decodificar JSON. Entrada recibida: " . $input;
    // exit;
}

// echo "Datos recibidos: ";
// print_r($data);
// exit;
	
	

	
	$idDeCampana = $data['idCampExistente'];
	 $emails = $data['envioDeEmails'];

	
	print_r($emails);


	foreach ($emails as $emailData) {
    $idContact = mysqli_real_escape_string($con, $emailData['idContact']);   
   // $correo = mysqli_real_escape_string($con, $emailData['email']);
    $nombre = mysqli_real_escape_string($con, $emailData['name']);
    $idioma = mysqli_real_escape_string($con, $emailData['idioma']);

    $sqlEmail = "INSERT INTO enviados (campaña_id, nombre_contacto, idioma, id_contacto)
                 VALUES ('$idDeCampana', '$nombre', '$idioma', '$idContact')";

    if (!mysqli_query($con, $sqlEmail)) {
        echo "Error al insertar email: " . mysqli_error($con);
    }
}



 file_put_contents('log.txt', "Ejecutado en: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);


	echo "Emails insertados exitosamente."; 

	// echo "Campaña recibida" . json_encode($datosCampana) . "\n";
	// echo "Campaña recibida" . json_encode($emails);



?>