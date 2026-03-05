<?php
include "../backend/auth/verificar.php";

if($_SESSION['rol'] != "Administrador"){
    header("Location: dashboard_trabajador.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Usuarios</title>
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
            <a class="nav-link text-white" href="#">📦 Productos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="facturar.php">🧾 Facturas</a>
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

        <h3 class="mb-4" style="color:#0d3b66;">Gestión de Usuarios</h3>

        <button class="btn text-white mb-3"
                style="background-color:#0d3b66;"
                data-bs-toggle="collapse"
                data-bs-target="#formCollapse">
            + Nuevo Usuario
        </button>

        <!-- FORMULARIO -->
        <div class="collapse" id="formCollapse">
            <div class="card shadow p-3 mb-3">
                <form id="formUsuario">

                    <input type="hidden" id="id_usuario">

                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" id="nombre" class="form-control mb-2" placeholder="Nombre" required>
                        </div>

                        <div class="col-md-4">
                            <input type="text" id="usuario" class="form-control mb-2" placeholder="Usuario" required>
                        </div>
                        

                        <div class="col-md-4">
                            <input type="password" id="password" class="form-control mb-2" placeholder="Contraseña" required>
                        </div>

                        <div class="col-md-4">
                            <select id="id_rol" class="form-control mb-2" required> 
                                <option value="">Seleccione un Rol</option>
                                <option value="1">Administrador</option> <!-- 1 -->
                                <option value="2">Trabajador</option> <!-- 2 -->
                            </select>
                        </div>
                    </div>

                    <button class="btn text-white" style="background-color:#0d3b66;">
                        Guardar Usuario
                    </button>
                </form>
            </div>
        </div>

        <!-- TABLA -->
        <div class="card shadow">
            <div class="card-body">
                <table class="table table-bordered">
                    <thead style="background-color:#0d3b66; color:white;">
                        <tr>
                            <th>Nombre</th>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th width="120">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaUsuarios"></tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
const API = "../backend/usuarios/";

document.addEventListener("DOMContentLoaded", listarUsuarios);

function listarUsuarios(){
    fetch(API + "listar.php")
    .then(res => res.json())
    .then(data => {

        let tabla = document.getElementById("tablaUsuarios");
        tabla.innerHTML = "";
        data.forEach(u => {
            tabla.innerHTML += `
    <tr>
        <td>${u.nombre}</td>
        <td>${u.usuario}</td>
        <td>${u.id_rol == 5 ? 'Administrador' : 'Trabajador'}</td>  
        <td class="d-flex  gap-1">
   <button onclick='editar(${JSON.stringify(u)})'
        class="btn btn-sm" style="background-color:#2e86c1; color:white;">
    Editar
</button>
<button onclick="eliminar(${u.id_usuario})"
        class="btn btn-sm" style="background-color:#616a6b; color:white;">
    Eliminar
</button>
    </td>
    </tr>
`;
        });
    });
}

document.getElementById("formUsuario").addEventListener("submit", function(e){
    e.preventDefault();

    let id = document.getElementById("id_usuario").value;
    let url = id ? "actualizar.php" : "insertar.php";

    let datos = {
        id_usuario: id,
        nombre:   document.getElementById("nombre").value,
        usuario:  document.getElementById("usuario").value,
        password: document.getElementById("password").value,
        id_rol:   document.getElementById("id_rol").value
    };

    fetch(API + url, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(datos)
    })
    .then(res => res.json())
    .then(data => {
        alert(data.mensaje || data.error);
        if(data.mensaje){
            this.reset();
            listarUsuarios();
            bootstrap.Collapse.getOrCreateInstance(
                document.getElementById("formCollapse")
            ).hide();
        }
    });
});
function editar(u){
    document.getElementById("id_usuario").value = u.id_usuario;
    document.getElementById("nombre").value     = u.nombre;
    document.getElementById("usuario").value    = u.usuario;
    document.getElementById("id_rol").value     = u.id_rol;

    bootstrap.Collapse.getOrCreateInstance(
        document.getElementById("formCollapse")
    ).show();
}

function eliminar(id){
    if(confirm("¿Eliminar usuario?")){
        fetch(API + "eliminar.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({id_usuario:id})
        })
       .then(res => res.json())
        .then(data => {
            alert(data.mensaje || data.error);
            listarUsuarios();
        });
    }
}


</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>