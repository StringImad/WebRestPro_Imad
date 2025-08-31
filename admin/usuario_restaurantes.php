<?php
session_name("22_23");
session_start();

require_once __DIR__."/../src/funciones_ctes.php";
if (!isset($_SESSION["user_name"])) { header("Location: ../index.php"); exit; }
require_once __DIR__."/../src/seguridad.php";
if ($datos_usu_log->type !== "admin") { header("Location: ../principal.php"); exit; }

/* Normalizar token */
$api_token = "";
if (isset($_SESSION["api_session"])) {
    $api_token = is_array($_SESSION["api_session"])
        ? ($_SESSION["api_session"]["api_session"] ?? $_SESSION["api_session"]["token"] ?? "")
        : (string)$_SESSION["api_session"];
}

/* Parámetros */
$idUser = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$nombre = isset($_GET['name']) ? $_GET['name'] : "";

/* Llamada API */
$restaurantes = [];
$msg = "";
if ($api_token !== "" && $idUser > 0) {
    $url = DIR_SERV."/usuarios/".$idUser."/restaurantes?api_session=".urlencode($api_token);
    $raw = consumir_servicios_REST($url,"GET",null);
    $obj = json_decode($raw, true);

    if (!$obj) {
        $msg = "⚠️ Respuesta no válida de la API";
    } elseif (isset($obj['mensaje_error'])) {
        $msg = "⚠️ ".$obj['mensaje_error'];
    } elseif (isset($obj['no_login'])) {
        $msg = "⚠️ ".$obj['no_login'];
    } elseif (isset($obj['paginas_usuario']) && is_array($obj['paginas_usuario'])) {
        $restaurantes = $obj['paginas_usuario'];
    } elseif (isset($obj['mensaje'])) {
        $msg = "ℹ️ ".$obj['mensaje'];
    } else {
        $msg = "ℹ️ Sin resultados";
    }
} else {
    $msg = "⚠️ Sesión API ausente o id de usuario no válido.";
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Restaurantes de <?= htmlspecialchars($nombre ?: ("usuario #".$idUser)) ?></title>
<link rel="stylesheet" href="../styles/normalize.css">
<style>
  body{font-family:sans-serif;padding:20px;background:#f9fafb;}
  header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;}
  h1{margin:0;}
  .hint{font-size:.9rem;color:#6b7280;margin:.5rem 0 1rem;}
  .btn{padding:6px 10px;border-radius:6px;border:1px solid #ccc;background:#fff;cursor:pointer;text-decoration:none;color:#111;font-size:.9rem;}
  .btn.danger{background:#ef4444;border-color:#ef4444;color:#fff;}
  .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px;}
  .card{background:#fff;padding:16px;border-radius:10px;box-shadow:0 2px 6px rgba(0,0,0,.05);}
  .muted{color:#6b7280;font-size:.9rem;margin:2px 0 8px;}
  .k{display:inline-block;min-width:110px;color:#6b7280;}
</style>
</head>
<body>
<header>
  <h1>🍽️ Restaurantes de <?= htmlspecialchars($nombre ?: ("usuario #".$idUser)) ?></h1>
  <div>
    <a class="btn" href="gest_usuarios.php">⬅️ Volver a usuarios</a>
    <a class="btn danger" href="../principal.php?salir=1">Cerrar sesión</a>
  </div>
</header>

<?php if ($msg): ?><p class="hint"><?= htmlspecialchars($msg) ?></p><?php endif; ?>

<?php if ($restaurantes): ?>
<div class="grid">
  <?php foreach ($restaurantes as $r): 
      // Campos posibles (adaptable)
      $titulo = $r['name'] ?? $r['restaurant_name'] ?? ("Restaurante #".($r['id_restaurant'] ?? ($r['id'] ?? "")));
  ?>
    <div class="card">
<h3>
  <a href="restaurante_detalle.php?id=<?= htmlspecialchars($r['id_restaurant'] ?? $r['id'] ?? 0) ?>"
     style="color:inherit;text-decoration:none;">
    <?= htmlspecialchars($titulo) ?>
  </a>
</h3>
      <p class="muted">
        <?php if (!empty($r['address'])): ?>
          <span class="k">Dirección:</span> <?= htmlspecialchars($r['address']) ?><br>
        <?php endif; ?>
        <?php if (!empty($r['city'])): ?>
          <span class="k">Ciudad:</span> <?= htmlspecialchars($r['city']) ?><br>
        <?php endif; ?>
        <?php if (!empty($r['phone'])): ?>
          <span class="k">Teléfono:</span> <?= htmlspecialchars($r['phone']) ?><br>
        <?php endif; ?>
        <?php if (!empty($r['id_restaurant'])): ?>
          <span class="k">ID:</span> <?= htmlspecialchars($r['id_restaurant']) ?>
        <?php endif; ?>
      </p>
      <!-- Aquí podrías añadir botones "Ver carta", "Editar", etc. -->
    </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

</body>
</html>
