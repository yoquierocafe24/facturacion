<?php
include "../../backend/config/conexion.php";
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$nombre      = $conn->real_escape_string($data['nombre']);
$precio      = floatval($data['precio']);
$stock       = intval($data['stock']);
$estado      = intval($data['estado']);
$descripcion = $conn->real_escape_string($data['descripcion'] ?? '');

$sql = "INSERT INTO productos (nombre, precio, stock, estado, descripcion)
        VALUES ('$nombre', $precio, $stock, $estado, '$descripcion')";

if($conn->query($sql)){
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => $conn->error]);
}