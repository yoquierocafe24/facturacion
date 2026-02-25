<?php 
include "../backend/auth/verificar.php";

if($_SESSION['rol'] !== "Administrador"){
    header("Location: dashboard_trabajador.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>
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
                <a class="nav-link text-white" href="#">Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="clientes.php">Clientes</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="#">Productos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="#">Facturas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="#">Usuarios</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="../backend/auth/logout.php">Cerrar sesión</a>
            </li>
        </ul>
    </div>

    <!-- CONTENIDO -->
    <div class="container-fluid p-4">

        <h2>Dashboard</h2>

        <!-- CARDS -->
        <div class="row mt-4">

            <div class="col-md-4">
                <div class="card text-white shadow-lg border-0" style="background-color: #1d4e89;">
                    <div class="card-body">
                        <h5>Total Facturas</h5>
                        <h3 id="totalFacturas">0</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
               <div class="card text-white shadow-lg border-0" style="background-color: #2563a6">
                    <div class="card-body">
                        <h5>Total Productos</h5>
                        <h3 id="totalProductos">0</h3>
                    </div>
                </div>
            </div>

        </div>

        <!-- TABLA DE DEUDORES -->
        <div class="mt-5">
            <h4>Personas con Facturas Pendientes</h4>

           <table class="table table-bordered mt-3">
             <thead style="background-color: #0d3b66; color: white;">
                    <tr>
                        <th>Cliente</th>
                        <th>Teléfono</th>
                        <th>Total Deuda</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody id="tablaDeudores"></tbody>
            </table>
        </div>

    </div>

</div>

<script>

// Cargar estadísticas
fetch("../backend/dashboard/estadisticas.php")
.then(res => res.json())
.then(data => {
    totalFacturas.innerText = data.facturas;
    totalProductos.innerText = data.productos;
});

// Cargar deudores
fetch("../backend/dashboard/deudores.php")
.then(res => res.json())
.then(data => {

    let tabla = document.getElementById("tablaDeudores");
    tabla.innerHTML = "";

    data.forEach(d => {
        tabla.innerHTML += `
            <tr>
                <td>${d.nombre}</td>
                <td>${d.telefono}</td>
                <td>$${d.total}</td>
                <td>${d.fecha}</td>
            </tr>
        `;
    });
});

</script>

</body>
</html>