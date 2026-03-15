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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Vidrería George</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --navy:      #0d3b66;
            --navy-mid:  #1a5276;
            --navy-light:#2563a6;
            --accent:    #00c6ff;
            --danger:    #ff4d6d;
            --success:   #0d7a5a;
            --bg:        #f0f4f9;
            --card:      #ffffff;
            --text:      #1a2740;
            --muted:     #7a90a8;
            --border:    #d6e4f0;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg); color: var(--text);
            display: flex; min-height: 100vh;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            width: 250px; min-height: 100vh; background: var(--navy);
            display: flex; flex-direction: column;
            padding: 1.5rem 1rem; position: sticky;
            top: 0; height: 100vh; overflow-y: auto;
        }
        .sidebar .brand { text-align: center; margin-bottom: 1rem; }
        .sidebar .brand .icon { font-size: 2rem; }
        .sidebar .brand h5 { color: #fff; font-weight: 700; margin: .25rem 0 0; font-size: 1rem; }
        .sidebar .brand small { color: #a8c7e8; font-size: .75rem; }
        .sidebar hr { border-color: rgba(255,255,255,.15); margin: .75rem 0; }
        .sidebar .user-badge {
            display: flex; align-items: center; gap: .75rem;
            padding: .5rem .75rem; border-radius: 10px;
            background: rgba(255,255,255,.07); margin-bottom: .5rem;
        }
        .sidebar .user-badge .avatar {
            width: 38px; height: 38px; border-radius: 50%;
            background: var(--navy-mid);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: .9rem; color: #fff; flex-shrink: 0;
        }
        .sidebar .user-badge .info .name { color: #fff; font-size: .85rem; font-weight: 600; }
        .sidebar .user-badge .info .role { color: #a8c7e8; font-size: .72rem; }
        .sidebar .nav-link {
            color: rgba(255,255,255,.75); border-radius: 8px;
            padding: .55rem .85rem; font-size: .88rem; transition: all .2s;
            display: flex; align-items: center; gap: .5rem;
        }
        .sidebar .nav-link:hover { background: rgba(255,255,255,.12); color: #fff; }
        .sidebar .nav-link.active {
            background: linear-gradient(90deg, rgba(0,198,255,.25), rgba(0,198,255,.05));
            border-left: 3px solid var(--accent); color: var(--accent);
        }
        .sidebar .nav-link.logout { color: #ff6b6b; }
        .sidebar .nav-link.logout:hover { background: rgba(255,77,109,.15); color: #ff4d6d; }

        /* ── MAIN ── */
        .main { flex: 1; padding: 2rem 2.5rem; overflow-x: hidden; }

        .page-header {
            margin-bottom: 2rem;
        }
        .page-header h1 { font-size: 1.8rem; font-weight: 700; color: var(--navy); line-height: 1.1; }
        .page-header h1 span {
            display: block; font-size: .8rem; font-weight: 400; color: var(--muted);
            text-transform: uppercase; letter-spacing: .1em; margin-bottom: .2rem;
        }

        /* ── STAT CARDS ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.25rem;
            margin-bottom: 1.75rem;
        }

        .stat-card {
            border-radius: 16px;
            padding: 1.5rem;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,.12);
            transition: transform .2s, box-shadow .2s;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,.18); }

        .stat-card::after {
            content: '';
            position: absolute;
            width: 100px; height: 100px;
            border-radius: 50%;
            background: rgba(255,255,255,.08);
            bottom: -25px; right: -20px;
        }

        .stat-card.blue   { background: linear-gradient(135deg, #1d4e89, #2563a6); }
        .stat-card.green  { background: linear-gradient(135deg, #065f46, #0d9e72); }
        .stat-card.red    { background: linear-gradient(135deg, #991b1b, #dc2626); }
        .stat-card.purple { background: linear-gradient(135deg, #4c1d95, #7c3aed); }

        .stat-card .stat-label {
            font-size: .78rem; font-weight: 600; text-transform: uppercase;
            letter-spacing: .1em; opacity: .8; margin-bottom: .5rem;
        }
        .stat-card .stat-icon {
            font-size: 1.4rem; margin-bottom: .5rem; display: block;
        }
        .stat-card .stat-value {
            font-size: 2rem; font-weight: 700; line-height: 1;
            font-family: 'Space Mono', monospace;
        }
        .stat-card .stat-value.small-value { font-size: 1.4rem; }

        /* ── CHART CARD ── */
        .chart-card {
            background: var(--card); border-radius: 16px;
            box-shadow: 0 2px 20px rgba(13,59,102,.08);
            padding: 1.75rem; margin-bottom: 1.75rem;
        }
        .chart-card .chart-header {
            display: flex; align-items: center;
            justify-content: space-between; margin-bottom: 1.5rem;
        }
        .chart-card .chart-title {
            font-size: 1rem; font-weight: 700; color: var(--navy);
        }
        .chart-card .chart-subtitle {
            font-size: .78rem; color: var(--muted); margin-top: .15rem;
        }
        .chart-legend {
            display: flex; align-items: center; gap: .5rem;
            font-size: .78rem; color: var(--muted); font-weight: 600;
        }
        .chart-legend .dot {
            width: 10px; height: 10px; border-radius: 50%;
            background: var(--navy-light);
        }

        /* ── DEUDORES TABLE ── */
        .table-card {
            background: var(--card); border-radius: 16px;
            box-shadow: 0 2px 20px rgba(13,59,102,.08); overflow: hidden;
        }
        .table-card .table-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1.25rem 1.5rem; border-bottom: 1.5px solid var(--border);
        }
        .table-card .table-header h4 {
            font-size: 1rem; font-weight: 700; color: var(--navy);
        }
        .table-card .table-header small { font-size: .78rem; color: var(--muted); }

        .table-card table { width: 100%; border-collapse: collapse; }
        .table-card thead th {
            background: var(--navy); color: rgba(255,255,255,.85);
            font-size: .73rem; text-transform: uppercase;
            letter-spacing: .08em; padding: .85rem 1.25rem; font-weight: 600;
        }
        .table-card tbody tr { border-bottom: 1px solid var(--border); transition: background .15s; }
        .table-card tbody tr:last-child { border-bottom: none; }
        .table-card tbody tr:hover { background: #f5f9ff; }
        .table-card tbody td { padding: .85rem 1.25rem; font-size: .87rem; vertical-align: middle; }

        .deuda-amount {
            font-family: 'Space Mono', monospace;
            font-size: .82rem; font-weight: 700; color: #dc2626;
        }

        .empty-state {
            text-align: center; padding: 3rem 2rem; color: var(--muted);
        }
        .empty-state .empty-icon { font-size: 2.5rem; margin-bottom: .75rem; }

        /* ── LOADER SPINNER ── */
        .spinner {
            width: 32px; height: 32px; border: 3px solid var(--border);
            border-top-color: var(--navy-light); border-radius: 50%;
            animation: spin .7s linear infinite; margin: 0 auto 1rem;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="brand">
        <div class="icon">🪟</div>
        <h5>Vidrería George</h5>
        <small>Sistema de Facturación</small>
    </div>
    <hr>
    <div class="user-badge">
        <div class="avatar"><?php echo strtoupper(substr($_SESSION['nombre'], 0, 2)); ?></div>
        <div class="info">
            <div class="name"><?php echo $_SESSION['nombre']; ?></div>
            <div class="role"><?php echo $_SESSION['rol']; ?></div>
        </div>
    </div>
    <hr>
    <ul class="nav flex-column gap-1">
        <li class="nav-item"><a class="nav-link active" href="dashboard_admin.php">📊 Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="clientes.php">👥 Clientes</a></li>
        <li class="nav-item"><a class="nav-link" href="productos.php">📦 Productos</a></li>
        <li class="nav-item"><a class="nav-link" href="facturar.php">🧾 Facturas</a></li>
        <li class="nav-item"><a class="nav-link" href="historial_facturas.php">📋 Historial</a></li>
        <?php if($_SESSION['rol'] == "Administrador"): ?>
        <li class="nav-item"><a class="nav-link" href="usuarios.php">👤 Usuarios</a></li>
        <?php endif; ?>
        <li class="nav-item mt-auto">
            <a class="nav-link logout" href="../backend/auth/logout.php">🚪 Cerrar sesión</a>
        </li>
    </ul>
</div>

<!-- MAIN -->
<div class="main">

    <div class="page-header">
        <h1>
            <span>Bienvenido, <?php echo $_SESSION['nombre']; ?></span>
            Dashboard
        </h1>
    </div>

    <!-- STAT CARDS -->
    <div class="stats-grid">
        <div class="stat-card blue">
            <span class="stat-icon"></span>
            <div class="stat-label">Total Facturas</div>
            <div class="stat-value" id="totalFacturas">
                <div class="spinner" style="width:24px;height:24px;border-width:2px;margin:0;"></div>
            </div>
        </div>
        <div class="stat-card green">
            <span class="stat-icon"></span>
            <div class="stat-label">Total Productos</div>
            <div class="stat-value" id="totalProductos">—</div>
        </div>
        <div class="stat-card red">
            <span class="stat-icon"></span>
            <div class="stat-label">Facturas Pendientes</div>
            <div class="stat-value" id="facturasPendientes">—</div>
        </div>
        <div class="stat-card purple">
            <span class="stat-icon"></span>
            <div class="stat-label">Ventas del Mes</div>
            <div class="stat-value small-value" id="ventasMes">—</div>
        </div>
    </div>

    <!-- GRÁFICO -->
    <div class="chart-card">
        <div class="chart-header">
            <div>
                <div class="chart-title">Resumen de Ventas</div>
                <div class="chart-subtitle">Últimos 6 meses</div>
            </div>
            <div class="chart-legend">
                <span class="dot"></span> Ventas (L)
            </div>
        </div>
        <canvas id="graficoVentas" height="90"></canvas>
    </div>

    <!-- TABLA DEUDORES -->
    <div class="table-card">
        <div class="table-header">
            <div>
                <h4> Facturas Pendientes de Pago</h4>
                <small>Clientes con crédito activo</small>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Teléfono</th>
                    <th>Total Deuda</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody id="tablaDeudores">
                <tr>
                    <td colspan="4">
                        <div class="empty-state">
                            <div class="spinner"></div>
                            Cargando...
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</div>

<script>
let grafico = null;

// ── Estadísticas + gráfico ────────────────────────────────────────────────────
fetch("../backend/dashboard/estadisticas.php")
.then(res => res.json())
.then(data => {
    document.getElementById("totalFacturas").innerText      = data.facturas;
    document.getElementById("totalProductos").innerText     = data.productos;
    document.getElementById("facturasPendientes").innerText = data.pendientes;
    document.getElementById("ventasMes").innerText          =
        "L " + parseFloat(data.ventas_mes).toLocaleString('es-HN', {minimumFractionDigits: 2});

    const ctx = document.getElementById("graficoVentas").getContext("2d");
    if(grafico) grafico.destroy();
    grafico = new Chart(ctx, {
        type: "line",
        data: {
            labels: data.grafico_meses,
            datasets: [{
                label: "Ventas (L)",
                data: data.grafico_ventas,
                backgroundColor: "rgba(37, 99, 166, 0.08)",
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
                    backgroundColor: "#0d3b66",
                    padding: 10,
                    cornerRadius: 10,
                    callbacks: {
                        label: ctx => "  L " + ctx.parsed.y.toLocaleString('es-HN', {minimumFractionDigits: 2})
                    }
                }
            },
            scales: {
                x: { grid: { display: false }, border: { display: false } },
                y: {
                    beginAtZero: true,
                    grid: { color: "rgba(214,228,240,.6)", drawBorder: false },
                    border: { display: false },
                    ticks: { callback: val => "L " + val.toLocaleString('es-HN'), color: "#7a90a8" }
                }
            }
        }
    });
})
.catch(err => console.error("Error estadisticas:", err));

// ── Deudores ──────────────────────────────────────────────────────────────────
fetch("../backend/dashboard/deudores.php")
.then(res => res.json())
.then(data => {
    const tabla = document.getElementById("tablaDeudores");

    if(data.length === 0){
        tabla.innerHTML = `
            <tr><td colspan="4">
                <div class="empty-state">
                    <div class="empty-icon"></div>
                    <p style="font-weight:600;color:var(--success);">Sin facturas pendientes</p>
                </div>
            </td></tr>`;
        return;
    }

    tabla.innerHTML = data.map(d => `
        <tr>
            <td style="font-weight:600;">${d.nombre}</td>
            <td style="color:var(--muted);font-family:'Space Mono',monospace;font-size:.82rem;">${d.telefono}</td>
            <td><span class="deuda-amount">L ${parseFloat(d.total).toLocaleString('es-HN', {minimumFractionDigits:2})}</span></td>
            <td style="color:var(--muted);font-size:.83rem;">${d.fecha}</td>
        </tr>
    `).join('');
})
.catch(err => console.error("Error deudores:", err));
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>