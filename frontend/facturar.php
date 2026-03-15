<?php 
include "../backend/auth/verificar.php";

if($_SESSION['rol'] !== "Administrador" && $_SESSION['rol'] !== "Trabajador"){
    header("Location: dashboard_admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facturación | Vidrería George</title>
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
            background: var(--bg);
            color: var(--text);
            display: flex;
            min-height: 100vh;
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

        /* ── SECTION CARDS ── */
        .section-card {
            background: var(--card);
            border-radius: 16px;
            box-shadow: 0 2px 20px rgba(13,59,102,.08);
            padding: 1.75rem;
            margin-bottom: 1.5rem;
        }
        .section-card .section-title {
            font-size: .75rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .1em;
            color: var(--muted); margin-bottom: 1.25rem;
            padding-bottom: .75rem; border-bottom: 1.5px solid var(--border);
            display: flex; align-items: center; gap: .5rem;
        }

        /* ── FORM ELEMENTS ── */
        .form-group { margin-bottom: 0; }
        .form-group label {
            display: block; font-size: .75rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: .07em;
            color: var(--muted); margin-bottom: .4rem;
        }
        .form-group select,
        .form-group input {
            width: 100%; border: 1.5px solid var(--border); border-radius: 10px;
            padding: .65rem .9rem; font-family: inherit; font-size: .9rem;
            color: var(--text); background: #fff; outline: none;
            transition: border .2s, box-shadow .2s;
            appearance: auto;
        }
        .form-group select:focus,
        .form-group input:focus {
            border-color: var(--navy-light);
            box-shadow: 0 0 0 3px rgba(37,99,166,.12);
        }

        .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .form-grid-5 { display: grid; grid-template-columns: 2.5fr 1fr 1.2fr 1.2fr auto; gap: 1rem; align-items: flex-end; }

        /* ── BTN ADD ── */
        .btn-agregar {
            background: linear-gradient(135deg, var(--navy), var(--navy-light));
            color: #fff; border: none; padding: .65rem 1.4rem;
            border-radius: 10px; font-weight: 600; font-size: .9rem;
            cursor: pointer; transition: all .25s; white-space: nowrap;
            box-shadow: 0 4px 15px rgba(13,59,102,.3); height: 44px;
        }
        .btn-agregar:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(13,59,102,.4); }

        /* ── TABLE ── */
        .table-card {
            background: var(--card); border-radius: 16px;
            box-shadow: 0 2px 20px rgba(13,59,102,.08); overflow: hidden;
            margin-bottom: 1.5rem;
        }
        .table-card table { width: 100%; border-collapse: collapse; }
        .table-card thead th {
            background: var(--navy); color: rgba(255,255,255,.85);
            font-size: .73rem; text-transform: uppercase;
            letter-spacing: .08em; padding: .9rem 1.1rem; font-weight: 600;
            text-align: center;
        }
        .table-card thead th:first-child { text-align: left; }
        .table-card tbody tr { border-bottom: 1px solid var(--border); transition: background .15s; }
        .table-card tbody tr:last-child { border-bottom: none; }
        .table-card tbody tr:hover { background: #f5f9ff; }
        .table-card tbody td {
            padding: .85rem 1.1rem; font-size: .87rem;
            color: var(--text); vertical-align: middle; text-align: center;
        }
        .table-card tbody td:first-child { text-align: left; }

        .empty-row td {
            text-align: center; padding: 3rem;
            color: var(--muted); font-size: .88rem;
        }
        .empty-icon-sm { font-size: 2rem; display: block; margin-bottom: .5rem; }

        .precio-mono {
            font-family: 'Space Mono', monospace;
            font-size: .82rem; font-weight: 700; color: var(--navy);
        }

        .isv-badge {
            display: inline-block; padding: .2rem .55rem;
            border-radius: 6px; font-size: .73rem; font-weight: 700;
            background: rgba(37,99,166,.1); color: var(--navy-light);
        }
        .isv-exento {
            background: rgba(13,122,90,.1); color: var(--success);
        }

        .btn-remove {
            background: rgba(255,77,109,.1); color: var(--danger);
            border: none; border-radius: 7px;
            width: 30px; height: 30px;
            font-size: .85rem; cursor: pointer;
            transition: all .2s; font-weight: 700;
            display: inline-flex; align-items: center; justify-content: center;
        }
        .btn-remove:hover { background: var(--danger); color: #fff; }

        /* ── TOTALES ── */
        .totales-card {
            background: var(--card); border-radius: 16px;
            box-shadow: 0 2px 20px rgba(13,59,102,.08);
            padding: 1.5rem; max-width: 360px; margin-left: auto;
            margin-bottom: 1.5rem;
        }
        .totales-row {
            display: flex; justify-content: space-between;
            align-items: center; padding: .5rem 0;
            font-size: .88rem; color: var(--muted);
            border-bottom: 1px solid var(--border);
        }
        .totales-row:last-child { border-bottom: none; }
        .totales-row strong { color: var(--text); font-family: 'Space Mono', monospace; font-size: .85rem; }
        .totales-row.total-final {
            padding-top: .85rem; margin-top: .25rem;
            font-size: 1rem; font-weight: 700; color: var(--navy);
        }
        .totales-row.total-final strong {
            font-size: 1.1rem; color: var(--success);
        }

       /* ── BTN GUARDAR ── */
     .btn-guardar {
    background: #0d3b66; /* azul sólido sin degradado */
    color: #fff;
    border: none;
    padding: .85rem 2.5rem;
    border-radius: 12px;
    font-weight: 700;
    font-size: 1rem;
    cursor: pointer;
    transition: all .25s;
    box-shadow: 0 4px 15px rgba(13, 59, 102, 0.5); /* sombra azul suave */
    display: flex;
    align-items: center;
    gap: .6rem;
    }
 .btn-guardar:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(13, 59, 102, 0.7); /* sombra más intensa al hover */
    background-color: #09426d; /* azul más oscuro al pasar mouse */
    }
    

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
        <li class="nav-item">
            <?php if($_SESSION['rol'] == "Administrador"): ?>
                <a class="nav-link" href="dashboard_admin.php">📊 Dashboard</a>
            <?php else: ?>
                <a class="nav-link" href="dashboard_trabajador.php">📊 Dashboard</a>
            <?php endif; ?>
        </li>
        <li class="nav-item"><a class="nav-link" href="clientes.php">👥 Clientes</a></li>
        <li class="nav-item"><a class="nav-link" href="productos.php">📦 Productos</a></li>
        <li class="nav-item"><a class="nav-link active" href="facturar.php">🧾 Facturas</a></li>
        <?php if($_SESSION['rol'] == "Administrador"): ?>
        <li class="nav-item"><a class="nav-link" href="historial_facturas.php">📋 Historial</a></li>
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
            <span>Crear</span>
            Nueva Factura
        </h1>
    </div>

    <!-- SECCIÓN 1: CLIENTE -->
    <div class="section-card">
        <div class="section-title">👤 Datos del cliente</div>
        <div class="form-grid-2">
            <div class="form-group">
                <label>Cliente</label>
                <select id="cliente"></select>
            </div>
            <div class="form-group">
                <label>Estado de Pago</label>
                <select id="estado_pago">
                    <option value="Pagado"> Pagado</option>
                    <option value="Pendiente">Crédito</option>
                </select>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 2: AGREGAR PRODUCTO -->
    <div class="section-card">
        <div class="section-title"> Agregar producto a la factura</div>
        <div class="form-grid-5">
            <div class="form-group">
                <label>Producto</label>
                <select id="producto"></select>
            </div>
            <div class="form-group">
                <label>Cantidad</label>
                <input type="number" id="cantidad" placeholder="0" min="1">
            </div>
            <div class="form-group">
                <label>Precio (L)</label>
                <input type="number" id="precio" placeholder="0.00" step="0.01">
            </div>
            <div class="form-group">
                <label>Tipo ISV</label>
                <select id="tipo_isv">
                    <option value="15">ISV 15%</option>
                    <option value="18">ISV 18%</option>
                    <option value="Exento">Exento</option>
                </select>
            </div>
            <div>
                <button class="btn-agregar" onclick="agregarProducto()"> Agregar</button>
            </div>
        </div>
    </div>

    <!-- TABLA DE PRODUCTOS -->
    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Descripción</th>
                    <th>Cant.</th>
                    <th>Precio</th>
                    <th>ISV</th>
                    <th>Impuesto</th>
                    <th>Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="tablaProductos">
                <tr class="empty-row">
                    <td colspan="8">
                        <span class="empty-icon-sm"></span>
                        Aún no hay productos en esta factura.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- TOTALES + GUARDAR -->
    <div class="totales-card">
        <div class="totales-row">
            <span>Subtotal</span>
            <strong id="subtotal">L 0.00</strong>
        </div>
        <div class="totales-row">
            <span>ISV 15%</span>
            <strong id="isv15">L 0.00</strong>
        </div>
        <div class="totales-row">
            <span>ISV 18%</span>
            <strong id="isv18">L 0.00</strong>
        </div>
        <div class="totales-row total-final">
            <span>Total</span>
            <strong id="total">L 0.00</strong>
        </div>
    </div>

    <button class="btn-guardar" onclick="guardarFactura()">
         Guardar Factura
    </button>

</div>

<!-- TOASTS -->
<div class="toast-wrap" id="toastWrap"></div>

<script>
let productos = [];

// ── Cargar clientes ───────────────────────────────────────────────────────────
fetch("../backend/clientes/listar.php")
.then(res => res.json())
.then(data => {
    let select = document.getElementById("cliente");
    data.forEach(c => {
        select.innerHTML += `<option value="${c.id_cliente}">${c.nombre} - ${c.rtn}</option>`;
    });
});

// ── Cargar productos ──────────────────────────────────────────────────────────
fetch("../backend/productos/listar.php")
.then(res => res.json())
.then(data => {
    let select = document.getElementById("producto");
    data.forEach(p => {
        select.innerHTML += `<option value="${p.id_producto}"
            data-precio="${p.precio}"
            data-descripcion="${p.descripcion}">
            ${p.nombre}
        </option>`;
    });
});

document.getElementById("producto").addEventListener("change", function(){
    let precio = this.selectedOptions[0].dataset.precio;
    document.getElementById("precio").value = precio;
});

// ── Agregar producto ──────────────────────────────────────────────────────────
function agregarProducto(){
    let prodSelect = document.getElementById("producto");
    let cantidad   = document.getElementById("cantidad").value.trim();
    let precio     = document.getElementById("precio").value.trim();
    let tipo_isv   = document.getElementById("tipo_isv").value;

    if(!prodSelect.value || !cantidad || !precio){
        toast("Completa todos los campos del producto", "error"); return;
    }

    productos.push({
        id_producto:  prodSelect.value,
        nombre:       prodSelect.selectedOptions[0].text.trim(),
        descripcion:  prodSelect.selectedOptions[0].dataset.descripcion,
        cantidad:     parseFloat(cantidad),
        precio:       parseFloat(precio),
        tipo_isv:     tipo_isv
    });

    // Limpiar cantidad
    document.getElementById("cantidad").value = "";
    renderTabla();
}

// ── Render tabla ──────────────────────────────────────────────────────────────
function renderTabla(){
    let tbody = document.getElementById("tablaProductos");

    if(productos.length === 0){
        tbody.innerHTML = `
            <tr class="empty-row">
                <td colspan="8">
                    <span class="empty-icon-sm"></span>
                    Aún no hay productos en esta factura.
                </td>
            </tr>`;
        actualizarTotales(0, 0, 0);
        return;
    }

    let subtotal = 0, total15 = 0, total18 = 0;
    tbody.innerHTML = "";

    productos.forEach((p, index) => {
        let sub      = p.cantidad * p.precio;
        let impuesto = 0;

        if(p.tipo_isv == "15")     { impuesto = sub * 0.15; total15 += impuesto; }
        else if(p.tipo_isv == "18"){ impuesto = sub * 0.18; total18 += impuesto; }

        subtotal += sub;

        tbody.innerHTML += `
        <tr>
            <td><span style="font-weight:600;">${p.nombre}</span></td>
            <td style="color:var(--muted);font-size:.82rem;">${p.descripcion ?? '—'}</td>
            <td>${p.cantidad}</td>
            <td class="precio-mono">L ${p.precio.toFixed(2)}</td>
            <td>
                <span class="isv-badge ${p.tipo_isv === 'Exento' ? 'isv-exento' : ''}">
                    ${p.tipo_isv === 'Exento' ? 'Exento' : p.tipo_isv + '%'}
                </span>
            </td>
            <td class="precio-mono">L ${impuesto.toFixed(2)}</td>
            <td class="precio-mono" style="color:var(--navy-light);">L ${(sub + impuesto).toFixed(2)}</td>
            <td>
                <button class="btn-remove" onclick="eliminar(${index})">✕</button>
            </td>
        </tr>`;
    });

    actualizarTotales(subtotal, total15, total18);
}

function actualizarTotales(subtotal, total15, total18){
    document.getElementById("subtotal").innerText = "L " + subtotal.toFixed(2);
    document.getElementById("isv15").innerText    = "L " + total15.toFixed(2);
    document.getElementById("isv18").innerText    = "L " + total18.toFixed(2);
    document.getElementById("total").innerText    = "L " + (subtotal + total15 + total18).toFixed(2);
}

function eliminar(index){
    productos.splice(index, 1);
    renderTabla();
}

// ── Guardar factura ───────────────────────────────────────────────────────────
function guardarFactura(){
    if(productos.length === 0){
        toast("Agrega al menos un producto", "error"); return;
    }

    fetch("../backend/facturas/crear.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            id_cliente:  document.getElementById("cliente").value,
            estado_pago: document.getElementById("estado_pago").value,
            productos:   productos
        })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            toast("Factura creada correctamente ✓", "success");
            window.open("../backend/facturas/factura_pdf.php?id=" + data.id_factura, "_blank");
            setTimeout(() => { location.reload(); }, 1200);
        } else {
            toast("Error: " + data.error, "error");
        }
    })
    .catch(() => toast("Error de conexión", "error"));
}

// ── Toast ─────────────────────────────────────────────────────────────────────
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