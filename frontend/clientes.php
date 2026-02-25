<?php 
include "../backend/auth/verificar.php";

if($_SESSION['rol'] !== "Administrador"){
    header("Location: dashboard_trabajador.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Clientes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="d-flex">

    <!-- SIDEBAR -->
     <div class="text-white p-3" style="width:250px; min-height:100vh; background-color: #0d3b66;">
        <h4>Panel Admin</h4>
        <hr>

        <p><strong><?php echo $_SESSION['nombre']; ?></strong></p>
        <p>Rol: <?php echo $_SESSION['rol']; ?></p>

        <hr>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-white" href="dashboard_admin.php">Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-warning" href="#">Clientes</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="#">Productos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="#">Facturas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="../backend/auth/logout.php">Cerrar sesión</a>
            </li>
        </ul>
    </div>

    <!-- CONTENIDO -->
    <div class="container-fluid p-4">

        <div class="d-flex justify-content-between align-items-center">
            <h2>Gestión de Clientes</h2>
           <button class="btn text-white" style="background-color: #0d3b66;" data-bs-toggle="collapse" 
        data-bs-target="#formCollapse">
    + Nuevo Cliente
</button>
        </div>

        <!-- FORMULARIO -->
        <div class="collapse mt-3" id="formCollapse">
            <div class="card shadow p-3">
                <form id="formCliente">
                    <input type="hidden" id="id_cliente">

                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" id="nombre" placeholder="Nombre" class="form-control mb-2" required>
                        </div>
                        <div class="col-md-4">
                            <input type="text" id="cedula" placeholder="Cédula" class="form-control mb-2">
                        </div>
                        <div class="col-md-4">
                            <input type="text" id="telefono" placeholder="Teléfono" class="form-control mb-2">
                        </div>
                        <div class="col-md-4">
                            <input type="email" id="email" placeholder="Email" class="form-control mb-2">
                        </div>
                        <div class="col-md-8">
                            <input type="text" id="direccion" placeholder="Dirección" class="form-control mb-2">
                        </div>
                        <div class="col-md-4">
                            <input type="text" id="rtn" placeholder="RTN" class="form-control mb-2">
                        </div>
                    </div>

                   <button class="btn text-white" style="background-color: #0d3b66;">
                    Guardar Cliente </button>
                </form>
            </div>
        </div>

        <!-- TABLA -->
        <div class="card shadow mt-4">
            <div class="card-body">
              <table class="table table-bordered mt-3">
             <thead style="background-color: #0d3b66; color: white;">
                        <tr>
                            <th>Nombre</th>
                            <th>Cédula</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th width="150">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaClientes"></tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
const API = "../backend/clientes/";

document.addEventListener("DOMContentLoaded", listarClientes);

function listarClientes() {
    fetch(API + "listar.php")
    .then(res => res.json())
    .then(data => {

        let tabla = document.getElementById("tablaClientes");
        tabla.innerHTML = "";

        data.forEach(cliente => {
            tabla.innerHTML += `
                <tr>
                    <td>${cliente.nombre}</td>
                    <td>${cliente.cedula}</td>
                    <td>${cliente.telefono}</td>
                    <td>${cliente.email}</td>
                    <td>
                        <button onclick='editar(${JSON.stringify(cliente)})' class="btn btn-warning btn-sm">Editar</button>
                        <button onclick='eliminar(${cliente.id_cliente})' class="btn btn-danger btn-sm">Eliminar</button>
                    </td>
                </tr>
            `;
        });
    });
}

document.getElementById("formCliente").addEventListener("submit", function(e){
    e.preventDefault();

    let id = document.getElementById("id_cliente").value;

    let cliente = {
        id_cliente: id,
        nombre: nombre.value,
        cedula: cedula.value,
        telefono: telefono.value,
        email: email.value,
        direccion: direccion.value,
        rtn: rtn.value
    };

    let url = id ? "actualizar.php" : "insertar.php";

    fetch(API + url, {
        method: "POST",
        body: JSON.stringify(cliente)
    })
    .then(res => res.json())
    .then(() => {
        this.reset();
        listarClientes();
        document.getElementById("formCollapse").classList.remove("show");
    });
});

function editar(cliente) {
    document.getElementById("formCollapse").classList.add("show");

    id_cliente.value = cliente.id_cliente;
    nombre.value = cliente.nombre;
    cedula.value = cliente.cedula;
    telefono.value = cliente.telefono;
    email.value = cliente.email;
    direccion.value = cliente.direccion;
    rtn.value = cliente.rtn;
}

function eliminar(id) {
    if(confirm("¿Eliminar cliente?")){
        fetch(API + "eliminar.php", {
            method: "POST",
            body: JSON.stringify({id_cliente:id})
        })
        .then(res => res.json())
        .then(() => listarClientes());
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>