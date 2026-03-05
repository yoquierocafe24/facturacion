<?php 
include "../backend/auth/verificar.php";

if($_SESSION['rol'] !== "Administrador" && $_SESSION['rol'] !== "Trabajador"){
    header("Location: dashboard_admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Facturación</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="d-flex">

<!-- SIDEBAR -->
<div class="text-white p-3" style="width:250px; min-height:100vh; background-color:#0d3b66;">
    
    <!-- EMPRESA -->
    <div class="text-center mb-3">
        <div style="font-size:2rem;">🪟</div>
        <h5 class="mb-0 fw-bold">Vidrería George</h5>
        <small style="color:#a8c7e8;">Sistema de Facturación</small>
    </div>

    <hr style="border-color:#ffffff30;">

    <!-- USUARIO -->
    <div class="d-flex align-items-center mb-3">
        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold me-2"
             style="width:40px; height:40px; background-color:#1a5276; font-size:1rem;">
            <?php echo strtoupper(substr($_SESSION['nombre'], 0, 2)); ?>
        </div>
        <div>
            <div class="fw-bold" style="font-size:0.9rem;"><?php echo $_SESSION['nombre']; ?></div>
            <small style="color:#a8c7e8;"><?php echo $_SESSION['rol']; ?></small>
        </div>
    </div>

    <hr style="border-color:#ffffff30;">

    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link text-white" href="dashboard_admin.php">📊 Dashboard</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="clientes.php">👥 Clientes</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="#">📦 Productos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="#">🧾 Facturas</a>
        </li>
        <?php if($_SESSION['rol'] == "Administrador"){ ?>
        <li class="nav-item">
            <a class="nav-link text-white" href="usuarios.php">👤 Usuarios</a>
        </li>
        <?php } ?>
        <li class="nav-item mt-3">
            <a class="nav-link text-danger" href="../backend/auth/logout.php">🚪 Cerrar sesión</a>
        </li>
    </ul>
</div>

<!-- CONTENIDO -->
<div class="container-fluid p-4">

<h2>Nueva Factura</h2>

<!-- Selección Cliente -->
<div class="card shadow p-3 mb-4">
    <label>Cliente</label>
    <select id="cliente" class="form-select"></select>
</div>

<!-- Agregar Productos -->
<div class="card shadow p-3 mb-4">
    <div class="row">
        <div class="col-md-3">
            <select id="producto" class="form-select"></select>
        </div>
        <div class="col-md-2">
            <input type="number" id="cantidad" class="form-control" placeholder="Cantidad">
        </div>
        <div class="col-md-2">
            <input type="number" id="precio" class="form-control" placeholder="Precio">
        </div>
        <div class="col-md-2">
            <select id="tipo_isv" class="form-select">
                <option value="15">ISV 15%</option>
                <option value="18">ISV 18%</option>
                <option value="Exento">Exento</option>
            </select>
        </div>
        <div class="col-md-2">
            <button onclick="agregarProducto()" class="btn btn-primary w-100">
                Agregar
            </button>
        </div>
    </div>
</div>

<!-- Tabla Productos -->
<div class="card shadow-lg border-0">
<div class="card-body">

<div class="table-responsive">
<table class="table table-hover align-middle text-center">
    <thead class="table-dark">
        <tr>
            <th>Producto</th>
            <th>Descripción</th>
            <th>Cant</th>
            <th>Precio</th>
            <th>ISV</th>
            <th>Impuesto</th>
            <th>Total</th>
            <th></th>
        </tr>
    </thead>
    <tbody id="tablaProductos"></tbody>
</table>
</div>

<!-- Totales mejorados -->
<div class="row justify-content-end mt-4">
    <div class="col-md-4">
        <div class="card bg-light p-3 shadow-sm">
            <p class="d-flex justify-content-between">
                <span>Subtotal:</span>
                <strong id="subtotal">L 0.00</strong>
            </p>

            <p class="d-flex justify-content-between">
                <span>ISV 15%:</span>
                <strong id="isv15">L 0.00</strong>
            </p>

            <p class="d-flex justify-content-between">
                <span>ISV 18%:</span>
                <strong id="isv18">L 0.00</strong>
            </p>

            <hr>

            <h5 class="d-flex justify-content-between">
                <span>Total:</span>
                <strong class="text-success" id="total">L 0.00</strong>
            </h5>
        </div>
    </div>
</div>

<button onclick="guardarFactura()" class="btn btn-success mt-4 px-4">
    💾 Guardar Factura
</button>

</div>
</div>


<script>

let productos = [];

// Cargar clientes
fetch("../backend/clientes/listar.php")
.then(res=>res.json())
.then(data=>{
    let select = document.getElementById("cliente");
    data.forEach(c=>{
        select.innerHTML += `<option value="${c.id_cliente}">
            ${c.nombre} - ${c.rtn}
        </option>`;
    });
});

// Cargar productos
fetch("../backend/productos/listar.php")
.then(res=>res.json())
.then(data=>{
    let select = document.getElementById("producto");
    data.forEach(p=>{
        select.innerHTML += `
        <option value="${p.id_producto}" 
            data-precio="${p.precio}"
            data-descripcion="${p.descripcion}">
            ${p.nombre}
        </option>`;
    });
});

document.getElementById("producto").addEventListener("change", function(){
    let precio = this.selectedOptions[0].dataset.precio;
    let descripcion = this.selectedOptions[0].dataset.descripcion;
    document.getElementById("precio").value = precio;
});

function agregarProducto(){

    let prodSelect = document.getElementById("producto");

    let producto = {
        id_producto: prodSelect.value,
        nombre: prodSelect.selectedOptions[0].text,
        descripcion: prodSelect.selectedOptions[0].dataset.descripcion,
        cantidad: parseFloat(cantidad.value),
        precio: parseFloat(precio.value),
        tipo_isv: tipo_isv.value
    };

    productos.push(producto);
    renderTabla();
}

function renderTabla(){

    let tabla = document.getElementById("tablaProductos");
    tabla.innerHTML = "";

    let subtotal=0;
    let total15=0;
    let total18=0;

    productos.forEach((p,index)=>{

        let sub = p.cantidad * p.precio;
        let impuesto = 0;

        if(p.tipo_isv == "15"){
            impuesto = sub * 0.15;
            total15 += impuesto;
        }
        else if(p.tipo_isv == "18"){
            impuesto = sub * 0.18;
            total18 += impuesto;
        }

        subtotal += sub;

        tabla.innerHTML += `
        <tr>
            <td><strong>${p.nombre}</strong></td>
            <td class="text-muted">${p.descripcion ?? ''}</td>
            <td>${p.cantidad}</td>
            <td>L ${p.precio.toFixed(2)}</td>
            <td>${p.tipo_isv}%</td>
            <td>L ${impuesto.toFixed(2)}</td>
            <td class="fw-bold">L ${(sub+impuesto).toFixed(2)}</td>
            <td>
                <button class="btn btn-danger btn-sm" onclick="eliminar(${index})">
                    ✕
                </button>
            </td>
        </tr>`;
    });

    document.getElementById("subtotal").innerText = "L " + subtotal.toFixed(2);
    document.getElementById("isv15").innerText = "L " + total15.toFixed(2);
    document.getElementById("isv18").innerText = "L " + total18.toFixed(2);
    document.getElementById("total").innerText =
        "L " + (subtotal + total15 + total18).toFixed(2);
}

function eliminar(index){
    productos.splice(index,1);
    renderTabla();
}

function guardarFactura(){

    fetch("../backend/facturas/crear.php",{
        method:"POST",
        body: JSON.stringify({
            id_cliente: cliente.value,
            productos: productos
        })
    })
    .then(res=>res.json())
    .then(data=>{
        if(data.success){
            alert("Factura creada correctamente");
            location.reload();
        }else{
            alert("Error: "+data.error);
        }
    });
}

</script>

</body>
</html>