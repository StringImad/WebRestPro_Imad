<?php
session_name("22_23");
session_start();

require_once __DIR__."/../src/funciones_ctes.php";
if (!isset($_SESSION["user_name"])) { header("Location: ../index.php"); exit; }
require_once __DIR__."/../src/seguridad.php";
if ($datos_usu_log->type !== "admin") { header("Location: ../principal.php"); exit; }

/* =========================
   Normalizar api_session
   ========================= */
$api_token = "";
if (isset($_SESSION["api_session"])) {
    $api_token = is_array($_SESSION["api_session"])
        ? ($_SESSION["api_session"]["api_session"] ?? $_SESSION["api_session"]["token"] ?? "")
        : (string)$_SESSION["api_session"];
}

/* =========================
   Flash message (PRG)
   ========================= */
$msg = $_SESSION['flash_msg'] ?? "";
unset($_SESSION['flash_msg']);

/* =========================
   Token idempotente (nonce)
   ========================= */
if (empty($_SESSION['form_nonce'])) {
    $_SESSION['form_nonce'] = bin2hex(random_bytes(16));
}
$form_nonce = $_SESSION['form_nonce'];

/* =========================
   Manejo POST con PRG
   ========================= */
if ($_SERVER["REQUEST_METHOD"] === "POST" && $api_token !== "") {
    // Validar nonce
    if (!isset($_POST['nonce']) || !hash_equals($_SESSION['form_nonce'], $_POST['nonce'])) {
        $_SESSION['flash_msg'] = "⚠️ Sesión del formulario inválida. Vuelve a intentarlo.";
        header("Location: gest_usuarios.php");
        exit;
    }
    // Rotar nonce para evitar reenvío
    $_SESSION['form_nonce'] = bin2hex(random_bytes(16));

    $accion = $_POST["accion"] ?? "";

    if ($accion === "crear") {
        $datos = [
            "name"        => $_POST["name"] ?? "",
            "user_name"   => $_POST["user_name"] ?? "",
            "passwd"      => $_POST["passwd"] ?? "",
            "email"       => $_POST["email"] ?? "",
            "api_session" => $api_token
        ];
        $resp = consumir_servicios_REST(DIR_SERV."/insertarUsuario", "POST", $datos);
        $obj  = json_decode($resp, true);
        if (!$obj) $_SESSION['flash_msg'] = "⚠️ Error creando usuario (respuesta no válida)";
        else if (isset($obj["ok"])) $_SESSION['flash_msg'] = $obj["ok"] ? "✅ Usuario creado" : ("⚠️ Error: ".($obj["error"]["message"] ?? "desconocido"));
        else if (isset($obj["mensaje_error"])) $_SESSION['flash_msg'] = "⚠️ Error: ".$obj["mensaje_error"];
        else $_SESSION['flash_msg'] = "✅ Usuario creado (compatibilidad)";
    }

    if ($accion === "editar") {
        $id  = $_POST["idUser"];
        $url = DIR_SERV."/usuarios/".$id."?api_session=".urlencode($api_token);
        $datos = [
            "name"      => $_POST["name"] ?? "",
            "user_name" => $_POST["user_name"] ?? "",
            "email"     => $_POST["email"] ?? "",
            "passwd"    => $_POST["passwd"] ?? "", // vacío = no cambiar
            "type"      => $_POST["type"] ?? "normal"
        ];
        $resp = consumir_servicios_REST($url, "PUT", $datos);
        $obj  = json_decode($resp, true);
        if (!$obj) $_SESSION['flash_msg'] = "⚠️ Error editando usuario (respuesta no válida)";
        else if (isset($obj["ok"])) $_SESSION['flash_msg'] = $obj["ok"] ? "✅ Usuario editado" : ("⚠️ Error: ".($obj["error"]["message"] ?? "desconocido"));
        else if (isset($obj["mensaje_error"])) $_SESSION['flash_msg'] = "⚠️ Error: ".$obj["mensaje_error"];
        else if (isset($obj["no_login"])) $_SESSION['flash_msg'] = "⚠️ Sesión inválida: ".$obj["no_login"];
        else $_SESSION['flash_msg'] = "✅ Usuario editado (compatibilidad)";
    }

    // PRG: redirige SIEMPRE tras un POST
    header("Location: gest_usuarios.php");
    exit;
}

