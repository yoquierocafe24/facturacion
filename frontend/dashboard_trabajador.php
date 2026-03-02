<?php 
include "../backend/auth/verificar.php";

// Redirigir si no es Trabajador
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
            <a class="nav-link text-white" href="#">📊 Dashboard</a>
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
        <li class="nav-item mt-3">
            <a class="nav-link text-danger" href="../backend/auth/logout.php">🚪 Cerrar sesión</a>
        </li>
    </ul>
</div>
    <!-- CONTENIDO -->
    <div class="container-fluid p-4">

        <h2>Dashboard</h2>

        <!-- CARDS -->
        <div class="row mt-4 g-4">

            <!-- Total Facturas -->
            <div class="col-md-4">
                <div class="card shadow border-0 text-white" style="background: linear-gradient(45deg, #1d4e89, #2563a6);">
                    <div class="card-body">
                        <h6>Total Facturas</h6>
                        <h2 id="totalFacturas">0</h2>
                    </div>
                </div>
            </div>

            <!-- Total Productos (opcional) -->
            <div class="col-md-4">
                <div class="card shadow border-0 text-white" style="background: linear-gradient(45deg, #198754, #20c997);">
                    <div class="card-body">
                        <h6>Total Productos</h6>
                        <h2 id="totalProductos">0</h2>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<script>

// Solo cargar estadísticas básicas para trabajador
fetch("../backend/dashboard/estadisticas.php")
.then(res => res.json())
.then(data => {
    totalFacturas.innerText = data.facturas;
    totalProductos.innerText = data.productos; 
});

</script>

</body>
</html>