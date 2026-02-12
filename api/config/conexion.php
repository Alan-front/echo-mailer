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
	mysqli_set_charset($connec, "utf8mb4");


	return $connec;
}

// $con = conectar();

// $sql = "SELECT * FROM media_contacts";

// $probando = mysqli_query($con, $sql);

// $data = [];

//  while($row = mysqli_fetch_assoc($probando))
//  	$data[] = $row;
	


// header('Content-Type: application/json');
//   echo json_encode($data);


?>