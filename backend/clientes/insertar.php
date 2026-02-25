<?php
include "../config/conexion.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

$data = json_decode(file_get_contents("php://input"), true);

if(!$data){
    echo json_encode(["error" => "No llegan datos"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$nombre = $data['nombre'];
$cedula = $data['cedula'];
$telefono = $data['telefono'];
$email = $data['email'];
$direccion = $data['direccion'];
$rtn= $data['rtn'];

$stmt = $conn->prepare("INSERT INTO clientes (nombre, cedula, telefono, email, direccion, rtn) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $nombre, $cedula, $telefono, $email, $direccion, $rtn);

if ($stmt->execute()) {
    echo json_encode(["mensaje" => "Cliente creado"]);
} else {
    echo json_encode(["error" => "Error al crear"]);
}
?>