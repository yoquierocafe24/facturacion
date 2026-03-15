<?php
include "../../backend/config/conexion.php";
header("Content-Type: application/json");

$sql = "SELECT * FROM productos ORDER BY id_producto DESC";
$res = $conn->query($sql);

$data = [];
while($row = $res->fetch_assoc()){
    $data[] = $row;
}

echo json_encode($data);