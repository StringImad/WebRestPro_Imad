<?php
session_name("22_23");
session_start();
require_once __DIR__."/src/funciones_ctes.php";
require_login();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Panel – WebRestPro</title>
  <link rel="stylesheet" href="styles/normalize.css">
  <link rel="stylesheet" href="styles/main.css">
  <style>/* —— Modal limpio y centrado (muy minimal) —— */
#modalBackdrop{
  position: fixed;
  inset: 0;
  display: none;                 /* lo abres con JS */
  align-items: center;
  justify-content: center;
  background: rgba(0,0,0,.4);
  z-index: 9999;
}

#modalBackdrop .modal{
  width: min(720px, 92vw);
  max-height: 88vh;
  overflow: auto;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  box-shadow: 0 16px 40px rgba(0,0,0,.25);
  padding: 18px 20px;
  color: #111827;
}

/* layout del formulario dentro del modal */
#modalBackdrop .modal .row{
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px 14px;
  margin-top: 6px;
}

#modalBackdrop .modal label{
  display: block;
  font-size: .9rem;
  color: #374151;
  margin-bottom: 6px;
}

#modalBackdrop .modal input,
#modalBackdrop .modal textarea{
  width: 100%;
  padding: 10px 12px;
  font-size: .95rem;
  border: 1px solid #d1d5db;
  border-radius: 10px;
  outline: none;
  background: #fff;
}

#modalBackdrop .modal textarea{ resize: vertical; }

/* que Descripción ocupe dos columnas */
#f_desc{ grid-column: 1 / -1; }

/* botones del modal alineados a la derecha */
#modalBackdrop .modal .actions{
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 14px;
}

/* botones */
.btn{ padding:10px 14px; border:1px solid #d1d5db; border-radius:10px; background:#fff; color:#111; cursor:pointer; }
.btn:hover{ background:#f8fafc; }
.btn.primary{ background:#2563eb; border-color:#2563eb; color:#fff; }
.btn.primary:hover{ filter:brightness(1.05); }
.btn.danger{ background:#ef4444; border-color:#ef4444; color:#fff; }

/* tabla un poco más agradable (opcional) */
table{ background:#fff; border-radius:12px; overflow:hidden; }
th{ background:#f3f4f6; font-weight: 600; }
tbody tr:nth-child(even){ background:#fafafa; }


    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; padding: 16px; }
    header { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;}
    select, input, textarea { padding:8px; width:100%; box-sizing:border-box; }
    table { width:100%; border-collapse: collapse; margin-top:12px;}
    th, td { border:1px solid #e5e7eb; padding:8px; }
    th { background:#f3f4f6; text-align:left; }
    .btn { padding:8px 12px; border:1px solid #d1d5db; background:#fff; cursor:pointer; border-radius:8px;}
    .btn.primary { background:#2563eb; color:#fff; border-color:#2563eb;}
    .btn.danger { background:#ef4444; color:#fff; border-color:#ef4444;}
    .actions { display:flex; gap:8px; }
    .row { display:grid; grid-template-columns: 1fr 1fr; gap:12px; }
    .modal-backdrop{ position:fixed; inset:0; background:rgba(0,0,0,.35); display:none; place-items:center;}
    .modal{ background:#fff; padding:16px; border-radius:12px; width:min(680px, 92vw); }
  </style>
</head>
<body>
<header>
  <h1>Mis restaurantes</h1>
  <a class="btn" href="principal.php?salir=1">Cerrar sesión</a>
</header>

<?php
if (isset($_GET['salir'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}
?>

<section>
  <label for="restSelect"><strong>Selecciona restaurante</strong></label>
  <select id="restSelect"></select>
  <div style="margin-top:10px">
    <button id="addBtn" class="btn primary">Añadir plato</button>
  </div>

  <table id="tablaPlatos">
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Descripción</th>
        <th>Alérgenos</th>
        <th>½ Ración</th>
        <th>Precio</th>
        <th>Tipo</th>
        <th style="width:160px;">Acciones</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
</section>

<!-- MODAL Crear/Editar -->
<div class="modal-backdrop" id="modalBackdrop">
  <div class="modal">
    <h3 id="modalTitle">Nuevo plato</h3>
    <div class="row">
      <div>
        <label>Nombre</label>
        <input id="f_name" type="text">
      </div>
      <div>
        <label>Tipo (categoría)</label>
        <input id="f_type" type="text" placeholder="Entrantes, Ensaladas...">
      </div>
      <div style="grid-column:1/-1">
        <label>Descripción</label>
        <textarea id="f_desc" rows="2"></textarea>
      </div>
      <div>
        <label>Alérgenos</label>
        <input id="f_allergen" type="text" placeholder="gluten, lactosa...">
      </div>
      <div>
        <label>½ Ración</label>
        <input id="f_half" type="text" placeholder="ej. 8">
      </div>
      <div>
        <label>Precio</label>
        <input id="f_price" type="text" placeholder="ej. 15">
      </div>
    </div>
    <div class="actions" >
      <button id="cancelBtn" class="btn">Cancelar</button>
      <button id="saveBtn" class="btn primary" type="button">Guardar</button>
    </div>
  </div>
</div>

<script src="js/crud_platos.js" defer></script>
</body>
</html>
