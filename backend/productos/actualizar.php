<?php
include "../../backend/config/conexion.php";
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$id          = intval($data['id_producto']);
$nombre      = $conn->real_escape_string($data['nombre']);
$precio      = floatval($data['precio']);
$stock       = intval($data['stock']);
$estado      = intval($data['estado']);
$descripcion = $conn->real_escape_string($data['descripcion'] ?? '');

$sql = "UPDATE productos 
        SET nombre='$nombre', precio=$precio, stock=$stock, estado=$estado, descripcion='$descripcion'
        WHERE id_producto=$id";

if($conn->query($sql)){
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => $conn->error]);
}