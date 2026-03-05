<?php
require('mc_table.php');
require_once "../config/conexion.php";

if(!isset($_GET['id'])){
    die("Factura no especificada");
}

$id_factura = intval($_GET['id']);

$pdf = new PDF_MC_Table('P','mm','Letter');
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 20);

$margen     = 15;
$pdf->SetMargins($margen, 10, $margen);
$anchoTotal = 216 - ($margen * 2); // ~186mm ancho útil

// ================================
// OBTENER DATOS
// ================================

$empresa = $conn->query("SELECT * FROM empresa LIMIT 1")->fetch_assoc();

$factura = $conn->query("
    SELECT f.*, c.nombre as cliente, c.rtn
    FROM facturas f
    INNER JOIN clientes c ON c.id_cliente = f.id_cliente
    WHERE f.id_factura = $id_factura
")->fetch_assoc();

$detalle = $conn->query("
    SELECT d.*, p.nombre as producto
    FROM detalle_factura d
    INNER JOIN productos p ON p.id_producto = d.id_producto
    WHERE d.id_factura = $id_factura
");

// ================================
// ENCABEZADO EMPRESA
// ================================

$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 7, $empresa['nombre'], 0, 1, 'C');

$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 5, "RTN: " . $empresa['rtn'], 0, 1, 'C');
$pdf->Cell(0, 5, $empresa['direccion'], 0, 1, 'C');
$pdf->Cell(0, 5, "Tel: " . $empresa['telefono'] . "  -  Correo: " . $empresa['correo'], 0, 1, 'C');

// Línea separadora
$pdf->Ln(3);
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetLineWidth(0.5);
$pdf->Line($margen, $pdf->GetY(), $margen + $anchoTotal, $pdf->GetY());
$pdf->Ln(3);

// Datos fiscales
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 5, "CAI: " . $empresa['cai'], 0, 1, 'C');
$pdf->Cell($anchoTotal / 2, 5, "Rango autorizado: " . $empresa['rango_inicial'] . " al " . $empresa['rango_final'], 0, 0, 'L');
$pdf->Cell($anchoTotal / 2, 5, "Fecha limite emision: " . $empresa['fecha_limite_emision'], 0, 1, 'R');

// Línea separadora
$pdf->Ln(2);
$pdf->SetLineWidth(0.4);
$pdf->Line($margen, $pdf->GetY(), $margen + $anchoTotal, $pdf->GetY());
$pdf->Ln(4);

// ================================
// DATOS FACTURA Y CLIENTE
// ================================

$pdf->SetFont('Arial', '', 10);
$pdf->Cell($anchoTotal / 2, 6, "Factura No: " . $factura['numero_factura'], 0, 0, 'L');
$pdf->Cell($anchoTotal / 2, 6, "Fecha: " . date('d/m/Y', strtotime($factura['fecha'])), 0, 1, 'R');

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell($anchoTotal / 2, 6, "Cliente: " . $factura['cliente'], 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell($anchoTotal / 2, 6, "RTN Cliente: " . $factura['rtn'], 0, 1, 'R');

$pdf->Ln(4);

// ================================
// TABLA PRODUCTOS
// ================================

$colProducto = $anchoTotal * 0.40;
$colCant     = $anchoTotal * 0.10;
$colPrecio   = $anchoTotal * 0.18;
$colISV      = $anchoTotal * 0.16;
$colSubtotal = $anchoTotal * 0.16;

$widths  = [$colProducto, $colCant, $colPrecio, $colISV, $colSubtotal];
$headers = ["Producto", "Cant", "Precio", "ISV", "Subtotal"];
$aligns  = ['L', 'C', 'R', 'R', 'R'];

// Encabezado dibujado con Cell() - simple, sin color
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetLineWidth(0.4);

foreach ($headers as $i => $titulo) {
    $pdf->Cell($widths[$i], 7, $titulo, 1, 0, 'C', true);
}
$pdf->Ln();

// Filas de productos
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetLineWidth(0.4);
$fill = false;

while ($row = $detalle->fetch_assoc()) {
    $pdf->SetFillColor($fill ? 240 : 255, $fill ? 240 : 255, $fill ? 240 : 255);
    $datos = [
        $row['producto'],
        $row['cantidad'],
        number_format($row['precio_unitario'], 2),
        number_format($row['impuesto'], 2),
        number_format($row['subtotal'], 2)
    ];
    foreach ($datos as $i => $valor) {
        $pdf->Cell($widths[$i], 6, $valor, 1, 0, $aligns[$i], true);
    }
    $pdf->Ln();
    $fill = !$fill;
}

$pdf->Ln(4);

// ================================
// TOTALES
// ================================

$colLabel = 35;
$colValue = 35;
$offsetX  = $anchoTotal - $colLabel - $colValue;

$pdf->SetFont('Arial', '', 10);

$pdf->Cell($offsetX, 6, '', 0, 0);
$pdf->Cell($colLabel, 6, "Subtotal:", 0, 0, 'L');
$pdf->Cell($colValue, 6, number_format($factura['subtotal'], 2), 0, 1, 'R');

$pdf->Cell($offsetX, 6, '', 0, 0);
$pdf->Cell($colLabel, 6, "ISV 15%:", 0, 0, 'L');
$pdf->Cell($colValue, 6, number_format($factura['isv15'], 2), 0, 1, 'R');

$pdf->Cell($offsetX, 6, '', 0, 0);
$pdf->Cell($colLabel, 6, "ISV 18%:", 0, 0, 'L');
$pdf->Cell($colValue, 6, number_format($factura['isv18'], 2), 0, 1, 'R');

// Línea antes del TOTAL
$pdf->SetLineWidth(0.6);
$pdf->Line($margen + $offsetX, $pdf->GetY(), $margen + $anchoTotal, $pdf->GetY());
$pdf->Ln(1);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell($offsetX, 7, '', 0, 0);
$pdf->Cell($colLabel, 7, "TOTAL:", 0, 0, 'L');
$pdf->Cell($colValue, 7, number_format($factura['total'], 2), 0, 1, 'R');


$pdf->Output();
?>