<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// header("Content-Type: text/plain");

include '../config/conexion.php';

$con = conectar();

$sql = "SELECT * FROM respuestas_plantillas";

$conecFiles = mysqli_query($con, $sql);

$dataFiles = [];

while($row = mysqli_fetch_assoc($conecFiles)){
	$dataFiles[] = $row;
}


echo json_encode($dataFiles, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);



// foreach ($dataFiles as $fila){
// 	print_r($fila);
// 	echo "\n---------------------\n";
// }

//con aplicaation/json, no con text/plai

// echo json_encode($dataFiles, JSON_PRETTY_PRINT); 

?>

