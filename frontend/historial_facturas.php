<?php
include "../backend/auth/verificar.php";

if($_SESSION['rol'] !== "Administrador"){
    header("Location: dashboard_trabajador.php");
    exit();
}

include "../backend/config/conexion.php";

// Filtros
$cliente_busqueda = isset($_GET['cliente'])     ? trim($_GET['cliente'])     : '';
$fecha_inicio     = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio']     : '';
$fecha_fin        = isset($_GET['fecha_fin'])    ? $_GET['fecha_fin']        : '';

// Query con filtros
$where = "WHERE 1=1";
if($cliente_busqueda !== ''){
    $safe = $conn->real_escape_string($cliente_busqueda);
    $where .= " AND c.nombre LIKE '%$safe%'";
}
if($fecha_inicio !== '') $where .= " AND f.fecha >= '$fecha_inicio'";
if($fecha_fin    !== '') $where .= " AND f.fecha <= '$fecha_fin'";

$facturas = $conn->query("
    SELECT f.id_factura, f.numero_factura, f.fecha, f.total, f.estado_pago,
           c.nombre as cliente, c.rtn as rtn_cliente
    FROM facturas f
    INNER JOIN clientes c ON c.id_cliente = f.id_cliente
    $where
    ORDER BY f.fecha DESC, f.id_factura DESC
");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Facturas | Vidrería George</title>
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
            --warning:   #b45309;
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
            display: flex; align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;
        }
        .page-header h1 { font-size: 1.8rem; font-weight: 700; color: var(--navy); line-height: 1.1; }
        .page-header h1 span {
            display: block; font-size: .8rem; font-weight: 400; color: var(--muted);
            text-transform: uppercase; letter-spacing: .1em; margin-bottom: .2rem;
        }

        .count-badge {
            background: var(--navy); color: #fff;
            padding: .45rem 1rem; border-radius: 20px;
            font-size: .82rem; font-weight: 600;
        }

        /* ── FILTROS ── */
        .filter-card {
            background: var(--card); border-radius: 16px;
            box-shadow: 0 2px 20px rgba(13,59,102,.08);
            padding: 1.5rem; margin-bottom: 1.5rem;
        }
        .filter-card .filter-title {
            font-size: .75rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: .1em; color: var(--muted);
            margin-bottom: 1rem; padding-bottom: .75rem;
            border-bottom: 1.5px solid var(--border);
        }
        .filter-grid {
            display: grid;
            grid-template-columns: 2fr 1.2fr 1.2fr auto auto;
            gap: 1rem; align-items: flex-end;
        }

        .form-group { margin-bottom: 0; }
        .form-group label {
            display: block; font-size: .75rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: .07em;
            color: var(--muted); margin-bottom: .4rem;
        }
        .form-group input {
            width: 100%; border: 1.5px solid var(--border); border-radius: 10px;
            padding: .65rem .9rem; font-family: inherit; font-size: .9rem;
            color: var(--text); background: #fff; outline: none;
            transition: border .2s, box-shadow .2s;
        }
        .form-group input:focus {
            border-color: var(--navy-light);
            box-shadow: 0 0 0 3px rgba(37,99,166,.12);
        }

        .btn-buscar {
            background: linear-gradient(135deg, var(--navy), var(--navy-light));
            color: #fff; border: none; padding: .65rem 1.4rem;
            border-radius: 10px; font-weight: 600; font-size: .88rem;
            cursor: pointer; transition: all .2s; white-space: nowrap;
            box-shadow: 0 4px 15px rgba(13,59,102,.25); height: 44px;
        }
        .btn-buscar:hover { transform: translateY(-1px); box-shadow: 0 8px 25px rgba(13,59,102,.35); }

        .btn-limpiar {
            background: var(--bg); color: var(--muted);
            border: 1.5px solid var(--border); padding: .65rem 1.2rem;
            border-radius: 10px; font-weight: 600; font-size: .88rem;
            cursor: pointer; transition: all .2s; white-space: nowrap;
            text-decoration: none; display: inline-flex; align-items: center;
            gap: .4rem; height: 44px;
        }
        .btn-limpiar:hover { background: #dce6f0; color: var(--text); border-color: #b8cfe0; }

        /* ── TABLE ── */
        .table-card {
            background: var(--card); border-radius: 16px;
            box-shadow: 0 2px 20px rgba(13,59,102,.08); overflow: hidden;
        }
        .table-card table { width: 100%; border-collapse: collapse; }
        .table-card thead th {
            background: var(--navy); color: rgba(255,255,255,.85);
            font-size: .73rem; text-transform: uppercase;
            letter-spacing: .08em; padding: 1rem 1.25rem; font-weight: 600;
        }
        .table-card tbody tr { border-bottom: 1px solid var(--border); transition: background .15s; }
        .table-card tbody tr:last-child { border-bottom: none; }
        .table-card tbody tr:hover { background: #f5f9ff; }
        .table-card tbody td { padding: .9rem 1.25rem; font-size: .87rem; color: var(--text); vertical-align: middle; }

        .num-factura {
            font-family: 'Space Mono', monospace;
            font-size: .8rem; font-weight: 700; color: var(--navy);
            background: rgba(13,59,102,.07); padding: .25rem .6rem;
            border-radius: 6px; display: inline-block;
        }

        .precio-mono {
            font-family: 'Space Mono', monospace;
            font-size: .83rem; font-weight: 700; color: var(--navy);
        }

        .rtn-text { font-family: 'Space Mono', monospace; font-size: .75rem; color: var(--muted); }

        /* ── ESTADO BADGES ── */
        .estado-badge {
            display: inline-flex; align-items: center; gap: .35rem;
            padding: .3rem .8rem; border-radius: 20px;
            font-size: .75rem; font-weight: 700;
        }
        .badge-pagado    { background: rgba(13,122,90,.12);  color: #065f46; }
        .badge-pendiente { background: rgba(180,83,9,.12);   color: var(--warning); }
        .badge-anulada   { background: rgba(255,77,109,.12); color: #991b1b; }

        /* ── ACTION BTNS ── */
        .btn-pagado {
            background: rgba(13,122,90,.1); color: var(--success);
            border: none; border-radius: 7px; padding: .35rem .75rem;
            font-size: .78rem; font-weight: 700; cursor: pointer; transition: all .2s;
            white-space: nowrap;
        }
        .btn-pagado:hover { background: var(--success); color: #fff; }

        .btn-imprimir {
            background: rgba(13,59,102,.1); color: var(--navy);
            border: none; border-radius: 7px; padding: .35rem .75rem;
            font-size: .78rem; font-weight: 700; cursor: pointer; transition: all .2s;
            text-decoration: none; display: inline-flex; align-items: center; gap: .3rem;
            white-space: nowrap;
        }
        .btn-imprimir:hover { background: var(--navy); color: #fff; }

        /* ── EMPTY ── */
        .empty-state {
            text-align: center; padding: 4rem 2rem; color: var(--muted);
        }
        .empty-state .empty-icon { font-size: 3rem; margin-bottom: 1rem; }
        .empty-state p { font-weight: 600; margin-bottom: .25rem; }
        .empty-state small { font-size: .82rem; }

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
        <li class="nav-item"><a class="nav-link" href="dashboard_admin.php">📊 Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="clientes.php">👥 Clientes</a></li>
        <li class="nav-item"><a class="nav-link" href="productos.php">📦 Productos</a></li>
        <li class="nav-item"><a class="nav-link" href="facturar.php">🧾 Facturas</a></li>
        <?php if($_SESSION['rol'] == "Administrador"): ?>
        <li class="nav-item"><a class="nav-link active" href="historial_facturas.php">📋 Historial</a></li>
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
            <span>Registro</span>
            Historial de Facturas
        </h1>
        <span class="count-badge"> <?= $facturas->num_rows ?> factura(s)</span>
    </div>

    <!-- FILTROS -->
    <div class="filter-card">
        <div class="filter-title"> Filtrar facturas</div>
        <form method="GET" action="">
            <div class="filter-grid">
                <div class="form-group">
                    <label>Nombre del cliente</label>
                    <input type="text" name="cliente"
                           placeholder="Ej: Juan Pérez"
                           value="<?= htmlspecialchars($cliente_busqueda) ?>">
                </div>
                <div class="form-group">
                    <label>Fecha desde</label>
                    <input type="date" name="fecha_inicio" value="<?= $fecha_inicio ?>">
                </div>
                <div class="form-group">
                    <label>Fecha hasta</label>
                    <input type="date" name="fecha_fin" value="<?= $fecha_fin ?>">
                </div>
                <div>
                    <button type="submit" class="btn-buscar"> Buscar</button>
                </div>
                <div>
                    <a href="historial_facturas.php" class="btn-limpiar"> Limpiar</a>
                </div>
            </div>
        </form>
    </div>

    <!-- TABLA -->
    <div class="table-card">
        <?php if($facturas->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>N° Factura</th>
                    <th>Cliente</th>
                    <th>RTN</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php while($f = $facturas->fetch_assoc()):
                $estado = strtolower($f['estado_pago'] ?? 'pagado');
                $badgeClass = match($estado) {
                    'pendiente' => 'badge-pendiente',
                    'anulada'   => 'badge-anulada',
                    default     => 'badge-pagado'
                };
                $estadoIcon = match($estado) {
                    'pendiente' => '',
                    'anulada'   => '',
                    default     => ''
                };
            ?>
                <tr>
                    <td><span class="num-factura"><?= htmlspecialchars($f['numero_factura']) ?></span></td>
                    <td style="font-weight:600;"><?= htmlspecialchars($f['cliente']) ?></td>
                    <td><span class="rtn-text"><?= htmlspecialchars($f['rtn_cliente']) ?: '—' ?></span></td>
                    <td style="color:var(--muted);font-size:.83rem;"><?= date('d/m/Y', strtotime($f['fecha'])) ?></td>
                    <td><span class="precio-mono">L <?= number_format($f['total'], 2) ?></span></td>
                    <td>
                        <span class="estado-badge <?= $badgeClass ?>">
                            <?= $estadoIcon ?> <?= ucfirst($estado) ?>
                        </span>
                    </td>
                    <td style="display:flex;gap:.5rem;align-items:center;">
                        <?php if($estado === 'pendiente'): ?>
                        <button class="btn-pagado"
                                onclick="marcarPagado(<?= $f['id_factura'] ?>, this)">
                             Marcar pagado
                        </button>
                        <?php endif; ?>
                        <a class="btn-imprimir"
                           href="../backend/facturas/factura_pdf.php?id=<?= $f['id_factura'] ?>"
                           target="_blank">
                             Imprimir
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">🔍</div>
            <p>No se encontraron facturas</p>
            <small>Intenta con otros filtros o limpia la búsqueda</small>
        </div>
        <?php endif; ?>
    </div>

</div>

<!-- TOASTS -->
<div class="toast-wrap" id="toastWrap"></div>

<script>
function marcarPagado(id_factura, btn) {
    fetch("../backend/facturas/marcar_pagado.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id_factura: id_factura })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            // Actualizar badge en la fila
            const fila  = btn.closest("tr");
            const badge = fila.querySelector(".estado-badge");
            badge.className = "estado-badge badge-pagado";
            badge.innerHTML = "Pagado";
            // Quitar botón
            btn.remove();
            toast("Factura marcada como pagada ", "success");
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
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>