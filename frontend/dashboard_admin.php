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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <a class="nav-link text-white" href="productos.php">📦 Productos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="facturar.php">🧾 Facturas</a>
        </li>
        <li class="nav-item">
              <a class="nav-link text-white" href="historial_facturas.php">📋 Historial</a>
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

        <h2>Dashboard</h2>

        <!-- RESUMEN SUPERIOR -->
        <div class="row mt-4 g-4">

            <!-- Total Facturas -->
            <div class="col-md-3">
                <div class="card shadow border-0 text-white" style="background: linear-gradient(45deg, #1d4e89, #2563a6);">
                    <div class="card-body">
                        <h6>Total Facturas</h6>
                        <h2 id="totalFacturas">0</h2>
                    </div>
                </div>
            </div>

            <!-- Total Productos -->
            <div class="col-md-3">
                <div class="card shadow border-0 text-white" style="background: linear-gradient(45deg, #198754, #20c997);">
                    <div class="card-body">
                        <h6>Total Productos</h6>
                        <h2 id="totalProductos">0</h2>
                    </div>
                </div>
            </div>

            <!-- Facturas Pendientes -->
            <div class="col-md-3">
                <div class="card shadow border-0 text-white" style="background: linear-gradient(45deg, #dc3545, #ff6b6b);">
                    <div class="card-body">
                        <h6>Facturas Pendientes</h6>
                        <h2 id="facturasPendientes">0</h2>
                    </div>
                </div>
            </div>

            <!-- Ventas del Mes -->
            <div class="col-md-3">
                <div class="card shadow border-0 text-white" style="background: linear-gradient(45deg, #6f42c1, #9d4edd);">
                    <div class="card-body">
                        <h6>Ventas del Mes</h6>
                        <h2 id="ventasMes">L 0</h2>
                    </div>
                </div>
            </div>

        </div>

        <!-- GRÁFICO -->
        <div class="card mt-5 shadow border-0">
            <div class="card-body">
                <h5>Resumen de Ventas (últimos 6 meses)</h5>
                <canvas id="graficoVentas" height="100"></canvas>
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

let grafico = null;

// Cargar estadísticas + gráfico
fetch("../backend/dashboard/estadisticas.php")
.then(res => res.json())
.then(data => {
    document.getElementById("totalFacturas").innerText      = data.facturas;
    document.getElementById("totalProductos").innerText     = data.productos;
    document.getElementById("facturasPendientes").innerText = data.pendientes;
    document.getElementById("ventasMes").innerText          = "L " + parseFloat(data.ventas_mes).toLocaleString('es-HN', {minimumFractionDigits:2});

    // Gráfico de barras
    const ctx = document.getElementById("graficoVentas").getContext("2d");
    if(grafico) grafico.destroy();
    grafico = new Chart(ctx, {
        type: "line",
        data: {
            labels: data.grafico_meses,
            datasets: [{
                label: "Ventas (L)",
                data: data.grafico_ventas,
                backgroundColor: "rgba(37, 99, 166, 0.1)",
                borderColor: "#2563a6",
                borderWidth: 3,
                pointBackgroundColor: "#1d4e89",
                pointBorderColor: "#fff",
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 8,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => "L " + ctx.parsed.y.toLocaleString('es-HN', {minimumFractionDigits:2})
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: val => "L " + val.toLocaleString('es-HN')
                    }
                }
            }
        }
    });
})
.catch(err => console.error("Error estadisticas:", err));

// Cargar deudores
fetch("../backend/dashboard/deudores.php")
.then(res => res.json())
.then(data => {
    let tabla = document.getElementById("tablaDeudores");
    tabla.innerHTML = "";

    if(data.length === 0){
        tabla.innerHTML = `<tr><td colspan="4" class="text-center text-muted">No hay facturas pendientes</td></tr>`;
        return;
    }

    data.forEach(d => {
        tabla.innerHTML += `
            <tr>
                <td>${d.nombre}</td>
                <td>${d.telefono}</td>
                <td>L ${parseFloat(d.total).toLocaleString('es-HN', {minimumFractionDigits:2})}</td>
                <td>${d.fecha}</td>
            </tr>
        `;
    });
})
.catch(err => console.error("Error deudores:", err));

</script>

</body>
</html>