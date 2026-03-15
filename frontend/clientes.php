<?php
include "../backend/auth/verificar.php";

if($_SESSION['rol'] != "Administrador" && $_SESSION['rol'] != "Trabajador"){
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes | Vidrería George</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --navy:      #0d3b66;
            --navy-mid:  #1a5276;
            --navy-light:#2563a6;
            --accent:    #00c6ff;
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

        /* ── BTN PRIMARY ── */
        .btn-add {
            background: linear-gradient(135deg, var(--navy), var(--navy-light));
            color: #fff; border: none; padding: .65rem 1.4rem;
            border-radius: 10px; font-weight: 600; font-size: .9rem;
            cursor: pointer; transition: all .25s;
            display: flex; align-items: center; gap: .5rem;
            box-shadow: 0 4px 15px rgba(13,59,102,.3);
        }
        .btn-add:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(13,59,102,.4); color: #fff; }

        /* ── SEARCH ── */
        .search-wrap { position: relative; max-width: 320px; }
        .search-wrap input {
            width: 100%; border: 1.5px solid var(--border); border-radius: 10px;
            padding: .6rem 1rem .6rem 2.6rem; font-family: inherit;
            font-size: .88rem; background: #fff; color: var(--text);
            transition: border .2s; outline: none;
        }
        .search-wrap input:focus { border-color: var(--navy-light); }
        .search-wrap .search-icon {
            position: absolute; left: .85rem; top: 50%;
            transform: translateY(-50%); color: var(--muted);
            font-size: 1rem; pointer-events: none;
        }

        .toolbar {
            display: flex; align-items: center;
            justify-content: space-between; gap: 1rem;
            margin-bottom: 1.5rem; flex-wrap: wrap;
        }

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
        .form-row-custom { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }

        .form-group { margin-bottom: 0; }
        .form-group label {
            display: block; font-size: .75rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: .07em;
            color: var(--muted); margin-bottom: .4rem;
        }
        .form-group input, .form-group select {
            width: 100%; border: 1.5px solid var(--border); border-radius: 10px;
            padding: .65rem .9rem; font-family: inherit; font-size: .9rem;
            color: var(--text); background: #fff; outline: none; transition: border .2s, box-shadow .2s;
        }
        .form-group input:focus, .form-group select:focus {
            border-color: var(--navy-light);
            box-shadow: 0 0 0 3px rgba(37,99,166,.12);
        }

        .btn-rtn {
            background: rgba(37,99,166,.1); color: var(--navy-light);
            border: none; border-radius: 8px; padding: .45rem 1rem;
            font-size: .82rem; font-weight: 600; cursor: pointer;
            transition: all .2s; display: inline-flex; align-items: center; gap: .4rem;
        }
        .btn-rtn:hover { background: var(--navy-light); color: #fff; }

        .form-actions { display: flex; gap: .75rem; margin-top: 1.25rem; }
        .btn-submit {
            padding: .7rem 2rem;
            background: linear-gradient(135deg, var(--navy), var(--navy-light));
            color: #fff; border: none; border-radius: 10px;
            font-size: .9rem; font-weight: 700; cursor: pointer;
            transition: all .2s; box-shadow: 0 4px 15px rgba(13,59,102,.25);
        }
        .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 8px 25px rgba(13,59,102,.35); }
        .btn-cancel-form {
            padding: .7rem 1.5rem; background: var(--bg);
            border: none; border-radius: 10px;
            font-size: .9rem; font-weight: 600;
            cursor: pointer; color: var(--muted); transition: background .2s;
        }
        .btn-cancel-form:hover { background: #dce6f0; color: var(--text); }

        /* ── TABLE ── */
        .table-card {
            background: var(--card); border-radius: 16px;
            box-shadow: 0 2px 20px rgba(13,59,102,.08); overflow: hidden;
        }
        .table-card table { width: 100%; border-collapse: collapse; }
        .table-card thead th {
            background: var(--navy); color: rgba(255,255,255,.85);
            font-size: .75rem; text-transform: uppercase;
            letter-spacing: .08em; padding: 1rem 1.25rem; font-weight: 600;
        }
        .table-card tbody tr { border-bottom: 1px solid var(--border); transition: background .15s; }
        .table-card tbody tr:last-child { border-bottom: none; }
        .table-card tbody tr:hover { background: #f5f9ff; }
        .table-card tbody td { padding: .9rem 1.25rem; font-size: .88rem; color: var(--text); vertical-align: middle; }

        .rtn-badge {
            display: inline-block; padding: .2rem .65rem;
            border-radius: 6px; font-size: .75rem; font-weight: 600;
            background: rgba(37,99,166,.1); color: var(--navy-light);
            font-family: 'Space Mono', monospace;
        }
        .rtn-none { color: var(--muted); font-size: .8rem; }

        /* ── ACTION BTNS ── */
        .action-btn {
            border: none; border-radius: 7px; padding: .35rem .65rem;
            cursor: pointer; font-size: .8rem; transition: all .2s; font-weight: 600;
        }
        .btn-edit { background: rgba(37,99,166,.1); color: var(--navy-light); }
        .btn-edit:hover { background: var(--navy-light); color: #fff; }
        .btn-del  { background: rgba(255,77,109,.1); color: var(--danger); }
        .btn-del:hover { background: var(--danger); color: #fff; }

        /* ── EMPTY / LOADER ── */
        .empty-state { text-align: center; padding: 4rem 2rem; color: var(--muted); }
        .empty-state .empty-icon { font-size: 3rem; margin-bottom: 1rem; }
        .loader-row td { text-align: center; padding: 3rem; color: var(--muted); }
        .spinner {
            width: 32px; height: 32px; border: 3px solid var(--border);
            border-top-color: var(--navy-light); border-radius: 50%;
            animation: spin .7s linear infinite; margin: 0 auto 1rem;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── CONFIRM MODAL ── */
        .modal-backdrop-custom {
            position: fixed; inset: 0;
            background: rgba(10,25,45,.6); backdrop-filter: blur(4px);
            z-index: 1000; display: flex; align-items: center; justify-content: center;
            opacity: 0; pointer-events: none; transition: opacity .25s;
        }
        .modal-backdrop-custom.show { opacity: 1; pointer-events: all; }
        .confirm-box {
            background: #fff; border-radius: 18px; padding: 2rem;
            max-width: 380px; width: 100%; text-align: center;
            box-shadow: 0 25px 60px rgba(10,25,45,.25);
            transform: scale(.92); transition: transform .3s cubic-bezier(.34,1.56,.64,1);
        }
        .modal-backdrop-custom.show .confirm-box { transform: scale(1); }
        .confirm-icon { font-size: 3rem; margin-bottom: 1rem; }
        .confirm-box h5 { font-weight: 700; color: var(--navy); margin-bottom: .5rem; }
        .confirm-box p  { font-size: .88rem; color: var(--muted); margin-bottom: 1.5rem; }
        .confirm-btns   { display: flex; gap: .75rem; }
        .btn-cancel-confirm {
            flex: 1; padding: .7rem; background: var(--bg);
            border: none; border-radius: 10px; font-weight: 600;
            font-size: .88rem; cursor: pointer; color: var(--text); transition: background .2s;
        }
        .btn-cancel-confirm:hover { background: #dce6f0; }
        .btn-confirm-del {
            flex: 1; padding: .7rem; background: var(--danger);
            border: none; border-radius: 10px; font-weight: 600;
            font-size: .88rem; cursor: pointer; color: #fff; transition: all .2s;
        }
        .btn-confirm-del:hover { background: #c0002d; }

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
        <li class="nav-item">
            <a class="nav-link active" href="clientes.php">👥 Clientes</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="productos.php">📦 Productos</a>
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
            <span>Gestión</span>
            Clientes
        </h1>
        <button class="btn-add" onclick="toggleForm()">
            <span id="btnAddIcon">＋</span>
            <span id="btnAddLabel">Nuevo Cliente</span>
        </button>
    </div>

    <!-- FORM PANEL -->
    <div class="form-panel" id="formPanel">
        <h5 id="formTitulo">➕ Agregar Cliente</h5>

        <input type="hidden" id="id_cliente">

        <div class="form-row-custom">
            <div class="form-group">
                <label>Nombre *</label>
                <input type="text" id="nombre" placeholder="Nombre completo">
            </div>
            <div class="form-group">
                <label>Cédula</label>
                <input type="text" id="cedula" placeholder="13 dígitos" maxlength="13">
            </div>
            <div class="form-group">
                <label>Teléfono</label>
                <input type="text" id="telefono" placeholder="8 dígitos" maxlength="8">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" id="email" placeholder="correo@ejemplo.com">
            </div>
            <div class="form-group" style="grid-column: span 2;">
                <label>Dirección</label>
                <input type="text" id="direccion" placeholder="Dirección del cliente">
            </div>
        </div>

        <div style="margin-top: 1rem;">
            <button class="btn-rtn" id="toggleRTN" onclick="toggleRTN()">
                 ¿Con RTN?
            </button>
        </div>

        <div id="rtnDiv" style="display:none; margin-top:.75rem;">
            <div class="form-group" style="max-width:260px;">
                <label>R.T.N</label>
                <input type="text" id="rtn" placeholder="14 dígitos" maxlength="14">
            </div>
        </div>

        <div class="form-actions">
            <button class="btn-submit" onclick="submitCliente()">
                <span id="btnSubmitLabel">Guardar Cliente</span>
            </button>
            <button class="btn-cancel-form" onclick="cerrarForm()">Cancelar</button>
        </div>
    </div>

    <!-- TOOLBAR -->
    <div class="toolbar">
        <div class="search-wrap">
            <span class="search-icon"></span>
            <input type="text" id="searchInput" placeholder="Buscar cliente..." oninput="filtrarTabla()">
        </div>
        <div style="font-size:.82rem; color:var(--muted);">
            <span id="countLabel">—</span> clientes
        </div>
    </div>

    <!-- TABLE -->
    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Cédula</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>R.T.N</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tablaClientes">
                <tr class="loader-row">
                    <td colspan="7">
                        <div class="spinner"></div>
                        Cargando clientes...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</div>

<!-- CONFIRM MODAL -->
<div class="modal-backdrop-custom" id="modalConfirm">
    <div class="confirm-box">
        <div class="confirm-icon"></div>
        <h5>¿Eliminar cliente?</h5>
        <p>Esta acción no se puede deshacer.</p>
        <div class="confirm-btns">
            <button class="btn-cancel-confirm" onclick="cerrarConfirm()">Cancelar</button>
            <button class="btn-confirm-del"    onclick="confirmarEliminar()">Sí, eliminar</button>
        </div>
    </div>
</div>

<!-- TOASTS -->
<div class="toast-wrap" id="toastWrap"></div>

<script>
const API = "../backend/clientes/";
let clientes   = [];
let eliminarId = null;
let formOpen   = false;

// Solo números
["cedula","telefono","rtn"].forEach(id => {
    document.getElementById(id).addEventListener("input", function(){
        this.value = this.value.replace(/\D/g, "");
    });
});

// ── Toggle form ───────────────────────────────────────────────────────────────
function toggleForm() {
    if (formOpen) {
        cerrarForm();
    } else {
        resetForm();
        document.getElementById("formTitulo").textContent    = "Agregar Cliente";
        document.getElementById("btnSubmitLabel").textContent = "Guardar Cliente";
        document.getElementById("formPanel").classList.add("open");
        document.getElementById("btnAddLabel").textContent = "Cerrar";
        document.getElementById("btnAddIcon").textContent  = "✕";
        formOpen = true;
    }
}

function cerrarForm() {
    document.getElementById("formPanel").classList.remove("open");
    document.getElementById("btnAddLabel").textContent = "Nuevo Cliente";
    document.getElementById("btnAddIcon").textContent  = "＋";
    formOpen = false;
    resetForm();
}

function resetForm() {
    ["id_cliente","nombre","cedula","telefono","email","direccion","rtn"]
        .forEach(id => document.getElementById(id).value = "");
    document.getElementById("rtnDiv").style.display = "none";
}

function toggleRTN() {
    const div = document.getElementById("rtnDiv");
    div.style.display = div.style.display === "none" ? "block" : "none";
}

// ── Listar ────────────────────────────────────────────────────────────────────
function listarClientes() {
    fetch(API + "listar.php")
    .then(r => r.json())
    .then(data => {
        clientes = data;
        renderTabla(data);
    })
    .catch(() => toast("Error al cargar clientes", "error"));
}

function renderTabla(data) {
    const tbody = document.getElementById("tablaClientes");
    document.getElementById("countLabel").textContent = data.length;

    if (data.length === 0) {
        tbody.innerHTML = `
            <tr><td colspan="7">
                <div class="empty-state">
                    <div class="empty-icon">👥</div>
                    <p>No hay clientes registrados aún.</p>
                </div>
            </td></tr>`;
        return;
    }

    const esAdmin = "<?php echo $_SESSION['rol']; ?>" === "Administrador";

    tbody.innerHTML = data.map((c, i) => `
        <tr>
            <td style="color:var(--muted);font-size:.78rem;font-weight:600;">${i + 1}</td>
            <td style="font-weight:600;">${c.nombre}</td>
            <td style="font-family:'Space Mono',monospace;font-size:.82rem;">${c.cedula || '—'}</td>
            <td style="font-family:'Space Mono',monospace;font-size:.82rem;">${c.telefono || '—'}</td>
            <td style="color:var(--muted);font-size:.82rem;">${c.email || '—'}</td>
            <td>
                ${c.rtn && c.rtn.trim() !== ""
                    ? `<span class="rtn-badge">${c.rtn}</span>`
                    : `<span class="rtn-none">Sin R.T.N</span>`}
            </td>
            <td>
                <button class="action-btn btn-edit" onclick='editarCliente(${JSON.stringify(c)})'> Editar</button>
                ${esAdmin ? `<button class="action-btn btn-del" onclick="pedirEliminar(${c.id_cliente})" style="margin-left:.4rem;">Eliminar</button>` : ''}
            </td>
        </tr>
    `).join('');
}

// ── Buscar ────────────────────────────────────────────────────────────────────
function filtrarTabla() {
    const q = document.getElementById("searchInput").value.toLowerCase();
    renderTabla(clientes.filter(c =>
        c.nombre.toLowerCase().includes(q) ||
        (c.cedula   && c.cedula.includes(q)) ||
        (c.telefono && c.telefono.includes(q)) ||
        (c.email    && c.email.toLowerCase().includes(q))
    ));
}

// ── Editar ────────────────────────────────────────────────────────────────────
function editarCliente(c) {
    document.getElementById("id_cliente").value = c.id_cliente;
    document.getElementById("nombre").value     = c.nombre;
    document.getElementById("cedula").value     = c.cedula    || "";
    document.getElementById("telefono").value   = c.telefono  || "";
    document.getElementById("email").value      = c.email     || "";
    document.getElementById("direccion").value  = c.direccion || "";
    document.getElementById("rtn").value        = c.rtn       || "";

    document.getElementById("rtnDiv").style.display =
        (c.rtn && c.rtn.trim() !== "") ? "block" : "none";

    document.getElementById("formTitulo").textContent     = "Editar Cliente";
    document.getElementById("btnSubmitLabel").textContent = "Actualizar Cliente";
    document.getElementById("formPanel").classList.add("open");
    document.getElementById("btnAddLabel").textContent = "Cerrar";
    document.getElementById("btnAddIcon").textContent  = "✕";
    formOpen = true;

    window.scrollTo({ top: 0, behavior: "smooth" });
}

// ── Submit ────────────────────────────────────────────────────────────────────
function submitCliente() {
    const id       = document.getElementById("id_cliente").value;
    const nombre   = document.getElementById("nombre").value.trim();
    const cedula   = document.getElementById("cedula").value.trim();
    const telefono = document.getElementById("telefono").value.trim();
    const email    = document.getElementById("email").value.trim();
    const direccion= document.getElementById("direccion").value.trim();
    const rtn      = document.getElementById("rtn").value.trim();
    const rtnVisible = document.getElementById("rtnDiv").style.display !== "none";

    if (!nombre) { toast("El nombre es obligatorio", "error"); return; }
    if (cedula   && cedula.length   !== 13) { toast("La cédula debe tener 13 dígitos", "error"); return; }
    if (telefono && telefono.length !== 8)  { toast("El teléfono debe tener 8 dígitos", "error"); return; }
    if (rtnVisible && rtn && rtn.length !== 14) { toast("El RTN debe tener 14 dígitos", "error"); return; }

    const payload = { id_cliente: id, nombre, cedula, telefono, email, direccion, rtn: rtnVisible ? rtn : "" };
    const url     = id ? "actualizar.php" : "insertar.php";

    fetch(API + url, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(data => {
        if (data.mensaje) {
            toast(data.mensaje, "success");
            cerrarForm();
            listarClientes();
        } else if (data.error) {
            toast(data.error, "error");
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
    fetch(API + "eliminar.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id_cliente: eliminarId })
    })
    .then(r => r.json())
    .then(data => {
        cerrarConfirm();
        toast(data.mensaje || data.error || "Listo", data.mensaje ? "success" : "error");
        listarClientes();
    })
    .catch(() => toast("Error de conexión", "error"));
}

// ── Escape ────────────────────────────────────────────────────────────────────
document.addEventListener("keydown", e => {
    if (e.key === "Escape") cerrarConfirm();
});
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
listarClientes();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>