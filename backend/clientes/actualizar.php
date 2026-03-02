<?php
include "../config/conexion.php";

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id_cliente'];
$nombre = $data['nombre'];
$cedula = $data['cedula'];
$telefono = $data['telefono'];
$email = $data['email'];
$direccion = $data['direccion'];
$rtn = $data['rtn'];  

$stmt = $conn->prepare("UPDATE clientes 
SET nombre=?, cedula=?, telefono=?, email=?, direccion=?, rtn=? 
WHERE id_cliente=?");

$stmt->bind_param("ssssssi", $nombre, $cedula, $telefono, $email, $direccion, $rtn, $id);

if ($stmt->execute()) {
    echo json_encode(["mensaje" => "Cliente actualizado"]);
} else {
    echo json_encode(["error" => "Error al actualizar"]);
}
?>