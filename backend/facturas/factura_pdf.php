<?php
require('mc_table.php');
require_once "../config/conexion.php";

if(!isset($_GET['id'])){
    die("Factura no especificada");
}

$id_factura = intval($_GET['id']);

class FacturaPDF extends PDF_MC_Table {
    function Header() {}
    function Footer() {}
}

$pdf = new FacturaPDF('P', 'mm', 'Letter');
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 20);

$margen     = 15;
$pdf->SetMargins($margen, 10, $margen);
$anchoTotal = 216 - ($margen * 2);

// Colores sistema #0d3b66
$navy      = [13,  59,  102];
$navyL     = [37,  99,  166];
$grisF = [240, 244, 249];   // gris azulado suave del sistema
$blanco    = [255, 255, 255];
$negro     = [0,   0,   0];

// ================================
// OBTENER DATOS
// ================================
$empresa = $conn->query("SELECT * FROM empresa LIMIT 1")->fetch_assoc();

$factura = $conn->query("
    SELECT f.*,
           c.nombre    AS cliente,
           c.rtn       AS rtn_cliente,
           c.direccion AS dir_cliente,
           c.telefono  AS tel_cliente
    FROM facturas f
    INNER JOIN clientes c ON c.id_cliente = f.id_cliente
    WHERE f.id_factura = $id_factura
")->fetch_assoc();

$detalle = $conn->query("
    SELECT d.*, p.nombre AS nombre_producto, p.descripcion AS medidas_producto
    FROM detalle_factura d
    INNER JOIN productos p ON p.id_producto = d.id_producto
    WHERE d.id_factura = $id_factura
");

// ================================
// NUMERO A LETRAS
// ================================
function numeroALetras($numero) {
    $numero    = round($numero, 2);
    $entero    = (int) $numero;
    $decimales = round(($numero - $entero) * 100);

    $unidades = ['','UNO','DOS','TRES','CUATRO','CINCO','SEIS','SIETE','OCHO','NUEVE',
                 'DIEZ','ONCE','DOCE','TRECE','CATORCE','QUINCE','DIECISEIS','DIECISIETE',
                 'DIECIOCHO','DIECINUEVE','VEINTE'];
    $decenas  = ['','DIEZ','VEINTE','TREINTA','CUARENTA','CINCUENTA',
                 'SESENTA','SETENTA','OCHENTA','NOVENTA'];
    $centenas = ['','CIENTO','DOSCIENTOS','TRESCIENTOS','CUATROCIENTOS','QUINIENTOS',
                 'SEISCIENTOS','SETECIENTOS','OCHOCIENTOS','NOVECIENTOS'];

    function cg($n, $u, $d, $c) {
        $r = '';
        if ($n == 100) return 'CIEN';
        if ($n > 100) { $r .= $c[(int)($n/100)].' '; $n = $n%100; }
        if ($n <= 20) $r .= $u[$n];
        else { $r .= $d[(int)($n/10)]; if ($n%10>0) $r .= ' Y '.$u[$n%10]; }
        return trim($r);
    }

    $letras = '';
    if ($entero == 0) { $letras = 'CERO'; }
    elseif ($entero < 1000) { $letras = cg($entero,$unidades,$decenas,$centenas); }
    elseif ($entero < 1000000) {
        $miles = (int)($entero/1000); $resto = $entero%1000;
        $letras = ($miles==1?'MIL':cg($miles,$unidades,$decenas,$centenas).' MIL');
        if ($resto>0) $letras .= ' '.cg($resto,$unidades,$decenas,$centenas);
    } else {
        $mill = (int)($entero/1000000); $resto = $entero%1000000;
        $letras = cg($mill,$unidades,$decenas,$centenas).($mill==1?' MILLON':' MILLONES');
        if ($resto>0) {
            $m2=(int)($resto/1000); $r2=$resto%1000;
            if ($m2>0) $letras .= ' '.($m2==1?'MIL':cg($m2,$unidades,$decenas,$centenas).' MIL');
            if ($r2>0) $letras .= ' '.cg($r2,$unidades,$decenas,$centenas);
        }
    }
    $cent = round(($numero - $entero) * 100);
    if ($cent > 0) {
        return trim($letras).' CON '.str_pad($cent,2,'0',STR_PAD_LEFT).'/100 LEMPIRAS';
    }
    return trim($letras).' LEMPIRAS';
}

// ================================
// ENCABEZADO
// ================================
$pdf->SetTextColor(...$navy);
$pdf->SetFont('Arial', 'B', 18);
$pdf->Cell(0, 10, strtoupper($empresa['nombre']), 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 13);
$pdf->Cell(0, 7, 'FACTURA', 0, 1, 'C');

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(...$negro);
$pdf->Cell(0, 5, 'RTN: ' . $empresa['rtn'], 0, 1, 'C');
$pdf->Ln(3);

// Linea separadora
$pdf->SetDrawColor(...$navyL);
$pdf->SetLineWidth(0.6);
$pdf->Line($margen, $pdf->GetY(), $margen + $anchoTotal, $pdf->GetY());
$pdf->Ln(4);

// ================================
// DOS COLUMNAS: factura+fiscal (izq) | empresa contacto (der)
// ================================
$colIzq = $anchoTotal * 0.55;
$colDer = $anchoTotal * 0.45;

$yBloque = $pdf->GetY();

// --- IZQUIERDA: N° Factura, CAI, Rango, Fecha límite ---
$filasIzq = [
    ['N. DE FACTURA:', $factura['numero_factura']],
    ['CAI:',           $empresa['cai']],
    ['Rango Autorizado :',  $empresa['rango_inicial'] . ' al ' . $empresa['rango_final']],
    ['Fecha Lim. Emision:', date('d/m/Y', strtotime($empresa['fecha_limite_emision']))],
];

foreach ($filasIzq as $f) {
    $pdf->SetFont('Arial', 'B', 8.5);
    $pdf->SetTextColor(...$navy);
    $pdf->Cell(30, 6, $f[0], 0, 0, 'L');
    $pdf->SetFont('Arial', '', 8.5);
    $pdf->SetTextColor(...$negro);
    $pdf->Cell($colIzq - 30, 6, $f[1], 0, 1, 'L');
}

// --- DERECHA: Fecha, Dirección, Teléfonos, Correo ---
$camposEmpresa = [
    ['FECHA:',     date('d/m/Y', strtotime($factura['fecha']))],
    ['DIRECCION:', $empresa['direccion']],
    ['TELEFONO:', $empresa['telefono']],
    ['CORREO:',    $empresa['correo']],
];

$yDer = $yBloque;
foreach ($camposEmpresa as $c) {
    $pdf->SetXY($margen + $colIzq, $yDer);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetTextColor(...$navy);
    $pdf->Cell(22, 6, $c[0], 0, 0, 'L');
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetTextColor(...$negro);
    $pdf->Cell($colDer - 22, 6, $c[1], 0, 0, 'L');
    $yDer += 6;
}

// Avanzar al final del bloque más alto
$yFin = max($pdf->GetY(), $yDer);
$pdf->SetY($yFin + 3);

// --- LÍNEA DIVISORIA ---
$pdf->SetDrawColor(...$navyL);
$pdf->SetLineWidth(0.5);
$pdf->Line($margen, $pdf->GetY(), $margen + $anchoTotal, $pdf->GetY());
$pdf->Ln(4);

// ================================
// DATOS CLIENTE
// ================================
$colLabelCli = 28;
$colDataCli  = $anchoTotal - $colLabelCli - 4;

$filaCliente = [
    ['CLIENTE',   $factura['cliente']],
    ['DIRECCION', $factura['dir_cliente'] ?: '----------'],
    ['RTN',       $factura['rtn_cliente']],
    ['TELEFONO', $factura['tel_cliente'] ?: '----------'],
];

foreach ($filaCliente as $idx => $f) {
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetTextColor(...$navy);
    $pdf->Cell(2, 6, '', 0, 0);
    $pdf->Cell($colLabelCli, 6, $f[0], 0, 0, 'L');
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetTextColor(...$negro);
    $pdf->Cell($colDataCli, 6, $f[1], 0, 1, 'L');
}

$pdf->Ln(5);

// ================================
// TABLA PRODUCTOS
// ================================
$colCant    = $anchoTotal * 0.10;
$colDesc    = $anchoTotal * 0.46;
$colMedidas = $anchoTotal * 0.22;
$colTotal   = $anchoTotal * 0.22;

$widths  = [$colCant, $colDesc, $colMedidas, $colTotal];
$headers = ['CANTIDAD', 'DESCRIPCION', 'MEDIDAS', 'TOTAL'];
$aligns  = ['C', 'L', 'C', 'R'];

$pdf->SetFillColor(...$navy);
$pdf->SetTextColor(...$blanco);
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetDrawColor(...$blanco);
$pdf->SetLineWidth(0.3);
foreach ($headers as $i => $titulo) {
    $pdf->Cell($widths[$i], 8, $titulo, 1, 0, 'C', true);
}
$pdf->Ln();

$pdf->SetTextColor(...$negro);
$pdf->SetFont('Arial', '', 9);
$pdf->SetDrawColor(200, 200, 200);
$fill = false;

while ($row = $detalle->fetch_assoc()) {
    $datos = [
        $row['cantidad'],
        $row['nombre_producto'],
        $row['medidas_producto'],
        number_format($row['subtotal'], 2),
    ];
    $bgColor = $fill ? $grisF : $blanco;
    $pdf->SetFillColor(...$bgColor);
    foreach ($datos as $i => $valor) {
        $pdf->Cell($widths[$i], 7, $valor, 1, 0, $aligns[$i], true);
    }
    $pdf->Ln();
    $fill = !$fill;
}

$pdf->Ln(3);

// ================================
// CONDICIONES + TOTALES
// ================================
$yBottom     = $pdf->GetY();
$colIzq      = $anchoTotal * 0.54;
$colDerLabel = 30;
$colDerVal   = $anchoTotal - $colIzq - $colDerLabel;

$pdf->SetFont('Arial', 'I', 8);
$pdf->SetTextColor(...$negro);
$pdf->SetDrawColor(200, 200, 200);
$pdf->SetLineWidth(0.3);
$condiciones = "Esta factura esta sujeta a terminos y condiciones.\n1. Entregado el trabajo o ejecutado el servicio no existen devoluciones\n2. Duracion de la oferta 15 dias.";
$pdf->MultiCell($colIzq, 5, $condiciones, 1, 'L', false);

$totales = [
    ['SUB TOTAL', number_format($factura['subtotal'], 2), false],
    ['ISV',       number_format($factura['isv15'] + $factura['isv18'], 2), false],
    ['TOTAL',     number_format($factura['total'], 2), true],
];

$pdf->SetXY($margen + $colIzq, $yBottom);
foreach ($totales as $t) {
    $pdf->SetFillColor(...$navy);
    $pdf->SetTextColor(...$blanco);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetDrawColor(...$navyL);
    $pdf->SetLineWidth(0.3);
    $pdf->Cell($colDerLabel, 7, $t[0], 1, 0, 'C', true);
    $pdf->SetFillColor(...$blanco);
    $pdf->SetTextColor(...$negro);
    $pdf->SetFont('Arial', $t[2] ? 'B' : '', 9);
    $pdf->Cell($colDerVal, 7, $t[1], 1, 1, 'R', true);
    $pdf->SetX($margen + $colIzq);
}

// SON: justo debajo de los totales, alineado a la derecha
$yDespTotales = $pdf->GetY() + 2;
$pdf->SetXY($margen + $colIzq, $yDespTotales);
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetTextColor(...$navy);
$pdf->Cell(12, 5, 'SON:', 0, 0, 'L');
$pdf->SetFont('Arial', 'I', 8);
$pdf->SetTextColor(...$negro);
$pdf->Cell($colDerLabel + $colDerVal - 12, 5, numeroALetras($factura['total']), 0, 1, 'L');

// ================================
$pdf->Output();
?>