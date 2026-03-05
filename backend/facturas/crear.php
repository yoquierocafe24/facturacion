<?php
session_start();
include "../config/conexion.php";

$data = json_decode(file_get_contents("php://input"), true);

$id_cliente = $data['id_cliente'];
$productos = $data['productos'];
$id_usuario = $_SESSION['id_usuario'];

$conn->begin_transaction();

try {

    // 🔹 Obtener datos empresa
    $res = $conn->query("SELECT * FROM empresa LIMIT 1");
    $empresa = $res->fetch_assoc();

    if($empresa['numero_actual'] > $empresa['rango_final']){
        throw new Exception("Rango vencido");
    }

    $correlativo = str_pad($empresa['numero_actual'],8,"0",STR_PAD_LEFT);

    $numeroFactura =
        $empresa['establecimiento']."-".
        $empresa['punto_emision']."-".
        $empresa['tipo_documento']."-".
        $correlativo;

    // 🔹 Totales
    $subtotal_general = 0;
    $total_isv15 = 0;
    $total_isv18 = 0;

    foreach($productos as $p){

        $subtotal = $p['cantidad'] * $p['precio'];

        if($p['tipo_isv'] == '15'){
            $impuesto = $subtotal * 0.15;
            $total_isv15 += $impuesto;
        }
        elseif($p['tipo_isv'] == '18'){
            $impuesto = $subtotal * 0.18;
            $total_isv18 += $impuesto;
        }
        else{
            $impuesto = 0;
        }

        $subtotal_general += $subtotal;
    }

    $total_factura = $subtotal_general + $total_isv15 + $total_isv18;

    // 🔹 Insertar factura
    $stmt = $conn->prepare("INSERT INTO facturas
    (numero_factura,id_cliente,id_usuario,subtotal,isv15,isv18,total)
    VALUES (?,?,?,?,?,?,?)");

    $stmt->bind_param("siiiddd",
        $numeroFactura,
        $id_cliente,
        $id_usuario,
        $subtotal_general,
        $total_isv15,
        $total_isv18,
        $total_factura
    );

    $stmt->execute();
    $id_factura = $conn->insert_id;

    // 🔹 Insertar detalles
    foreach($productos as $p){

        $subtotal = $p['cantidad'] * $p['precio'];

        if($p['tipo_isv'] == '15'){
            $impuesto = $subtotal * 0.15;
        } elseif($p['tipo_isv'] == '18'){
            $impuesto = $subtotal * 0.18;
        } else{
            $impuesto = 0;
        }

        $stmtDetalle = $conn->prepare("INSERT INTO detalle_factura
        (id_factura,id_producto,cantidad,precio_unitario,tipo_isv,impuesto,subtotal)
        VALUES (?,?,?,?,?,?,?)");

        $stmtDetalle->bind_param("iiidddd",
            $id_factura,
            $p['id_producto'],
            $p['cantidad'],
            $p['precio'],
            $p['tipo_isv'],
            $impuesto,
            $subtotal
        );

        $stmtDetalle->execute();

        // 🔹 Actualizar stock
        $conn->query("UPDATE productos 
                      SET stock = stock - {$p['cantidad']}
                      WHERE id_producto = {$p['id_producto']}");
    }

    // 🔹 Incrementar correlativo
    $conn->query("UPDATE empresa SET numero_actual = numero_actual + 1");

    $conn->commit();

    echo json_encode(["success"=>true]);

} catch(Exception $e){
    $conn->rollback();
    echo json_encode(["error"=>$e->getMessage()]);
}
?>