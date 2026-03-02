<?php
require_once __DIR__ . "/../config/conexion.php";

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id_usuario'];
$nombre = $data['nombre'];
$usuario = $data['usuario'];
$password = $data['password']; 
$id_rol = $data['id_rol'];

// Si viene nueva contraseña
if(!empty($password)){

    $passwordEncriptada = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE usuarios 
    SET nombre=?, usuario=?, password=?, id_rol=? 
    WHERE id_usuario=?");

    $stmt->bind_param("sssii", $nombre, $usuario, $passwordEncriptada, $id_rol, $id);

} else {

    // Si NO cambia contraseña
    $stmt = $conn->prepare("UPDATE usuarios 
    SET nombre=?, usuario=?, id_rol=? 
    WHERE id_usuario=?");

    $stmt->bind_param("ssii", $nombre, $usuario, $id_rol, $id);
}

if ($stmt->execute()) {
    echo json_encode(["mensaje" => "Usuario actualizado"]);
} else {
    echo json_encode(["error" => "Error al actualizar"]);
}
?>