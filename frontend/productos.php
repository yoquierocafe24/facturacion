<?php 
include "../backend/auth/verificar.php";

if($_SESSION['rol'] !== "Administrador" && $_SESSION['rol'] !== "Trabajador"){
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos | Vidrería George</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --navy:      #0d3b66;
            --navy-mid:  #1a5276;
            --navy-light:#2563a6;
            --accent:    #00c6ff;
            --accent2:   #38e8b5;
            --danger:    #ff4d6d;
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
            width: 250px;
            min-height: 100vh;
            background: var(--navy);
            display: flex;
            flex-direction: column;
            padding: 1.5rem 1rem;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar .brand {
            text-align: center;
            margin-bottom: 1rem;
        }
        .sidebar .brand .icon { font-size: 2rem; }
        .sidebar .brand h5 {
            color: #fff;
            font-weight: 700;
            margin: 0.25rem 0 0;
            font-size: 1rem;
        }
        .sidebar .brand small { color: #a8c7e8; font-size: 0.75rem; }

        .sidebar hr { border-color: rgba(255,255,255,.15); margin: 0.75rem 0; }

        .sidebar .user-badge {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .5rem .75rem;
            border-radius: 10px;
            background: rgba(255,255,255,.07);
            margin-bottom: .5rem;
        }
        .sidebar .user-badge .avatar {
            width: 38px; height: 38px;
            border-radius: 50%;
            background: var(--navy-mid);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: .9rem; color: #fff;
            flex-shrink: 0;
        }
        .sidebar .user-badge .info .name { color: #fff; font-size: .85rem; font-weight: 600; }
        .sidebar .user-badge .info .role { color: #a8c7e8; font-size: .72rem; }

        .sidebar .nav-link {
            color: rgba(255,255,255,.75);
            border-radius: 8px;
            padding: .55rem .85rem;
            font-size: .88rem;
            transition: all .2s;
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,.12);
            color: #fff;
        }
        .sidebar .nav-link.active {
            background: linear-gradient(90deg, rgba(0,198,255,.25), rgba(0,198,255,.05));
            border-left: 3px solid var(--accent);
            color: var(--accent);
        }
        .sidebar .nav-link.logout { color: #ff6b6b; margin-top: auto; }
        .sidebar .nav-link.logout:hover { background: rgba(255,77,109,.15); color: #ff4d6d; }

        /* ── MAIN ── */
        .main {
            flex: 1;
            padding: 2rem 2.5rem;
            overflow-x: hidden;
        }

        .page-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .page-header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--navy);
            line-height: 1.1;
        }
        .page-header h1 span {
            display: block;
            font-size: .8rem;
            font-weight: 400;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .1em;
            margin-bottom: .2rem;
        }

        /* ── BTN PRIMARY ── */
        .btn-add {
            background: linear-gradient(135deg, var(--navy), var(--navy-light));
            color: #fff;
            border: none;
            padding: .65rem 1.4rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: .9rem;
            cursor: pointer;
            transition: all .25s;
            display: flex;
            align-items: center;
            gap: .5rem;
            box-shadow: 0 4px 15px rgba(13,59,102,.3);
        }
        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(13,59,102,.4);
            color: #fff;
        }

        /* ── SEARCH BAR ── */
        .search-wrap {
            position: relative;
            max-width: 320px;
        }
        .search-wrap input {
            width: 100%;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            padding: .6rem 1rem .6rem 2.6rem;
            font-family: inherit;
            font-size: .88rem;
            background: #fff;
            color: var(--text);
            transition: border .2s;
            outline: none;
        }
        .search-wrap input:focus { border-color: var(--navy-light); }
        .search-wrap .search-icon {
            position: absolute;
            left: .85rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 1rem;
            pointer-events: none;
        }

        /* ── TOOLBAR ── */
        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        /* ── TABLE CARD ── */
        .table-card {
            background: var(--card);
            border-radius: 16px;
            box-shadow: 0 2px 20px rgba(13,59,102,.08);
            overflow: hidden;
        }

        .table-card table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-card thead th {
            background: var(--navy);
            color: rgba(255,255,255,.85);
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            padding: 1rem 1.25rem;
            font-weight: 600;
        }

        .table-card tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background .15s;
        }
        .table-card tbody tr:last-child { border-bottom: none; }
        .table-card tbody tr:hover { background: #f5f9ff; }

        .table-card tbody td {
            padding: .9rem 1.25rem;
            font-size: .88rem;
            color: var(--text);
            vertical-align: middle;
        }

        .badge-estado {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .3rem .75rem;
            border-radius: 20px;
            font-size: .75rem;
            font-weight: 600;
        }
        .badge-activo  { background: rgba(56,232,181,.15); color: #0d7a5a; }
        .badge-inactivo{ background: rgba(255,77,109,.12); color: #c0002d; }

        .precio-cell {
            font-family: 'Space Mono', monospace;
            font-size: .82rem;
            color: var(--navy);
            font-weight: 700;
        }

        .stock-pill {
            display: inline-block;
            padding: .2rem .6rem;
            border-radius: 6px;
            font-size: .8rem;
            font-weight: 600;
            background: var(--bg);
            color: var(--navy-mid);
        }

        /* ── ACTION BTNS ── */
        .action-btn {
            border: none;
            border-radius: 7px;
            padding: .35rem .65rem;
            cursor: pointer;
            font-size: .8rem;
            transition: all .2s;
            font-weight: 600;
        }
        .btn-edit  { background: rgba(37,99,166,.1); color: var(--navy-light); }
        .btn-edit:hover  { background: var(--navy-light); color: #fff; }
        .btn-del   { background: rgba(255,77,109,.1);  color: var(--danger); }
        .btn-del:hover   { background: var(--danger); color: #fff; }

        /* ── EMPTY STATE ── */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--muted);
        }
        .empty-state .empty-icon { font-size: 3rem; margin-bottom: 1rem; }
        .empty-state p { font-size: .95rem; }

        /* ── LOADER ── */
        .loader-row td {
            text-align: center;
            padding: 3rem;
            color: var(--muted);
        }
        .spinner {
            width: 32px; height: 32px;
            border: 3px solid var(--border);
            border-top-color: var(--navy-light);
            border-radius: 50%;
            animation: spin .7s linear infinite;
            margin: 0 auto 1rem;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── FORM PANEL ── */
        .form-panel {
            background: var(--card);
            border-radius: 16px;
            box-shadow: 0 2px 20px rgba(13,59,102,.08);
            padding: 1.75rem;
            margin-bottom: 1.5rem;
            display: none;
            animation: slideDown .25s ease;
        }
        .form-panel.open { display: block; }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .form-panel h5 {
            font-size: 1rem; font-weight: 700; color: var(--navy);
            margin-bottom: 1.25rem; padding-bottom: .75rem;
            border-bottom: 1.5px solid var(--border);
        }
        .form-actions { display: flex; gap: .75rem; margin-top: 1.25rem; }
        .btn-cancel-form {
            padding: .7rem 1.5rem; background: var(--bg);
            border: none; border-radius: 10px;
            font-size: .9rem; font-weight: 600;
            cursor: pointer; color: var(--muted); transition: background .2s;
        }
        .btn-cancel-form:hover { background: #dce6f0; color: var(--text); }

        /* ── FORM ── */
        .form-group { margin-bottom: 1.1rem; }
        .form-group label {
            display: block;
            font-size: .78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: var(--muted);
            margin-bottom: .4rem;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            padding: .65rem .9rem;
            font-family: inherit;
            font-size: .9rem;
            color: var(--text);
            background: #fff;
            outline: none;
            transition: border .2s, box-shadow .2s;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--navy-light);
            box-shadow: 0 0 0 3px rgba(37,99,166,.12);
        }
        .form-group textarea { resize: vertical; min-height: 80px; }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

        .btn-submit {
            padding: .7rem 2rem;
            background: linear-gradient(135deg, var(--navy), var(--navy-light));
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: .9rem;
            font-weight: 700;
            cursor: pointer;
            transition: all .2s;
            box-shadow: 0 4px 15px rgba(13,59,102,.25);
        }
        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(13,59,102,.35);
        }

        /* ── TOAST ── */
        .toast-wrap {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            z-index: 2000;
            display: flex;
            flex-direction: column;
            gap: .5rem;
        }
        .toast-item {
            background: var(--navy);
            color: #fff;
            padding: .8rem 1.2rem;
            border-radius: 12px;
            font-size: .88rem;
            font-weight: 500;
            box-shadow: 0 8px 25px rgba(0,0,0,.2);
            display: flex;
            align-items: center;
            gap: .5rem;
            animation: toastIn .3s ease;
        }
        .toast-item.success { background: #0d7a5a; }
        .toast-item.error   { background: #c0002d; }
        @keyframes toastIn {
            from { opacity: 0; transform: translateX(30px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        /* ── CONFIRM MODAL ── */
        .modal-backdrop-custom {
            position: fixed;
            inset: 0;
            background: rgba(10,25,45,.6);
            backdrop-filter: blur(4px);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity .25s;
        }
        .modal-backdrop-custom.show {
            opacity: 1;
            pointer-events: all;
        }
        .confirm-box {
            background: #fff;
            border-radius: 18px;
            padding: 2rem;
            max-width: 380px;
            width: 100%;
            text-align: center;
            box-shadow: 0 25px 60px rgba(10,25,45,.25);
            transform: scale(.92);
            transition: transform .3s cubic-bezier(.34,1.56,.64,1);
        }
        .modal-backdrop-custom.show .confirm-box { transform: scale(1); }
        .confirm-icon { font-size: 3rem; margin-bottom: 1rem; }
        .confirm-box h5 { font-weight: 700; color: var(--navy); margin-bottom: .5rem; }
        .confirm-box p  { font-size: .88rem; color: var(--muted); margin-bottom: 1.5rem; }
        .confirm-btns   { display: flex; gap: .75rem; }
        .btn-cancel {
            flex: 1; padding: .7rem;
            background: var(--bg);
            border: none; border-radius: 10px;
            font-weight: 600; font-size: .88rem;
            cursor: pointer; color: var(--text);
            transition: background .2s;
        }
        .btn-cancel:hover { background: #dce6f0; }
        .btn-confirm-del {
            flex: 1; padding: .7rem;
            background: var(--danger);
            border: none; border-radius: 10px;
            font-weight: 600; font-size: .88rem;
            cursor: pointer; color: #fff;
            transition: all .2s;
        }
        .btn-confirm-del:hover { background: #c0002d; }
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
            <a class="nav-link" href="dashboard_admin.php">📊 Dashboard</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="clientes.php">👥 Clientes</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="productos.php">📦 Productos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="facturar.php">🧾 Facturas</a>
        </li>
        <?php if($_SESSION['rol'] == "Administrador"): ?>
        <li class="nav-item">
            <a class="nav-link" href="historial_facturas.php">📋 Historial</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="usuarios.php">👤 Usuarios</a>
        </li>
        <?php endif ; ?>
        <li class="nav-item mt-auto">
            <a class="nav-link logout" href="../backend/auth/logout.php">🚪 Cerrar sesión</a>
        </li>
    </ul>
</div>

<!-- MAIN -->
<div class="main">

    <div class="page-header">
        <h1>
            <span>Gestión</span>
            Productos
        </h1>
        <?php if($_SESSION['rol'] === "Administrador"): ?>
        <button class="btn-add" onclick="abrirModal()">
            <span id="btnAddIcon">＋</span>
            <span id="btnAddLabel">Agregar Producto</span>
        </button>
        <?php endif; ?>
    </div>

    <!-- FORM PANEL INLINE -->
    <div class="form-panel" id="formPanel">
        <h5 id="formTitulo"> Agregar Producto</h5>

        <input type="hidden" id="productoId">

        <div class="form-group">
            <label>Nombre del producto</label>
            <input type="text" id="nombre" placeholder="Ej. Vidrio templado 6mm">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Precio (L)</label>
                <input type="number" id="precio" placeholder="0.00" step="0.01" min="0">
            </div>
            <div class="form-group">
                <label>Stock</label>
                <input type="number" id="stock" placeholder="0" min="0">
            </div>
        </div>

        <div class="form-group">
            <label>Descripción</label>
            <textarea id="descripcion" placeholder="Descripción opcional del producto..."></textarea>
        </div>

        <div class="form-actions">
            <button class="btn-submit" onclick="guardarProducto()">
                <span id="btnSubmitLabel">Guardar Producto</span>
            </button>
            <button class="btn-cancel-form" onclick="cerrarForm()">Cancelar</button>
        </div>
    </div>

    <div class="toolbar">
        <div class="search-wrap">
            <span class="search-icon"></span>
            <input type="text" id="searchInput" placeholder="Buscar producto..." oninput="filtrarTabla()">
        </div>
        <div style="font-size:.82rem; color:var(--muted);">
            <span id="countLabel">—</span> productos
        </div>
    </div>

    <div class="table-card">
        <table id="tablaProductos">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Estado</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="bodyProductos">
                <tr class="loader-row">
                    <td colspan="7">
                        <div class="spinner"></div>
                        Cargando productos...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</div>

<!-- ── MODAL CONFIRMAR ELIMINAR ── -->
<div class="modal-backdrop-custom" id="modalConfirm">
    <div class="confirm-box">
        <div class="confirm-icon"></div>
        <h5>¿Desactivar producto?</h5>
        <p>El producto quedará inactivo y no aparecerá disponible para facturar.</p>
        <div class="confirm-btns">
            <button class="btn-cancel" onclick="cerrarConfirm()">Cancelar</button>
            <button class="btn-confirm-del" onclick="confirmarEliminar()">Sí, desactivar</button>
        </div>
    </div>
</div>

<!-- TOASTS -->
<div class="toast-wrap" id="toastWrap"></div>

<script>
let productos   = [];
let eliminarId  = null;
let formOpen    = false;

// ── Cargar productos ──────────────────────────────────────────────────────────
function cargarProductos() {
    fetch("../backend/productos/listar.php")
        .then(r => r.json())
        .then(data => {
            productos = data;
            renderTabla(data);
        })
        .catch(() => toast("Error al cargar productos", "error"));
}

function renderTabla(data) {
    const tbody = document.getElementById("bodyProductos");
    document.getElementById("countLabel").textContent = data.length;

    if (data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7">
                    <div class="empty-state">
                        <div class="empty-icon"></div>
                        <p>No hay productos registrados aún.</p>
                    </div>
                </td>
            </tr>`;
        return;
    }

    const esAdmin = "<?php echo $_SESSION['rol']; ?>" === "Administrador";

    tbody.innerHTML = data.map((p, i) => `
        <tr>
            <td style="color:var(--muted);font-size:.78rem;font-weight:600;">${i + 1}</td>
            <td style="font-weight:600;">${p.nombre}</td>
            <td class="precio-cell">L ${parseFloat(p.precio).toLocaleString('es-HN', {minimumFractionDigits:2})}</td>
            <td><span class="stock-pill">${p.stock}</span></td>
            <td>
                <span class="badge-estado ${p.estado == 1 ? 'badge-activo' : 'badge-inactivo'}">
                    ${p.estado == 1 ? '● Activo' : '○ Inactivo'}
                </span>
            </td>
            <td style="color:var(--muted);font-size:.82rem;">${p.descripcion || '—'}</td>
            <td>
                ${esAdmin ? `
                <button class="action-btn btn-edit" onclick="editarProducto(${p.id_producto})"> Editar</button>
                <button class="action-btn btn-del"  onclick="pedirEliminar(${p.id_producto})" style="margin-left:.4rem;">Eliminar</button>
                ` : '<span style="color:var(--muted);font-size:.78rem;">—</span>'}
            </td>
        </tr>
    `).join('');
}

// ── Buscador ──────────────────────────────────────────────────────────────────
function filtrarTabla() {
    const q = document.getElementById("searchInput").value.toLowerCase();
    const filtrado = productos.filter(p =>
        p.nombre.toLowerCase().includes(q) ||
        (p.descripcion && p.descripcion.toLowerCase().includes(q))
    );
    renderTabla(filtrado);
}

// ── Toggle form panel ─────────────────────────────────────────────────────────
function abrirModal() {
    if (formOpen) { cerrarForm(); return; }
    resetForm();
    document.getElementById("formTitulo").textContent     = "Agregar Producto";
    document.getElementById("btnSubmitLabel").textContent = "Guardar Producto";
    document.getElementById("formPanel").classList.add("open");
    document.getElementById("btnAddLabel").textContent = "Cerrar";
    document.getElementById("btnAddIcon").textContent  = "✕";
    formOpen = true;
}

function cerrarForm() {
    document.getElementById("formPanel").classList.remove("open");
    document.getElementById("btnAddLabel").textContent = "Agregar Producto";
    document.getElementById("btnAddIcon").textContent  = "＋";
    formOpen = false;
    resetForm();
}

function resetForm() {
    ["productoId","nombre","precio","stock","descripcion"]
        .forEach(id => document.getElementById(id).value = "");
}

function editarProducto(id) {
    const p = productos.find(x => x.id_producto == id);
    if (!p) return;

    document.getElementById("formTitulo").textContent     = " Editar Producto";
    document.getElementById("btnSubmitLabel").textContent = "Actualizar Producto";
    document.getElementById("productoId").value  = p.id_producto;
    document.getElementById("nombre").value      = p.nombre;
    document.getElementById("precio").value      = p.precio;
    document.getElementById("stock").value       = p.stock;
    document.getElementById("descripcion").value = p.descripcion || "";

    document.getElementById("formPanel").classList.add("open");
    document.getElementById("btnAddLabel").textContent = "Cerrar";
    document.getElementById("btnAddIcon").textContent  = "✕";
    formOpen = true;
    window.scrollTo({ top: 0, behavior: "smooth" });
}

// ── Guardar (crear o actualizar) ──────────────────────────────────────────────
function guardarProducto() {
    const id          = document.getElementById("productoId").value;
    const nombre      = document.getElementById("nombre").value.trim();
    const precio      = document.getElementById("precio").value;
    const stock       = document.getElementById("stock").value;
    const descripcion = document.getElementById("descripcion").value.trim();

    if (!nombre || precio === "" || stock === "") {
        toast("Completa los campos obligatorios", "error"); return;
    }

    const endpoint = id
        ? "../backend/productos/actualizar.php"
        : "../backend/productos/guardar.php";

    // Al crear siempre activo (1), al editar no se toca el estado
    const payload = { nombre, precio, stock, descripcion, estado: 1 };
    if (id) payload.id_producto = id;

    fetch(endpoint, {
        method : "POST",
        headers: { "Content-Type": "application/json" },
        body   : JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            toast(id ? "Producto actualizado " : "Producto guardado ", "success");
            cerrarForm();
            cargarProductos();
        } else {
            toast(data.message || "Error al guardar", "error");
        }
    })
    .catch(() => toast("Error de conexión", "error"));
}

// ── Eliminar ──────────────────────────────────────────────────────────────────
function pedirEliminar(id) {
    eliminarId = id;
    document.getElementById("modalConfirm").classList.add("show");
}
function cerrarConfirm() {
    eliminarId = null;
    document.getElementById("modalConfirm").classList.remove("show");
}
function confirmarEliminar() {
    if (!eliminarId) return;
    fetch("../backend/productos/eliminar.php", {
        method : "POST",
        headers: { "Content-Type": "application/json" },
        body   : JSON.stringify({ id_producto: eliminarId })
    })
    .then(r => r.json())
    .then(data => {
        cerrarConfirm();
        if (data.success) {
            toast("Producto desactivado ", "success");
            cargarProductos();
        } else {
            toast(data.message || "Error al desactivar", "error");
        }
    })
    .catch(() => toast("Error de conexión", "error"));
}

document.addEventListener("keydown", e => { if (e.key === "Escape") cerrarConfirm(); });
document.getElementById("modalConfirm").addEventListener("click", function(e){
    if (e.target === this) cerrarConfirm();
});

// ── Toast ─────────────────────────────────────────────────────────────────────
function toast(msg, tipo = "") {
    const wrap = document.getElementById("toastWrap");
    const el   = document.createElement("div");
    el.className = `toast-item ${tipo}`;
    el.textContent = msg;
    wrap.appendChild(el);
    setTimeout(() => el.remove(), 3500);
}

// ── Init ──────────────────────────────────────────────────────────────────────
cargarProductos();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>