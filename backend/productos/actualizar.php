<?php
include "../config/conexion.php";

$data = json_decode(file_get_contents("php://input"), true);

if(!isset($data['id_producto'])){
    echo json_encode(['success'=>false, 'error'=>'ID no proporcionado']);
    exit;
}

$id = intval($data['id_producto']);
$nombre = $conn->real_escape_string($data['nombre']);
$precio = floatval($data['precio']);
$stock = intval($data['stock']);
$descripcion = $conn->real_escape_string($data['descripcion']);

$query = $conn->query("
    UPDATE productos 
    SET nombre='$nombre', precio=$precio, stock=$stock, descripcion='$descripcion'
    WHERE id_producto=$id
");

if($query){
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false, 'error'=>$conn->error]);
}
?>