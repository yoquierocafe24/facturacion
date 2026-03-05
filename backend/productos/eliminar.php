<?php
include "../config/conexion.php";

// Recibir datos JSON
$data = json_decode(file_get_contents("php://input"), true);

if(!isset($data['id_producto'])){
    echo json_encode(['success'=>false, 'error'=>'ID no proporcionado']);
    exit;
}

$id = intval($data['id_producto']);

$query = $conn->query("DELETE FROM productos WHERE id_producto = $id");

if($query){
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false, 'error'=>$conn->error]);
}
?>