/* =========================
   Eliminar por GET (redirige)
   ========================= */
if (isset($_GET["delete"]) && $api_token !== "") {
    $id  = $_GET["delete"];
    $url = DIR_SERV."/usuarios/".$id."?api_session=".urlencode($api_token);
    $resp = consumir_servicios_REST($url, "DELETE", null);
    $obj  = json_decode($resp, true);
    if (!$obj) $_SESSION['flash_msg'] = "⚠️ Error eliminando (respuesta no válida)";
    else if (isset($obj["ok"])) $_SESSION['flash_msg'] = $obj["ok"] ? "🗑️ Usuario eliminado" : ("⚠️ Error: ".($obj["error"]["message"] ?? "desconocido"));
    else if (isset($obj["mensaje_error"])) $_SESSION['flash_msg'] = "⚠️ Error: ".$obj["mensaje_error"];
    else if (isset($obj["no_login"])) $_SESSION['flash_msg'] = "⚠️ Sesión inválida: ".$obj["no_login"];
    else $_SESSION['flash_msg'] = "🗑️ Usuario eliminado (compatibilidad)";
    header("Location: gest_usuarios.php");
    exit;
}

/* =========================
   Listar usuarios
   ========================= */
$usuarios = [];
$list_raw = "";
if ($api_token !== "") {
    // Para GET: el helper aceptará string y añadirá ?api_session=...
    $respuesta = consumir_servicios_REST(DIR_SERV."/usuarios","GET", $api_token);
    $list_raw = $respuesta;
    $data = json_decode($respuesta, true);

    // DEBUG: ?debug=1
    if (isset($_GET['debug'])) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            "api_token"               => $api_token,
            "list_response_raw"       => $list_raw,
            "parsed_list"             => $data
        ]);
        exit;
    }

    // Soporta formatos {usuarios:[...]}, {data:[...]}, o array plano
    if (is_array($data)) {
        if (isset($data["usuarios"]) && is_array($data["usuarios"])) {
            $usuarios = $data["usuarios"];
        } elseif (isset($data["data"]) && is_array($data["data"])) {
            $usuarios = $data["data"];
        } elseif (count($data) > 0 && array_key_exists(0, $data)) {
            $usuarios = $data;
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Admin · Usuarios</title>
<link rel="stylesheet" href="../styles/normalize.css">
<style>
  body{font-family:sans-serif;padding:20px;background:#f9fafb;}
  header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;}
  h1{margin:0;}
  .btn{padding:6px 10px;border-radius:6px;border:1px solid #ccc;background:#fff;cursor:pointer;text-decoration:none;color:#111;font-size:.9rem;}
  .btn.primary{background:#2563eb;color:#fff;border-color:#2563eb;}
  .btn.danger{background:#ef4444;color:#fff;border-color:#ef4444;}
  .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px;}
  .card{background:#fff;padding:16px;border-radius:10px;box-shadow:0 2px 6px rgba(0,0,0,.05);}
  .muted{color:#6b7280;font-size:.9rem;margin:2px 0 8px;}
  .badge{padding:2px 6px;border-radius:6px;font-size:.8rem;}
  .badge.admin{background:#2563eb;color:#fff;}
  .badge.normal{background:#e5e7eb;color:#111;}
  .actions{margin-top:10px;display:flex;gap:8px;flex-wrap:wrap;}
  .modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.45);display:none;align-items:center;justify-content:center;}
  .modal{background:#fff;padding:20px;border-radius:10px;width:90%;max-width:480px;}
  .modal label{display:block;margin-top:8px;font-size:.9rem;}
  .modal input,.modal select{width:100%;padding:8px;margin-top:4px;}
  .hint{font-size:.85rem;color:#6b7280;margin:8px 0;}
</style>
</head>
<body>
<header>
  <h1>👤 Gestión de usuarios</h1>
  <div>
    <button id="btnAdd" class="btn primary" type="button">➕ Nuevo usuario</button>
    <a class="btn" href="gest_paginas.php">⬅️ Volver</a>
    <a class="btn danger" href="../principal.php?salir=1">Cerrar sesión</a>
  </div>
</header>

<?php if (!empty($msg)): ?><p class="hint"><?= htmlspecialchars($msg) ?></p><?php endif; ?>

<div class="grid">
  <?php foreach ($usuarios as $u): ?>
    <div class="card" data-row-id="<?= htmlspecialchars($u["idUser"]) ?>">
<h3>
  <a href="usuario_restaurantes.php?id=<?= htmlspecialchars($u['idUser']) ?>&name=<?= urlencode(($u['name'] ?? '') !== '' ? $u['name'] : ($u['user_name'] ?? '')) ?>"
     style="color:inherit;text-decoration:none;">
    <?= htmlspecialchars(($u["name"] ?? "") !== "" ? $u["name"] : ($u["user_name"] ?? "")) ?>
  </a>
</h3>
      <p class="muted">
        <?php if (!empty($u["name"])): ?>
          Usuario: <?= htmlspecialchars($u["user_name"] ?? "") ?><br>
        <?php endif; ?>
        Email: <?= htmlspecialchars($u["email"] ?? "") ?>
      </p>
      <span class="badge <?= (($u["type"] ?? "normal")==="admin"?"admin":"normal") ?>"><?= htmlspecialchars($u["type"] ?? "normal") ?></span>
      <div class="actions">
        <button class="btn" type="button"
          onclick="openEdit(
            '<?= htmlspecialchars($u["idUser"],ENT_QUOTES) ?>',
            '<?= htmlspecialchars($u["name"] ?? "",ENT_QUOTES) ?>',
            '<?= htmlspecialchars($u["user_name"] ?? "",ENT_QUOTES) ?>',
            '<?= htmlspecialchars($u["email"] ?? "",ENT_QUOTES) ?>',
            '<?= htmlspecialchars($u["type"] ?? "normal",ENT_QUOTES) ?>'
          )">Editar</button>
        <a class="btn danger" href="?delete=<?= htmlspecialchars($u["idUser"]) ?>" onclick="return confirm('¿Eliminar este usuario?')">Eliminar</a>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<!-- Modal Crear -->
<div class="modal-backdrop" id="modalCrear">
  <div class="modal">
    <h2>➕ Crear usuario</h2>
    <form method="post" onsubmit="this.querySelector('button[type=submit]').disabled=true;">
      <input type="hidden" name="accion" value="crear">
      <input type="hidden" name="nonce" value="<?= htmlspecialchars($form_nonce) ?>">
      <label>Nombre*</label>
      <input type="text" name="name" required>
      <label>Usuario*</label>
      <input type="text" name="user_name" required>
      <label>Email</label>
      <input type="email" name="email">
      <label>Contraseña*</label>
      <input type="text" name="passwd" required>
      <div class="actions">
        <button type="button" class="btn" onclick="closeModal('modalCrear')">Cancelar</button>
        <button type="submit" class="btn primary">Crear</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Editar -->
<div class="modal-backdrop" id="modalEditar">
  <div class="modal">
    <h2>✏️ Editar usuario</h2>
    <form method="post" onsubmit="this.querySelector('button[type=submit]').disabled=true;">
      <input type="hidden" name="accion" value="editar">
      <input type="hidden" name="nonce" value="<?= htmlspecialchars($form_nonce) ?>">
      <input type="hidden" id="edit_id" name="idUser">
      <label>Nombre*</label>
      <input type="text" id="edit_name" name="name" required>
      <label>Usuario*</label>
      <input type="text" id="edit_user" name="user_name" required>
      <label>Email</label>
      <input type="email" id="edit_email" name="email">
      <label>Contraseña (vacío = no cambiar)</label>
      <input type="text" id="edit_passwd" name="passwd">
      <label>Tipo</label>
      <select id="edit_type" name="type">
        <option value="normal">normal</option>
        <option value="admin">admin</option>
      </select>
      <div class="actions">
        <button type="button" class="btn" onclick="closeModal('modalEditar')">Cancelar</button>
        <button type="submit" class="btn primary">Guardar cambios</button>
      </div>
    </form>
  </div>
</div>

<script>
function closeModal(id){ document.getElementById(id).style.display="none"; }
document.getElementById("btnAdd").onclick = () => {
  document.getElementById("modalCrear").style.display = "flex";
};
function openEdit(id,name,user,email,type){
  document.getElementById("edit_id").value   = id;
  document.getElementById("edit_name").value = name;
  document.getElementById("edit_user").value = user;
  document.getElementById("edit_email").value= email;
  document.getElementById("edit_type").value = type;
  document.getElementById("modalEditar").style.display="flex";
}
</script>
</body>
</html>
