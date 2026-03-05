<?php
include "../config/conexion.php";

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id_factura'];

$conn->query("DELETE FROM facturas WHERE id_factura = $id");

echo json_encode(["success"=>true]);
?>