<?php
session_start();
include "../config/conexion.php";

if($_SESSION['rol'] !== "Administrador"){
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

$data       = json_decode(file_get_contents("php://input"), true);
$id_factura = intval($data['id_factura'] ?? 0);

if(!$id_factura){
    echo json_encode(["error" => "Factura no válida"]);
    exit;
}

$stmt = $conn->prepare("UPDATE facturas SET estado_pago = 'Pagado' WHERE id_factura = ?");
$stmt->bind_param("i", $id_factura);
$stmt->execute();

if($stmt->affected_rows > 0){
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "No se pudo actualizar"]);
}
?>