<?php
require_once __DIR__ . "/../config/conexion.php";

$data = json_decode(file_get_contents("php://input"), true);

if(!$data){
    echo json_encode(["error" => "No llegan datos"]);
    exit;
}

$id = $data['id_usuario'];

$stmt = $conn->prepare("UPDATE usuarios SET estado=0 WHERE id_usuario=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["mensaje" => "Usuario eliminado"]);
} else {
    echo json_encode(["error" => "Error al eliminar"]);
}
?>