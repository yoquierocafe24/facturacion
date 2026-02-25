<?php
require_once __DIR__ . "/../config/conexion.php";

$sql = "SELECT * FROM usuarios WHERE estado = 1";
$result = $conn->query($sql);

$usuarios = [];

while ($row = $result->fetch_assoc()) {
    $usuarios[] = $row;
}

echo json_encode($usuarios);
?>