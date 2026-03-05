<?php
include "../backend/auth/verificar.php";

if($_SESSION['rol'] != "Administrador" && $_SESSION['rol'] != "Trabajador"){
    header("Location: login.html");
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

        <h3 class="mb-4" style="color:#0d3b66;">Gestión de Clientes</h3>

        <button class="btn text-white mb-3"
                style="background-color:#0d3b66;"
                data-bs-toggle="collapse"
                data-bs-target="#formCollapse">
            + Nuevo Cliente
        </button>

        <!-- FORMULARIO -->
        <div class="collapse" id="formCollapse">
            <div class="card shadow p-3 mb-3">
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

                        <!-- Botón RTN -->
                        <div class="col-md-12 mb-2">
                            <button type="button" id="toggleRTN" class="btn btn-sm text-white" style="background-color:#2e86c1;">
                                ¿Con RTN?
                            </button>
                        </div>

                        <!-- Input RTN oculto -->
                        <div class="col-md-4" id="rtnDiv" style="display:none;">
                            <input type="text" id="rtn" placeholder="R.T.N" class="form-control mb-2">
                        </div>
                    </div>

                    <button class="btn text-white" style="background-color:#0d3b66;">
                        Guardar Cliente
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
                            <th>Cédula</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>R.T.N</th>
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

// Mostrar/ocultar campo R.T.N
document.getElementById("toggleRTN").addEventListener("click", () => {
    const div = document.getElementById("rtnDiv");
    div.style.display = div.style.display === "none" ? "block" : "none";
});

document.addEventListener("DOMContentLoaded", listarClientes);

function listarClientes() {
    fetch(API + "listar.php")
    .then(res => res.json())
    .then(data => {
        let tabla = document.getElementById("tablaClientes");
        tabla.innerHTML = "";

        data.forEach(cliente => {
        let botones = `<button onclick='editar(${JSON.stringify(cliente)})' class="btn btn-sm" style="background-color:#2e86c1; color:white;">Editar</button>`;

         if("<?php echo $_SESSION['rol']; ?>" === "Administrador") {
        botones += ` <button onclick='eliminar(${cliente.id_cliente})' class="btn btn-sm" style="background-color:#616a6b; color:white;">Eliminar</button>`;
}

            tabla.innerHTML += `
                <tr>
                    <td>${cliente.nombre}</td>
                    <td>${cliente.cedula}</td>
                    <td>${cliente.telefono}</td>
                    <td>${cliente.email}</td>
                    <td>${cliente.rtn && cliente.rtn.trim() !== "" ? cliente.rtn : "Sin R.T.N"}</td>
                    <td>${botones}</td>
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
        rtn: document.getElementById("rtn").value
    };

    let url = id ? "actualizar.php" : "insertar.php";

    fetch(API + url, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(cliente)
    })
    .then(res => res.json())
    .then(data => {
        if(data.mensaje){
            alert(data.mensaje);
            this.reset();
            document.getElementById("rtnDiv").style.display = "none";
            listarClientes();
            bootstrap.Collapse.getOrCreateInstance(
                document.getElementById("formCollapse")
            ).hide();
        } else if(data.error){
            alert(data.error);
        }
    })
    .catch(err => console.error(err));
});

function editar(cliente) {
    document.getElementById("id_cliente").value  = cliente.id_cliente;
    document.getElementById("nombre").value      = cliente.nombre;
    document.getElementById("cedula").value      = cliente.cedula;
    document.getElementById("telefono").value    = cliente.telefono;
    document.getElementById("email").value       = cliente.email;
    document.getElementById("direccion").value   = cliente.direccion;
    document.getElementById("rtn").value         = cliente.rtn || "";

    if(cliente.rtn && cliente.rtn.trim() !== ""){
        document.getElementById("rtnDiv").style.display = "block";
    } else {
        document.getElementById("rtnDiv").style.display = "none";
    }

    bootstrap.Collapse.getOrCreateInstance(
        document.getElementById("formCollapse")
    ).show();
}

function eliminar(id) {
    if(confirm("¿Eliminar cliente?")){
        fetch(API + "eliminar.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({id_cliente: id})
        })
        .then(res => res.json())
        .then(data => {
            alert(data.mensaje || data.error);
            listarClientes();
        });
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>