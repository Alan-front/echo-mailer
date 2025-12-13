<?php
header("Access-Control-Allow-Origin: *"); 
header("Content-Type: application/json");

include 'conexion.php';

$con = conectar();

mysqli_set_charset($con, "utf8");


$sql = "SELECT id, sender_name FROM email_accounts";

$resultado = mysqli_query($con, $sql);

$datos = [];

while ($fila = mysqli_fetch_assoc($resultado)) {
    $datos[] = $fila;
}

echo json_encode($datos, JSON_UNESCAPED_UNICODE);

