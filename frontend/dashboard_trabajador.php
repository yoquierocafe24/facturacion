<?php 
include "../backend/auth/verificar.php";

if($_SESSION['rol'] !== "Trabajador"){
    header("Location: dashboard_admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Trabajador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="d-flex">

    <!-- SIDEBAR -->
    <div class="text-white p-3" style="width:250px; min-height:100vh; background-color: #0d3b66;">
        <h4>Panel Trabajador</h4>
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
                <a class="nav-link text-danger" href="../backend/auth/logout.php">Cerrar sesión</a>
            </li>
        </ul>
    </div>

    <!-- CONTENIDO -->
    <div class="container-fluid p-4">

        <h2>Dashboard</h2>

        <div class="row mt-4">

            <div class="col-md-4">
                <div class="card text-white shadow-lg border-0" style="background-color: #1d4e89;">
                    <div class="card-body">
                        <h5>Total Facturas</h5>
                        <h3 id="totalFacturas">0</h3>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<script>

// Solo cargar estadísticas básicas
fetch("../backend/dashboard/estadisticas.php")
.then(res => res.json())
.then(data => {
    totalFacturas.innerText = data.facturas;
});

</script>

</body>
</html>