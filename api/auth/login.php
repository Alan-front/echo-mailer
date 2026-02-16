<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once '../config/conexion.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['username']) || !isset($data['password'])) {
    echo json_encode(['success' => false, 'message' => 'Faltan credenciales']);
    exit;
}

$con = conectar();
$username = mysqli_real_escape_string($con, $data['username']);

$sql = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($con, $sql);
$user = mysqli_fetch_assoc($result);

if ($user && password_verify($data['password'], $user['password'])) {
    $token = bin2hex(random_bytes(32));
    echo json_encode(['success' => true, 'token' => $token]);
} else {
    echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas']);
}
?>