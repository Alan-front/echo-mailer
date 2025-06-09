<?php
header("Access-Control-Allow-Origin: *"); 
 header("Content-Type: application/json");
// header("Content-Type: text/plain");

include 'conexion.php';

$con = conectar();

$sql = "SELECT * FROM campañas";

$conecCampanas = mysqli_query($con, $sql);

$dataCamp = [];

while($row = mysqli_fetch_assoc($conecCampanas)){
	$dataCamp[] = $row;
}


 echo json_encode($dataCamp);

//con text/plain
// foreach ($dataCamp as $fila){
// 	print_r($fila);
// 	echo "\n---------------------\n";
// }

//con aplicaation/json, no con text/plai

// echo json_encode($dataCamp, JSON_PRETTY_PRINT); 

?>

