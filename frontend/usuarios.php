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
                <a class="nav-link text-warning" href="clientes.php">Clientes</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="#">Productos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="#">Facturas</a>
            </li>
           <?php if($_SESSION['rol'] == "Administrador"){ ?>
           <li> <a href="usuarios.php" class="nav-link text-white ">Usuarios</a> </li>
<?php } ?>
            <li class="nav-item">
                <a class="nav-link text-danger" href="../backend/auth/logout.php">Cerrar sesión</a>
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
                                <option value="5">Administrador</option> 
                                <option value="6">Trabajador</option>
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
                    <td>
                        <button onclick="eliminar(${u.id_usuario})"
                                class="btn btn-danger btn-sm">
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
    alert("SE ESTA EJECUTANDO EL JS");

    let datos = {
        nombre: document.getElementById("nombre").value,
        usuario: document.getElementById("usuario").value,
        password: document.getElementById("password").value,
        id_rol: document.getElementById("id_rol").value
    };

    fetch(API + "insertar.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(datos)
    })
    .then(res => res.json())
    .then(() => {
        this.reset();
        listarUsuarios();
        bootstrap.Collapse.getOrCreateInstance(
            document.getElementById("formCollapse")
        ).hide();
    });
});

function eliminar(id){
    if(confirm("¿Eliminar usuario?")){
        fetch(API + "eliminar.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({id_usuario:id})
        })
        .then(() => listarUsuarios());
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>