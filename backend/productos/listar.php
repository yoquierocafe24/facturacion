<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../config/conexion.php";

$result = $conn->query("SELECT * FROM productos WHERE estado=1");

$productos = [];

while($row = $result->fetch_assoc()){

$productos[] = $row;

}

echo json_encode($productos);