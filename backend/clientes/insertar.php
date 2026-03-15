<?php
include "../config/conexion.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

$data = json_decode(file_get_contents("php://input"), true);

if(!$data){
    echo json_encode(["error" => "No llegan datos"]);
    exit;
}

$nombre    = trim($data['nombre']    ?? '');
$cedula    = trim($data['cedula']    ?? '');
$telefono  = trim($data['telefono'] ?? '');
$email     = trim($data['email']    ?? '');
$direccion = trim($data['direccion'] ?? '');
$rtn       = trim($data['rtn']      ?? '');

if(!$nombre){
    echo json_encode(["error" => "El nombre es obligatorio"]);
    exit;
}

// Función para buscar cliente por un campo 
function buscarCliente($conn, $campo, $valor){
    $stmt = $conn->prepare("SELECT id_cliente, estado FROM clientes WHERE $campo = ?");
    $stmt->bind_param("s", $valor);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Verificar duplicados por cédula, teléfono, email o RTN 
$clienteExistente = null;

if($cedula !== ''){
    $clienteExistente = buscarCliente($conn, 'cedula', $cedula);
}
if(!$clienteExistente && $telefono !== ''){
    $clienteExistente = buscarCliente($conn, 'telefono', $telefono);
}
if(!$clienteExistente && $email !== ''){
    $clienteExistente = buscarCliente($conn, 'email', $email);
}
if(!$clienteExistente && $rtn !== ''){
    $clienteExistente = buscarCliente($conn, 'rtn', $rtn);
}

if($clienteExistente){
    if($clienteExistente['estado'] == 0){
        // Cliente eliminado  reactivar y actualizar datos
        $stmt = $conn->prepare("UPDATE clientes SET nombre=?, cedula=?, telefono=?, email=?, direccion=?, rtn=?, estado=1 WHERE id_cliente=?");
        $stmt->bind_param("ssssssi", $nombre, $cedula, $telefono, $email, $direccion, $rtn, $clienteExistente['id_cliente']);
        if($stmt->execute()){
            echo json_encode(["mensaje" => "Cliente reactivado nuevamente"]);
        } else {
            echo json_encode(["error" => "Error al reactivar: " . $conn->error]);
        }
    } else {
        // Cliente activo  no duplicar
        echo json_encode(["error" => "Ya existe un cliente activo con esos datos"]);
    }
    exit;
}

//  No existe insertar nuevo 
$stmt = $conn->prepare("INSERT INTO clientes (nombre, cedula, telefono, email, direccion, rtn, estado) VALUES (?, ?, ?, ?, ?, ?, 1)");
$stmt->bind_param("ssssss", $nombre, $cedula, $telefono, $email, $direccion, $rtn);

if($stmt->execute()){
    echo json_encode(["mensaje" => "Cliente guardado con éxito"]);
} else {
    echo json_encode(["error" => "Error al guardar: " . $conn->error]);
}
?>