<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include '../config/conexion.php';

$con = conectar();
 // id, name, media_type, music_genre, country, country, secondary_language

$sql = "SELECT id, name, media_type, music_genre, country, country, secondary_language FROM media_contacts WHERE active = 1  ";
$result = mysqli_query($con, $sql);

$dataContacts = [];

while ($row = mysqli_fetch_assoc($result)) {
    $dataContacts[] = $row;
}

echo json_encode($dataContacts);
