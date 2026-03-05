<?php
include "../config/conexion.php";

$sql = "SELECT f.id_factura, f.numero_factura, 
c.nombre as cliente, f.total, f.fecha
FROM facturas f
INNER JOIN clientes c ON f.id_cliente = c.id_cliente
ORDER BY f.id_factura DESC";

$result = $conn->query($sql);

$data = [];

while($row = $result->fetch_assoc()){
    $data[] = $row;
}

echo json_encode($data);
?>