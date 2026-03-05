<?php
include "../backend/auth/verificar.php";

// Solo el administrador puede acceder

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Productos</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background-color:#f4f6f9;">

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
            <a class="nav-link text-white" href="productos.php">📦 Productos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="facturar.php">🧾 Facturas</a>
        </li>

          <?php if($_SESSION['rol'] == "Administrador"){ ?>
         <li class="nav-item">
              <a class="nav-link text-white" href="historial_facturas.php">📋 Historial</a>
        </li>
        <?php } ?>

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

    <h3 class="mb-4" style="color:#0d3b66;">Gestión de Productos</h3>

    <!-- BOTÓN PARA MOSTRAR FORMULARIO -->
    <button class="btn text-white mb-3"
            style="background-color:#0d3b66;"
            data-bs-toggle="collapse"
            data-bs-target="#formCollapse">
        + Nuevo Producto
    </button>

    <!-- FORMULARIO OCULTO -->
    <div class="collapse" id="formCollapse">
        <div class="card shadow p-3 mb-3">
            <form id="formProducto">
                <input type="hidden" id="id_producto">

                <div class="row">
                    <div class="col-md-3">
                        <input type="text" id="nombre" class="form-control mb-2" placeholder="Nombre" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" id="precio" class="form-control mb-2" placeholder="Precio" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" id="stock" class="form-control mb-2" placeholder="Stock" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" id="descripcion" class="form-control mb-2" placeholder="Descripción" required>
                    </div>
                </div>

                <button class="btn text-white" style="background-color:#0d3b66;">Guardar Producto</button>
            </form>
        </div>
    </div>

    <!-- TABLA DE PRODUCTOS -->
    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered">
                <thead style="background-color:#0d3b66; color:white;">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Descripción</th>
                        <th width="120">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaProductos"></tbody>
            </table>
        </div>
    </div>

</div>
</div>

<script>
const API = "../backend/productos/";

// LISTAR PRODUCTOS
function listarProductos(){
    fetch(API + "listar.php")
    .then(res => res.json())
    .then(data => {
        let tabla = document.getElementById("tablaProductos");
        tabla.innerHTML = "";

        data.forEach(p => {
            tabla.innerHTML += `
<tr>
<td>${p.id_producto}</td>
<td>${p.nombre}</td>
<td>L ${p.precio}</td>
<td>${p.stock}</td>
<td>${p.descripcion}</td>
<td class="d-flex gap-1">
<button onclick='editar(${JSON.stringify(p)})' class="btn btn-sm" style="background-color:#2e86c1; color:white;">Editar</button>
<button onclick="eliminar(${p.id_producto})" class="btn btn-sm" style="background-color:#616a6b; color:white;">Eliminar</button>
</td>
</tr>
`;
        });
    });
}

// GUARDAR PRODUCTO
document.getElementById("formProducto").addEventListener("submit", function(e){
    e.preventDefault();

    let id = document.getElementById("id_producto").value;
    let url = id ? "actualizar.php" : "guardar.php"; // decidir si insertar o actualizar

    let datos = {
        id_producto: id,
        nombre: document.getElementById("nombre").value.trim(),
        precio: document.getElementById("precio").value.trim(),
        stock: document.getElementById("stock").value.trim(),
        descripcion: document.getElementById("descripcion").value.trim()
    };

    // Validar campos obligatorios
    if(!datos.nombre || !datos.precio || !datos.stock || !datos.descripcion){
        alert("Todos los campos son obligatorios.");
        return;
    }

    fetch(API + url, {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify(datos)
    })
    .then(res => res.json())
    .then(data => {
        alert(data.mensaje || data.error);
        if(data.mensaje){
            this.reset(); // limpiar formulario
            bootstrap.Collapse.getOrCreateInstance(
                document.getElementById("formCollapse")
            ).hide(); // ocultar formulario
            listarProductos(); // recargar tabla
        }
    });
});

// EDITAR PRODUCTO
function editar(p){
    document.getElementById("id_producto").value = p.id_producto;
    document.getElementById("nombre").value = p.nombre;
    document.getElementById("precio").value = p.precio;
    document.getElementById("stock").value = p.stock;
    document.getElementById("descripcion").value = p.descripcion;

    // mostrar formulario al editar
    bootstrap.Collapse.getOrCreateInstance(
        document.getElementById("formCollapse")
    ).show();
}

// ELIMINAR PRODUCTO
function eliminar(id){
    if(confirm("¿Eliminar producto?")){
        fetch(API + "eliminar.php", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({id_producto: id})
        })
        .then(res => res.json())
        .then(data => {
            alert(data.mensaje || data.error);
            listarProductos();
        });
    }
}

// CARGAR AL INICIAR
document.addEventListener("DOMContentLoaded", listarProductos);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>