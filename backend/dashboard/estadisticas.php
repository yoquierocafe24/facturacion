<?php
include "../config/conexion.php";

$data = [];

// Total facturas
$res1 = $conn->query("SELECT COUNT(*) as total FROM facturas");
$data['facturas'] = $res1->fetch_assoc()['total'];

// Total productos activos
$res2 = $conn->query("SELECT COUNT(*) as total FROM productos WHERE estado=1");
$data['productos'] = $res2->fetch_assoc()['total'];

// Facturas pendientes
$res3 = $conn->query("SELECT COUNT(*) as total FROM facturas WHERE estado_pago = 'Pendiente'");
$data['pendientes'] = $res3->fetch_assoc()['total'];

// Ventas del mes actual
$res4 = $conn->query("SELECT COALESCE(SUM(total), 0) as total FROM facturas 
                       WHERE MONTH(fecha) = MONTH(CURDATE()) 
                       AND YEAR(fecha) = YEAR(CURDATE())");
$data['ventas_mes'] = $res4->fetch_assoc()['total'];

// Ventas por mes (todos los meses del año actual) para el gráfico
$res5 = $conn->query("
    SELECT m.mes, m.num_mes, COALESCE(SUM(f.total), 0) as total
    FROM (
        SELECT 1 as num_mes, 'Ene' as mes UNION SELECT 2,'Feb' UNION SELECT 3,'Mar'
        UNION SELECT 4,'Abr' UNION SELECT 5,'May' UNION SELECT 6,'Jun'
        UNION SELECT 7,'Jul' UNION SELECT 8,'Ago' UNION SELECT 9,'Sep'
        UNION SELECT 10,'Oct' UNION SELECT 11,'Nov' UNION SELECT 12,'Dic'
    ) m
    LEFT JOIN facturas f ON MONTH(f.fecha) = m.num_mes AND YEAR(f.fecha) = YEAR(CURDATE())
    GROUP BY m.num_mes, m.mes
    ORDER BY m.num_mes ASC
");
$data['grafico_meses']  = [];
$data['grafico_ventas'] = [];
while($row = $res5->fetch_assoc()){
    $data['grafico_meses'][]  = $row['mes'];
    $data['grafico_ventas'][] = (float)$row['total'];
}

echo json_encode($data);
?>