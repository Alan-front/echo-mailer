<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include 'conexion.php';

$con = conectar();

$sql = "SELECT * FROM media_contacts";
$result = mysqli_query($con, $sql);

$dataContacts = [];

while ($row = mysqli_fetch_assoc($result)) {
    $dataContacts[] = $row;
}

echo json_encode($dataContacts);
