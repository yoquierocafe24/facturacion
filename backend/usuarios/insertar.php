<?php
require_once __DIR__ . "/../config/conexion.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

$data = json_decode(file_get_contents("php://input"), true);

if(!$data){
    echo json_encode(["error" => "No llegan datos"]);
    exit;
}

$nombre   = $data['nombre'];
$usuario  = $data['usuario'];
$passwordOriginal = $data['password'];
$id_rol   = $data['id_rol'];

if(empty($nombre) || empty($usuario) || empty($passwordOriginal) || empty($id_rol)){
    echo json_encode(["error" => "Todos los campos son obligatorios"]);
    exit;
}

if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&\.#\-_,;:]).{8,}$/', $passwordOriginal)) {
    echo json_encode([
        "error" => "La contraseña debe tener mínimo 8 caracteres, una mayúscula, una minúscula, un número y un símbolo (@$!%*?&.#-_,;:)"
    ]);
    exit;
}

$password = password_hash($passwordOriginal, PASSWORD_DEFAULT);

//¿Existe activo?
$checkActivo = $conn->prepare("SELECT id_usuario FROM usuarios WHERE usuario = ? AND estado = 1");
$checkActivo->bind_param("s", $usuario);
$checkActivo->execute();
if($checkActivo->get_result()->num_rows > 0){
    echo json_encode(["error" => "El nombre de usuario ya está en uso"]);
    exit;
}

//  ¿Existe inactivo?  reactivar
$checkInactivo = $conn->prepare("SELECT id_usuario FROM usuarios WHERE usuario = ? AND estado = 0");
$checkInactivo->bind_param("s", $usuario);
$checkInactivo->execute();
$resultInactivo = $checkInactivo->get_result();

if($resultInactivo->num_rows > 0){
    $row = $resultInactivo->fetch_assoc();
    $stmt = $conn->prepare("UPDATE usuarios SET nombre=?, password=?, id_rol=?, estado=1 WHERE id_usuario=?");
    $stmt->bind_param("ssii", $nombre, $password, $id_rol, $row['id_usuario']);

    if($stmt->execute()){
        echo json_encode(["mensaje" => "Usuario reactivado correctamente"]);
    } else {
        echo json_encode(["error" => "Error al reactivar usuario"]);
    }
    exit;
}

//  No existe → INSERT
$stmt = $conn->prepare("INSERT INTO usuarios (nombre, usuario, password, id_rol, estado) VALUES (?, ?, ?, ?, 1)");
$stmt->bind_param("sssi", $nombre, $usuario, $password, $id_rol);

if($stmt->execute()){
    echo json_encode(["mensaje" => "Usuario creado"]);
} else {
    echo json_encode(["error" => "Error al crear usuario"]);
}
?>