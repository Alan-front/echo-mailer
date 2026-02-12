<?php
header("Access-Control-Allow-Origin: *"); 
 header("Content-Type: application/json");
// header("Content-Type: text/plain");

include '../config/conexion.php';

$con = conectar();

//	id, campaña_id, id_contacto, idioma, nombre_contacto

$sql = "SELECT id, campaña_id, id_contacto, idioma, estado, nombre_contacto FROM enviados";

$conecYaEnviados = mysqli_query($con, $sql);

$dataCamp = [];

while($row = mysqli_fetch_assoc($conecYaEnviados)){
	$dataYaEnv[] = $row;
}


 echo json_encode($dataYaEnv);

//con text/plain
// foreach ($dataCamp as $fila){
// 	print_r($fila);
// 	echo "\n---------------------\n";
// }

//con aplicaation/json, no con text/plai

// echo json_encode($dataCamp, JSON_PRETTY_PRINT); 

?>

