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
  <style>
/* Botones (por si acaso) */
/* Botones (por si acaso) */
.btn { padding:8px 12px; border:1px solid #d1d5db; background:#fff; color:#111; cursor:pointer; border-radius:8px;}
.btn.primary { background:#2563eb; color:#fff; border-color:#2563eb;}
.btn.danger  { background:#ef4444; color:#fff; border-color:#ef4444;}

/* Backdrop + modal con alta especificidad por ID */
#modalBackdrop{
  position: fixed !important;
  inset: 0 !important;
  background: rgba(0,0,0,.45) !important;
  display: none !important;         /* se abre vía JS */
  z-index: 999999 !important;       /* por encima de TODO */
  align-items: center !important;
  justify-content: center !important;
}

#modalBackdrop .modal{
  /* background:#fff !important;
  padding:16px !important;
  border-radius:12px !important;
  width:min(680px, 92vw) !important;
  max-height:90vh !important;
  overflow:auto !important;
  box-shadow:0 10px 25px rgba(0,0,0,.25) !important;
  border:3px solid #2563eb !important;  */
  /* <- DEBUG: QUÍTALO cuando lo veas */
  display:block !important;
}


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
    <div style="display:flex; gap:8px; justify-content:flex-end; margin-top:12px;">
      <button id="cancelBtn" class="btn">Cancelar</button>
      <button id="saveBtn" class="btn primary" type="button">Guardar</button>
    </div>
  </div>
</div>

<script src="js/crud_platos.js" defer></script>
</body>
</html>
