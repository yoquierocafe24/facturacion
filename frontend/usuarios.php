<?php
include "../backend/auth/verificar.php";

if($_SESSION['rol'] != "Administrador"){
    header("Location: dashboard_trabajador.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios | Vidrería George</title>
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

        /* ── BTN ADD ── */
        .btn-add {
            background: linear-gradient(135deg, var(--navy), var(--navy-light));
            color: #fff; border: none; padding: .65rem 1.4rem;
            border-radius: 10px; font-weight: 600; font-size: .9rem;
            cursor: pointer; transition: all .25s;
            display: flex; align-items: center; gap: .5rem;
            box-shadow: 0 4px 15px rgba(13,59,102,.3);
        }
        .btn-add:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(13,59,102,.4); color: #fff; }

        /* ── FORM PANEL ── */
        .form-panel {
            background: var(--card); border-radius: 16px;
            box-shadow: 0 2px 20px rgba(13,59,102,.08);
            padding: 1.75rem; margin-bottom: 1.5rem;
            display: none; animation: slideDown .25s ease;
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

        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }

        .form-group { margin-bottom: 0; }
        .form-group label {
            display: block; font-size: .75rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: .07em;
            color: var(--muted); margin-bottom: .4rem;
        }
        .form-group input,
        .form-group select {
            width: 100%; border: 1.5px solid var(--border); border-radius: 10px;
            padding: .65rem .9rem; font-family: inherit; font-size: .9rem;
            color: var(--text); background: #fff; outline: none;
            transition: border .2s, box-shadow .2s;
        }
        .form-group input:focus,
        .form-group select:focus {
            border-color: var(--navy-light);
            box-shadow: 0 0 0 3px rgba(37,99,166,.12);
        }

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
            border: none; border-radius: 10px; font-size: .9rem;
            font-weight: 600; cursor: pointer; color: var(--muted); transition: background .2s;
        }
        .btn-cancel-form:hover { background: #dce6f0; color: var(--text); }

        /* ── SEARCH ── */
        .toolbar {
            display: flex; align-items: center;
            justify-content: space-between; gap: 1rem;
            margin-bottom: 1.5rem; flex-wrap: wrap;
        }
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
            transform: translateY(-50%); color: var(--muted); pointer-events: none;
        }

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

        .rol-badge {
            display: inline-flex; align-items: center; gap: .35rem;
            padding: .3rem .75rem; border-radius: 20px;
            font-size: .75rem; font-weight: 600;
        }
        .rol-admin  { background: rgba(13,59,102,.1);  color: var(--navy); }
        .rol-worker { background: rgba(37,99,166,.1);  color: var(--navy-light); }

        .avatar-table {
            width: 34px; height: 34px; border-radius: 50%;
            background: var(--navy-mid);
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: .8rem; color: #fff;
        }

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
        <li class="nav-item"><a class="nav-link" href="dashboard_admin.php">📊 Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="clientes.php">👥 Clientes</a></li>
        <li class="nav-item"><a class="nav-link" href="productos.php">📦 Productos</a></li>
        <li class="nav-item"><a class="nav-link" href="facturar.php">🧾 Facturas</a></li>
        <?php if($_SESSION['rol'] == "Administrador"): ?>
        <li class="nav-item"><a class="nav-link" href="historial_facturas.php">📋 Historial</a></li>
        <li class="nav-item"><a class="nav-link active" href="usuarios.php">👤 Usuarios</a></li>
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
            Usuarios
        </h1>
        <button class="btn-add" onclick="toggleForm()">
            <span id="btnAddIcon">＋</span>
            <span id="btnAddLabel">Nuevo Usuario</span>
        </button>
    </div>

    <!-- FORM PANEL -->
    <div class="form-panel" id="formPanel">
        <h5 id="formTitulo">Agregar Usuario</h5>
        <input type="hidden" id="id_usuario">

        <div class="form-grid">
            <div class="form-group">
                <label>Nombre</label>
                <input type="text" id="nombre" placeholder="Nombre completo">
            </div>
            <div class="form-group">
                <label>Usuario</label>
                <input type="text" id="usuario" placeholder="Nombre de usuario">
            </div>
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" id="password" placeholder="••••••••">
            </div>
            <div class="form-group">
                <label>Rol</label>
                <select id="id_rol">
                    <option value="">Seleccione un rol</option>
                    <option value="1">Administrador</option>
                    <option value="2">Trabajador</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button class="btn-submit" onclick="submitUsuario()">
                <span id="btnSubmitLabel">Guardar Usuario</span>
            </button>
            <button class="btn-cancel-form" onclick="cerrarForm()">Cancelar</button>
        </div>
    </div>

    <!-- TOOLBAR -->
    <div class="toolbar">
        <div class="search-wrap">
            <span class="search-icon"></span>
            <input type="text" id="searchInput" placeholder="Buscar usuario..." oninput="filtrarTabla()">
        </div>
        <div style="font-size:.82rem; color:var(--muted);">
            <span id="countLabel">—</span> usuarios
        </div>
    </div>

    <!-- TABLE -->
    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Usuario</th>
                    <th>Nombre</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tablaUsuarios">
                <tr class="loader-row">
                    <td colspan="5">
                        <div class="spinner"></div>
                        Cargando usuarios...
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
        <h5>¿Eliminar usuario?</h5>
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
const API = "../backend/usuarios/";
let usuarios   = [];
let eliminarId = null;
let formOpen   = false;

// ── Toggle form ───────────────────────────────────────────────────────────────
function toggleForm() {
    if (formOpen) { cerrarForm(); return; }
    resetForm();
    document.getElementById("formTitulo").textContent     = " Agregar Usuario";
    document.getElementById("btnSubmitLabel").textContent = "Guardar Usuario";
    document.getElementById("formPanel").classList.add("open");
    document.getElementById("btnAddLabel").textContent = "Cerrar";
    document.getElementById("btnAddIcon").textContent  = "✕";
    formOpen = true;
}

function cerrarForm() {
    document.getElementById("formPanel").classList.remove("open");
    document.getElementById("btnAddLabel").textContent = "Nuevo Usuario";
    document.getElementById("btnAddIcon").textContent  = "＋";
    formOpen = false;
    resetForm();
}

function resetForm() {
    ["id_usuario","nombre","usuario","password"].forEach(id => document.getElementById(id).value = "");
    document.getElementById("id_rol").value = "";
}

// ── Listar ────────────────────────────────────────────────────────────────────
function listarUsuarios() {
    fetch(API + "listar.php")
    .then(r => r.json())
    .then(data => { usuarios = data; renderTabla(data); })
    .catch(() => toast("Error al cargar usuarios", "error"));
}

function renderTabla(data) {
    const tbody = document.getElementById("tablaUsuarios");
    document.getElementById("countLabel").textContent = data.length;

    if (data.length === 0) {
        tbody.innerHTML = `
            <tr><td colspan="5">
                <div class="empty-state">
                    <div class="empty-icon">👤</div>
                    <p>No hay usuarios registrados.</p>
                </div>
            </td></tr>`;
        return;
    }

    tbody.innerHTML = data.map((u, i) => `
        <tr>
            <td style="color:var(--muted);font-size:.78rem;font-weight:600;">${i + 1}</td>
            <td>
                <div style="display:flex;align-items:center;gap:.65rem;">
                    <div class="avatar-table">${u.nombre.substring(0,2).toUpperCase()}</div>
                    <span style="font-weight:600;">${u.usuario}</span>
                </div>
            </td>
            <td>${u.nombre}</td>
            <td>
                <span class="rol-badge ${u.id_rol == 5 ? 'rol-admin' : 'rol-worker'}">
                    ${u.id_rol == 5 ? 'Administrador' : 'Trabajador'}
                </span>
            </td>
            <td>
                <button class="action-btn btn-edit" onclick='editarUsuario(${JSON.stringify(u)})'>Editar</button>
                <button class="action-btn btn-del"  onclick="pedirEliminar(${u.id_usuario})" style="margin-left:.4rem;">Eliminar</button>
            </td>
        </tr>
    `).join('');
}

// ── Buscar ────────────────────────────────────────────────────────────────────
function filtrarTabla() {
    const q = document.getElementById("searchInput").value.toLowerCase();
    renderTabla(usuarios.filter(u =>
        u.nombre.toLowerCase().includes(q) ||
        u.usuario.toLowerCase().includes(q)
    ));
}

// ── Editar ────────────────────────────────────────────────────────────────────
function editarUsuario(u) {
    document.getElementById("id_usuario").value = u.id_usuario;
    document.getElementById("nombre").value     = u.nombre;
    document.getElementById("usuario").value    = u.usuario;
    document.getElementById("password").value   = "";
    document.getElementById("id_rol").value     = u.id_rol;

    document.getElementById("formTitulo").textContent     = " Editar Usuario";
    document.getElementById("btnSubmitLabel").textContent = "Actualizar Usuario";
    document.getElementById("formPanel").classList.add("open");
    document.getElementById("btnAddLabel").textContent = "Cerrar";
    document.getElementById("btnAddIcon").textContent  = "✕";
    formOpen = true;
    window.scrollTo({ top: 0, behavior: "smooth" });
}

// ── Submit ────────────────────────────────────────────────────────────────────
function submitUsuario() {
    const id      = document.getElementById("id_usuario").value;
    const nombre  = document.getElementById("nombre").value.trim();
    const usuario = document.getElementById("usuario").value.trim();
    const password= document.getElementById("password").value;
    const id_rol  = document.getElementById("id_rol").value;

    if (!nombre || !usuario || !id_rol) { toast("Completa todos los campos", "error"); return; }
    if (!id && !password) { toast("La contraseña es obligatoria", "error"); return; }

    const url  = id ? "actualizar.php" : "insertar.php";
    const datos = { id_usuario: id, nombre, usuario, password, id_rol };

    fetch(API + url, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(datos)
    })
    .then(r => r.json())
    .then(data => {
        if (data.mensaje) {
            toast(data.mensaje, "success");
            cerrarForm();
            listarUsuarios();
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
        body: JSON.stringify({ id_usuario: eliminarId })
    })
    .then(r => r.json())
    .then(data => {
        cerrarConfirm();
        toast(data.mensaje || data.error, data.mensaje ? "success" : "error");
        listarUsuarios();
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
listarUsuarios();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>