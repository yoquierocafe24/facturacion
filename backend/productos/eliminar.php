<?php
include "../../backend/config/conexion.php";
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
$id   = intval($data['id_producto']);

// Desactivar en vez de eliminar físicamente
$sql = "UPDATE productos SET estado = 0 WHERE id_producto = $id";

if($conn->query($sql)){
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => $conn->error]);
}