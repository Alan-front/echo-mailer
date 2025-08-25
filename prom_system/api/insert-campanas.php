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
	
	$datosCampana = $data['envioDeCampana'];

	$artista = mysqli_real_escape_string($con, $datosCampana['nombreDeCampana']);
	$nombre_lanzamiento = mysqli_real_escape_string($con, $datosCampana['nombreLanzamiento']);
	$tipo_lanzamiento = mysqli_real_escape_string($con, $datosCampana['lanzamiento']);
	$enlace = mysqli_real_escape_string($con, $datosCampana['elLink']);
	$genero = mysqli_real_escape_string($con, $datosCampana['elGenero']);
	$id_firma = mysqli_real_escape_string($con, $datosCampana['idFirma']);


	$cerrarSql ="UPDATE campañas SET activa = 2 WHERE id_email_account = '$id_firma' AND activa = 1";
	mysqli_query($con, $cerrarSql);



	$sql = "INSERT INTO campañas (artista, tipo_de_lanzamiento, nombre_lanzamiento, enlace, music_genre, id_email_account) 
	VALUES ('$artista', '$tipo_lanzamiento', '$nombre_lanzamiento', '$enlace', '$genero', '$id_firma')";


	if(mysqli_query($con, $sql)) {
		$idCampana = mysqli_insert_id($con);
		echo "Campaña insertada exitosamente, si Id es: $idCampana";
	}else{
		echo "Error al insertar campaña: " . mysqli_error($con);
	}

	 $emails = $data['envioDeEmails'];

	
	print_r($emails);


	foreach ($emails as $emailObj) {
	$idContact = mysqli_real_escape_string($con, $emailObj['idContact']);
  //  $correo = mysqli_real_escape_string($con, $emailObj['email']);
    $nombre = mysqli_real_escape_string($con, $emailObj['name']);
    $idioma = mysqli_real_escape_string($con, $emailObj['idioma']);

    $sqlEmail = "INSERT INTO enviados (campaña_id, nombre_contacto, idioma, id_contacto)
    VALUES ('$idCampana', '$nombre', '$idioma', '$idContact')";

    mysqli_query($con, $sqlEmail);
}


file_put_contents('log.txt', "Ejecutado en: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);



	// echo "Campaña recibida" . json_encode($datosCampana) . "\n";
	// echo "Campaña recibida" . json_encode($emails);



?>