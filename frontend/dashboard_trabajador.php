<?php 
include "../backend/auth/verificar.php";

if($_SESSION['rol'] !== "Trabajador"){
    header("Location: dashboard_admin.php");
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

        .page-header { margin-bottom: 2rem; }
        .page-header h1 { font-size: 1.8rem; font-weight: 700; color: var(--navy); line-height: 1.1; }
        .page-header h1 span {
            display: block; font-size: .8rem; font-weight: 400; color: var(--muted);
            text-transform: uppercase; letter-spacing: .1em; margin-bottom: .2rem;
        }

        /* ── STAT CARDS ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
            max-width: 640px;
            margin-bottom: 2rem;
        }

        .stat-card {
            border-radius: 16px; padding: 1.5rem;
            color: #fff; position: relative; overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,.12);
            transition: transform .2s, box-shadow .2s;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,.18); }
        .stat-card::after {
            content: ''; position: absolute;
            width: 100px; height: 100px; border-radius: 50%;
            background: rgba(255,255,255,.08);
            bottom: -25px; right: -20px;
        }
        .stat-card.blue  { background: linear-gradient(135deg, #1d4e89, #2563a6); }
        .stat-card.green { background: linear-gradient(135deg, #065f46, #0d9e72); }

        .stat-card .stat-icon  { font-size: 1.4rem; margin-bottom: .5rem; display: block; }
        .stat-card .stat-label {
            font-size: .78rem; font-weight: 600; text-transform: uppercase;
            letter-spacing: .1em; opacity: .8; margin-bottom: .5rem;
        }
        .stat-card .stat-value {
            font-size: 2.2rem; font-weight: 700; line-height: 1;
            font-family: 'Space Mono', monospace;
        }

        /* ── ACCESOS RÁPIDOS ── */
        .section-title {
            font-size: .75rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: .1em; color: var(--muted); margin-bottom: 1rem;
        }

        .quick-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem; max-width: 640px;
        }

        .quick-card {
            background: var(--card); border-radius: 14px;
            padding: 1.4rem 1rem; text-align: center;
            box-shadow: 0 2px 15px rgba(13,59,102,.07);
            text-decoration: none; color: var(--text);
            border: 1.5px solid transparent;
            transition: all .2s;
            display: flex; flex-direction: column;
            align-items: center; gap: .5rem;
        }
        .quick-card:hover {
            border-color: var(--navy-light);
            box-shadow: 0 6px 25px rgba(13,59,102,.14);
            transform: translateY(-2px);
            color: var(--navy);
        }
        .quick-card .quick-icon { font-size: 1.8rem; }
        .quick-card .quick-label {
            font-size: .85rem; font-weight: 600; color: var(--navy);
        }
        .quick-card .quick-desc {
            font-size: .75rem; color: var(--muted);
        }

        /* ── TABLE CRÉDITOS ── */
        .table-card {
            background: var(--card); border-radius: 16px;
            box-shadow: 0 2px 20px rgba(13,59,102,.08); overflow: hidden;
            margin-top: .75rem;
        }
        .table-card table { width: 100%; border-collapse: collapse; }
        .table-card thead th {
            background: var(--navy); color: rgba(255,255,255,.85);
            font-size: .73rem; text-transform: uppercase;
            letter-spacing: .08em; padding: .9rem 1.1rem; font-weight: 600;
        }
        .table-card tbody tr { border-bottom: 1px solid var(--border); transition: background .15s; }
        .table-card tbody tr:last-child { border-bottom: none; }
        .table-card tbody tr:hover { background: #f5f9ff; }
        .table-card tbody td { padding: .85rem 1.1rem; font-size: .87rem; vertical-align: middle; }

        .num-factura {
            font-family: 'Space Mono', monospace; font-size: .78rem;
            font-weight: 700; color: var(--navy);
            background: rgba(13,59,102,.07); padding: .2rem .55rem;
            border-radius: 6px; display: inline-block;
        }
        .deuda-amount {
            font-family: 'Space Mono', monospace;
            font-size: .82rem; font-weight: 700; color: #dc2626;
        }

        .btn-pagado {
            background: rgba(13,122,90,.1); color: var(--success);
            border: none; border-radius: 7px; padding: .35rem .75rem;
            font-size: .78rem; font-weight: 700; cursor: pointer;
            transition: all .2s; white-space: nowrap;
        }
        .btn-pagado:hover { background: var(--success); color: #fff; }

        .loading-state {
            text-align: center; padding: 2.5rem; color: var(--muted);
            font-size: .88rem; display: flex; align-items: center;
            justify-content: center; gap: .75rem;
        }
        .spinner-dark {
            width: 20px; height: 20px; border: 2px solid var(--border);
            border-top-color: var(--navy-light); border-radius: 50%;
            animation: spin .7s linear infinite; flex-shrink: 0;
        }

        .empty-creditos {
            text-align: center; padding: 2.5rem; color: var(--muted);
        }
        .empty-creditos .icon { font-size: 2rem; margin-bottom: .5rem; }
        .empty-creditos p { font-size: .88rem; font-weight: 600; color: #0d3b66; }

        /* ── TOAST ── */
        .toast-wrap {
            position: fixed; bottom: 1.5rem; right: 1.5rem;
            z-index: 2000; display: flex; flex-direction: column; gap: .5rem;
        }
        .toast-item {
            background: var(--navy); color: #fff;
            padding: .8rem 1.2rem; border-radius: 12px;
            font-size: .88rem; font-weight: 500;
            box-shadow: 0 8px 25px rgba(0,0,0,.2);
            display: flex; align-items: center; gap: .5rem;
            animation: toastIn .3s ease;
        }
        .toast-item.success { background: #0d7a5a; }
        .toast-item.error   { background: #c0002d; }
        @keyframes toastIn {
            from { opacity: 0; transform: translateX(30px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        /* ── SPINNER ── */
        .spinner {
            width: 24px; height: 24px; border: 2px solid rgba(255,255,255,.3);
            border-top-color: #fff; border-radius: 50%;
            animation: spin .7s linear infinite; display: inline-block;
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
        <li class="nav-item"><a class="nav-link active" href="dashboard_trabajador.php">📊 Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="clientes.php">👥 Clientes</a></li>
        <li class="nav-item"><a class="nav-link" href="productos.php">📦 Productos</a></li>
        <li class="nav-item"><a class="nav-link" href="facturar.php">🧾 Facturas</a></li>
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

    <!-- STATS -->
    <div class="stats-grid">
        <div class="stat-card blue">
            <span class="stat-icon"></span>
            <div class="stat-label">Total Facturas</div>
            <div class="stat-value" id="totalFacturas">
                <div class="spinner"></div>
            </div>
        </div>
        <div class="stat-card green">
            <span class="stat-icon"></span>
            <div class="stat-label">Total Productos</div>
            <div class="stat-value" id="totalProductos">—</div>
        </div>
    </div>

  
    <!-- CRÉDITOS PENDIENTES -->
    <div class="section-title">Créditos pendientes de pago</div>
    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>N° Factura</th>
                    <th>Cliente</th>
                    <th>Teléfono</th>
                    <th>Total</th>
                    <th>Fecha</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody id="tablaCreditos">
                <tr><td colspan="6">
                    <div class="loading-state">
                        <div class="spinner-dark"></div>
                        Cargando créditos...
                    </div>
                </td></tr>
            </tbody>
        </table>
    </div>

</div>

<!-- TOAST -->
<div class="toast-wrap" id="toastWrap"></div>

<script>
// Estadísticas
fetch("../backend/dashboard/estadisticas.php")
.then(res => res.json())
.then(data => {
    document.getElementById("totalFacturas").innerText  = data.facturas;
    document.getElementById("totalProductos").innerText = data.productos;
})
.catch(err => console.error("Error estadisticas:", err));

// Créditos pendientes
function cargarCreditos() {
    fetch("../backend/dashboard/deudores.php")
    .then(res => res.json())
    .then(data => {
        const tbody = document.getElementById("tablaCreditos");

        if(data.length === 0){
            tbody.innerHTML = `
                <tr><td colspan="6">
                    <div class="empty-creditos">
                        <div class="icon"></div>
                        <p>Sin créditos pendientes</p>
                    </div>
                </td></tr>`;
            return;
        }

        tbody.innerHTML = data.map(d => `
            <tr>
                <td><span class="num-factura">${d.numero_factura ?? '—'}</span></td>
                <td style="font-weight:600;">${d.nombre}</td>
                <td style="color:var(--muted);font-family:'Space Mono',monospace;font-size:.8rem;">${d.telefono}</td>
                <td><span class="deuda-amount">L ${parseFloat(d.total).toLocaleString('es-HN', {minimumFractionDigits:2})}</span></td>
                <td style="color:var(--muted);font-size:.82rem;">${d.fecha}</td>
                <td>
                    <button class="btn-pagado" onclick="marcarPagado(${d.id_factura}, this)">
                         Marcar pagado
                    </button>
                </td>
            </tr>
        `).join('');
    })
    .catch(() => toast("Error al cargar créditos", "error"));
}

function marcarPagado(id_factura, btn) {
    fetch("../backend/facturas/marcar_pagado.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id_factura: id_factura })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            btn.closest("tr").style.opacity = "0.4";
            btn.closest("tr").style.transition = "opacity .4s";
            setTimeout(() => cargarCreditos(), 500);
            toast("Crédito marcado como pagado ✓", "success");
        } else {
            toast("Error: " + data.error, "error");
        }
    })
    .catch(() => toast("Error de conexión", "error"));
}

function toast(msg, tipo = "") {
    const wrap = document.getElementById("toastWrap");
    const el   = document.createElement("div");
    el.className = `toast-item ${tipo}`;
    el.textContent = msg;
    wrap.appendChild(el);
    setTimeout(() => el.remove(), 3500);
}

cargarCreditos();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>