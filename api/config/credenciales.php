<?php

header("Access-Control-Allow-Origin: *"); 
header("Content-Type: application/json");

function conectar(){
	$host = "localhost";
	$user = "root";
	$pass = "";
	$db = "promotion_system";
	
	$connec = mysqli_connect($host, $user, $pass);
	mysqli_select_db($connec, $db);
	
	return $connec;
}

$con = conectar();

mysqli_set_charset($con, "utf8");


if(!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['error' => 'No se recibió el ID']);
    exit;
}

$id = intval($_GET['id']);

$sql = "SELECT * FROM email_accounts WHERE id = $id";

$resultado = mysqli_query($con, $sql);

if($resultado && mysqli_num_rows($resultado) > 0) {
    $fila = mysqli_fetch_assoc($resultado);
    echo json_encode($fila);
} else {
    echo json_encode(['error' => 'No se encontró la firma']);
}

mysqli_close($con);

?>