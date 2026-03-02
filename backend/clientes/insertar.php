<?php
include "../config/conexion.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

$data = json_decode(file_get_contents("php://input"), true);

if(!$data){
    echo json_encode(["error" => "No llegan datos"]);
    exit;
}

$nombre = $data['nombre'];
$cedula = $data['cedula'];
$telefono = $data['telefono'];
$email = $data['email'];
$direccion = $data['direccion'];
$rtn = isset($data['rtn']) ? $data['rtn'] : ""; 

//  Revisar si ya existe cliente por cédula o R.T.N
$stmt = $conn->prepare("SELECT id_cliente, estado FROM clientes WHERE cedula = ? OR rtn = ?");
$stmt->bind_param("ss", $cedula, $rtn);
$stmt->execute();
$result = $stmt->get_result();

if($cliente = $result->fetch_assoc()){
    if($cliente['estado'] == 0){
        // Cliente existe pero estaba inactivo → reactivarlo y actualizar datos
        $stmt = $conn->prepare("UPDATE clientes SET nombre=?, telefono=?, email=?, direccion=?, rtn=?, estado=1 WHERE id_cliente=?");
        $stmt->bind_param("sssssi", $nombre, $telefono, $email, $direccion, $rtn, $cliente['id_cliente']);
        if($stmt->execute()){
            echo json_encode(["mensaje" => "Cliente reactivado y actualizado"]);
        } else {
            echo json_encode(["error" => "Error al reactivar cliente"]);
        }
    } else {
        //  Cliente ya existe activo → no duplicar
        echo json_encode(["error" => "El cliente ya existe"]);
    }
} else {
    // Cliente no existe → insertar normalmente
    $stmt = $conn->prepare("INSERT INTO clientes (nombre, cedula, telefono, email, direccion, rtn, estado) VALUES (?, ?, ?, ?, ?, ?, 1)");
    $stmt->bind_param("ssssss", $nombre, $cedula, $telefono, $email, $direccion, $rtn);
    if ($stmt->execute()) {
        echo json_encode(["mensaje" => "Cliente creado"]);
    } else {
        echo json_encode(["error" => "Error al crear cliente"]);
    }
}



?>