<?php
include "../backend/auth/verificar.php";

if($_SESSION['rol'] !== "Administrador"){
    header("Location: dashboard_trabajador.php");
    exit();
}

include "../backend/config/conexion.php";

// Filtros
$cliente_busqueda = isset($_GET['cliente']) ? trim($_GET['cliente']) : '';
$fecha_inicio     = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fecha_fin        = isset($_GET['fecha_fin'])    ? $_GET['fecha_fin']    : '';

// Query con filtros
$where = "WHERE 1=1";
if($cliente_busqueda !== ''){
    $safe = $conn->real_escape_string($cliente_busqueda);
    $where .= " AND c.nombre LIKE '%$safe%'";
}
if($fecha_inicio !== ''){
    $where .= " AND f.fecha >= '$fecha_inicio'";
}
if($fecha_fin !== ''){
    $where .= " AND f.fecha <= '$fecha_fin'";
}

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
<html>
<head>
    <title>Historial de Facturas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .badge-pagada    { background-color: #d1fae5; color: #065f46; }
        .badge-pendiente { background-color: #fef9c3; color: #854d0e; }
        .badge-anulada   { background-color: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>

<div class="d-flex">

    <!-- SIDEBAR -->
    <div class="text-white p-3" style="width:250px; min-height:100vh; background-color:#0d3b66;">

        <div class="text-center mb-3">
            <div style="font-size:2rem;">🪟</div>
            <h5 class="mb-0 fw-bold">Vidrería George</h5>
            <small style="color:#a8c7e8;">Sistema de Facturación</small>
        </div>

        <hr style="border-color:#ffffff30;">

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
                <a class="nav-link text-white fw-bold" href="historial_facturas.php">📋 Historial</a>
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

        <h2>📋 Historial de Facturas</h2>

        <!-- FILTROS -->
        <div class="card shadow border-0 mt-4 p-3">
            <form method="GET" action="">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Nombre del Cliente</label>
                        <input type="text" name="cliente" class="form-control"
                               placeholder="Ej: Almedro Rivers"
                               value="<?= htmlspecialchars($cliente_busqueda) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Fecha Desde</label>
                        <input type="date" name="fecha_inicio" class="form-control"
                               value="<?= $fecha_inicio ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Fecha Hasta</label>
                        <input type="date" name="fecha_fin" class="form-control"
                               value="<?= $fecha_fin ?>">
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn w-100 fw-bold text-white"
                                style="background-color:#0d3b66;">
                            🔍 Buscar
                        </button>
                        <a href="historial_facturas.php" class="btn w-100 fw-bold text-white"
                           style="background-color:#6c757d;">
                            ✖ Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- TABLA -->
        <div class="card shadow border-0 mt-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Resultados</h5>
                    <span class="badge" style="background-color:#0d3b66;"><?= $facturas->num_rows ?> factura(s)</span>
                </div>

                <?php if($facturas->num_rows > 0): ?>
                <table class="table table-bordered table-hover">
                    <thead style="background-color:#0d3b66; color:white;">
                        <tr>
                            <th>N° Factura</th>
                            <th>Cliente</th>
                            <th>RTN Cliente</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while($f = $facturas->fetch_assoc()): ?>
                        <tr>
                            <td class="fw-bold"><?= htmlspecialchars($f['numero_factura']) ?></td>
                            <td><?= htmlspecialchars($f['cliente']) ?></td>
                            <td class="text-muted" style="font-size:12px;"><?= htmlspecialchars($f['rtn_cliente']) ?></td>
                            <td><?= date('d/m/Y', strtotime($f['fecha'])) ?></td>
                            <td class="fw-bold">L <?= number_format($f['total'], 2) ?></td>
                            <td>
                                <?php
                                    $estado = strtolower($f['estado_pago'] ?? 'pagada');
                                    $badge  = match($estado) {
                                        'pendiente' => 'badge-pendiente',
                                        'anulada'   => 'badge-anulada',
                                        default     => 'badge-pagada'
                                    };
                                ?>
                                <span class="badge <?= $badge ?> px-3 py-2">
                                    <?= ucfirst($estado) ?>
                                </span>
                            </td>
                            <td>
                                <a href="../backend/facturas/factura_pdf.php?id=<?= $f['id_factura'] ?>"
                                   target="_blank"
                                   class="btn btn-sm fw-bold text-white"
                                   style="background-color:#0d3b66;">
                                   🖨️ Imprimir
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <div class="text-center text-muted py-5">
                        <div style="font-size:3rem;">🔍</div>
                        <p class="mt-2 fw-bold">No se encontraron facturas</p>
                        <small>Intenta con otros filtros</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

</body>
</html>