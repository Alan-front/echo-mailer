<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// header("Content-Type: text/plain");

include 'conexion.php';

$con = conectar();

$sql = "SELECT * FROM campañas";

$conecCampanas = mysqli_query($con, $sql);

$dataCamp = [];

// traer nombre
$sqlEmail = "SELECT id, sender_name FROM email_accounts";
$resEmail = mysqli_query($con, $sqlEmail);
//
$mapaSenderNames = [];
while ($row = mysqli_fetch_assoc($resEmail)) {
    $mapaSenderNames[$row['id']] = $row['sender_name'];
}

while ($row = mysqli_fetch_assoc($conecCampanas)) {
    $idEmail = $row['id_email_account'];
    $row['sender_name'] = isset($mapaSenderNames[$idEmail]) ? $mapaSenderNames[$idEmail] : null;
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

