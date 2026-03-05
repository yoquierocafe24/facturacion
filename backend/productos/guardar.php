<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../config/conexion.php";

$data = json_decode(file_get_contents("php://input"), true);

$nombre = $data['nombre'];
$precio = $data['precio'];
$stock = $data['stock'];
$descripcion = $data['descripcion'];

$stmt = $conn->prepare("INSERT INTO productos(nombre,precio,stock,descripcion,estado)
VALUES(?,?,?,?,1)");

$stmt->bind_param("sdis",$nombre,$precio,$stock,$descripcion);

$stmt->execute();

echo json_encode(["mensaje"=>"ok"]);