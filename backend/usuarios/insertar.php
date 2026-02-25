<?php
require_once __DIR__ . "/../config/conexion.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

$data = json_decode(file_get_contents("php://input"), true);

if(!$data){
    echo json_encode(["error" => "No llegan datos"]);
    exit;
}

$nombre = $data['nombre'];
$usuario = $data['usuario'];
$password = password_hash($data['password'], PASSWORD_DEFAULT);
$id_rol = $data['id_rol'];

$stmt = $conn->prepare("INSERT INTO usuarios (nombre, usuario, password, id_rol, estado) VALUES (?, ?, ?, ?, 1)");

$stmt->bind_param("sssi", $nombre, $usuario, $password, $id_rol);

if ($stmt->execute()) {
    echo json_encode(["mensaje" => "Usuario creado"]);
} else {
    echo json_encode(["error" => "Error al crear usuario"]);
}
?>