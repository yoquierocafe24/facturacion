<?php
session_start();
include "../config/conexion.php";

$data = json_decode(file_get_contents("php://input"), true);

$id_cliente  = $data['id_cliente']  ?? null;
$productos   = $data['productos']   ?? [];
$estado_pago = $data['estado_pago'] ?? 'Pagado';
$id_usuario  = $_SESSION['id_usuario'] ?? null;

// Validaciones básicas
if(!$id_usuario){
    echo json_encode(["error" => "Sesión no válida"]);
    exit;
}
if(empty($id_cliente)){
    echo json_encode(["error" => "Debe seleccionar un cliente"]);
    exit;
}
if(empty($productos)){
    echo json_encode(["error" => "No hay productos en la factura"]);
    exit;
}

$conn->begin_transaction();

try {
    // Obtener datos de la empresa
    $res     = $conn->query("SELECT * FROM empresa LIMIT 1");
    $empresa = $res->fetch_assoc();

 // Extraer el número del rango final (los últimos 8 dígitos)
$rangoFinalNum = (int) substr($empresa['rango_final'], strrpos($empresa['rango_final'], '-') + 1);

if($empresa['numero_actual'] > $rangoFinalNum){
    throw new Exception("Rango fiscal vencido");
}
    $correlativo   = str_pad($empresa['numero_actual'], 8, "0", STR_PAD_LEFT);
    $numeroFactura = $empresa['establecimiento'] . "-" .
                     $empresa['punto_emision']   . "-" .
                     $empresa['tipo_documento']  . "-" .
                     $correlativo;

    // Calcular totales
    $subtotal_general = 0;
    $total_isv15      = 0;
    $total_isv18      = 0;

    foreach($productos as $p){
        $stmtStock = $conn->prepare("SELECT stock FROM productos WHERE id_producto = ?");
        $stmtStock->bind_param("i", $p['id_producto']);
        $stmtStock->execute();
        $productoDB = $stmtStock->get_result()->fetch_assoc();

        if(!$productoDB || $productoDB['stock'] < $p['cantidad']){
            throw new Exception("Stock insuficiente para un producto");
        }

        $subtotal = $p['cantidad'] * $p['precio'];

        if($p['tipo_isv'] == '15'){
            $impuesto = $subtotal * 0.15;
            $total_isv15 += $impuesto;
        } elseif($p['tipo_isv'] == '18'){
            $impuesto = $subtotal * 0.18;
            $total_isv18 += $impuesto;
        } else {
            $impuesto = 0;
        }

        $subtotal_general += $subtotal;
    }

    $total_factura = $subtotal_general + $total_isv15 + $total_isv18;

    // Insertar factura CON estado_pago
    $stmt = $conn->prepare("INSERT INTO facturas
        (numero_factura, id_cliente, id_usuario, subtotal, isv15, isv18, total, estado_pago)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("siiiddds",
        $numeroFactura,
        $id_cliente,
        $id_usuario,
        $subtotal_general,
        $total_isv15,
        $total_isv18,
        $total_factura,
        $estado_pago
    );

    $stmt->execute();
    $id_factura = $conn->insert_id;

    // Insertar detalles y actualizar stock
    foreach($productos as $p){
        $subtotal = $p['cantidad'] * $p['precio'];
        $impuesto = 0;
        if($p['tipo_isv'] == '15') $impuesto = $subtotal * 0.15;
        if($p['tipo_isv'] == '18') $impuesto = $subtotal * 0.18;

        $stmtDetalle = $conn->prepare("INSERT INTO detalle_factura
            (id_factura, id_producto, cantidad, precio_unitario, tipo_isv, impuesto, subtotal)
            VALUES (?, ?, ?, ?, ?, ?, ?)");

        $stmtDetalle->bind_param("iiidsdd",
            $id_factura,
            $p['id_producto'],
            $p['cantidad'],
            $p['precio'],
            $p['tipo_isv'],
            $impuesto,
            $subtotal
        );
        $stmtDetalle->execute();

        $stmtUpdate = $conn->prepare("UPDATE productos SET stock = stock - ? WHERE id_producto = ?");
        $stmtUpdate->bind_param("ii", $p['cantidad'], $p['id_producto']);
        $stmtUpdate->execute();
    }

    // Incrementar correlativo
    $conn->query("UPDATE empresa SET numero_actual = numero_actual + 1");

    $conn->commit();

    echo json_encode([
        "success"        => true,
        "id_factura"     => $id_factura,
        "numero_factura" => $numeroFactura
    ]);

} catch(Exception $e){
    $conn->rollback();
    echo json_encode(["error" => $e->getMessage()]);
}
?>