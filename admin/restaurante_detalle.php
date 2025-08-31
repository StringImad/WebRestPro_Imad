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
$idRest = isset($_GET['id']) ? (int)$_GET['id'] : 0;

/* Llamada API */
$restaurante = null;
$platos = [];
$msg = "";

if ($api_token !== "" && $idRest > 0) {
    $url = DIR_SERV."/restaurantes/".$idRest."/detalle?api_session=".urlencode($api_token);
    $raw = consumir_servicios_REST($url, "GET", null);
    $obj = json_decode($raw, true);

    if (!$obj) {
        $msg = "⚠️ Respuesta no válida de la API";
    } elseif (isset($obj['mensaje_error'])) {
        $msg = "⚠️ ".$obj['mensaje_error'];
    } elseif (isset($obj['no_login'])) {
        $msg = "⚠️ ".$obj['no_login'];
    } else {
        $restaurante = $obj['restaurante'] ?? null;
        $platos      = $obj['platos'] ?? [];
        if (!$restaurante && !$platos) $msg = "ℹ️ No hay datos para este restaurante.";
    }
} else {
    $msg = "⚠️ Parámetros inválidos o sesión API ausente.";
}

/* Agrupar platos por tipo (type_food) */
$platos_por_tipo = [];
foreach ($platos as $p) {
    $tipo = $p['type_food'] ?? 'Sin categoría';
    $platos_por_tipo[$tipo][] = $p;
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title><?= $restaurante ? htmlspecialchars($restaurante['name'] ?? ('Restaurante #'.$idRest)) : 'Restaurante' ?></title>
<link rel="stylesheet" href="../styles/normalize.css">
<style>
  body{font-family:sans-serif;padding:20px;background:#f9fafb;}
  header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;}
  h1{margin:0;}
  .btn{padding:6px 10px;border-radius:6px;border:1px solid #ccc;background:#fff;cursor:pointer;text-decoration:none;color:#111;font-size:.9rem;}
  .btn.primary{background:#2563eb;color:#fff;border-color:#2563eb;}
  .btn.danger{background:#ef4444;color:#fff;border-color:#ef4444;}
  .hint{font-size:.9rem;color:#6b7280;margin:.5rem 0 1rem;}
  .card{background:#fff;padding:16px;border-radius:10px;box-shadow:0 2px 6px rgba(0,0,0,.05);margin-bottom:16px;}
  .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px;}
  .pill{display:inline-block;border:1px solid #ddd;border-radius:999px;padding:2px 8px;font-size:.8rem;margin-right:6px;color:#374151;}
  .price{font-weight:600;}
  .muted{color:#6b7280;}
</style>
</head>
<body>
<header>
  <h1>🍽️ <?= $restaurante ? htmlspecialchars($restaurante['name'] ?? ('Restaurante #'.$idRest)) : 'Restaurante' ?></h1>
  <div>
    <a class="btn" href="gest_usuarios.php">⬅️ Volver</a>
    <a class="btn danger" href="../principal.php?salir=1">Cerrar sesión</a>
  </div>
</header>

<?php if ($msg): ?><p class="hint"><?= htmlspecialchars($msg) ?></p><?php endif; ?>

<?php if ($restaurante): ?>
<div class="card">
  <h3>Información</h3>
  <p class="muted">
    <?php if (!empty($restaurante['address'])): ?>
      📍 <?= htmlspecialchars($restaurante['address']) ?><br>
    <?php endif; ?>
    <?php if (!empty($restaurante['city'])): ?>
      🏙️ <?= htmlspecialchars($restaurante['city']) ?><br>
    <?php endif; ?>
    <?php if (!empty($restaurante['phone'])): ?>
      ☎️ <?= htmlspecialchars($restaurante['phone']) ?><br>
    <?php endif; ?>
    ID: <?= htmlspecialchars($restaurante['id_restaurant'] ?? $idRest) ?>
  </p>
</div>
<?php endif; ?>

<?php if ($platos): ?>
  <?php foreach ($platos_por_tipo as $tipo=>$lista): ?>
    <div class="card">
      <h3><?= htmlspecialchars($tipo) ?></h3>
      <div class="grid">
        <?php foreach ($lista as $p): ?>
          <div class="card">
            <h4><?= htmlspecialchars($p['plate_name'] ?? 'Plato') ?></h4>
            <?php if (!empty($p['descrip'])): ?>
              <p class="muted"><?= htmlspecialchars($p['descrip']) ?></p>
            <?php endif; ?>
            <p>
              <?php if (isset($p['half_price']) && $p['half_price']!==""): ?>
                <span class="pill">1/2: <span class="price"><?= htmlspecialchars($p['half_price']) ?></span></span>
              <?php endif; ?>
              <?php if (isset($p['price']) && $p['price']!==""): ?>
                <span class="pill">Precio: <span class="price"><?= htmlspecialchars($p['price']) ?></span></span>
              <?php endif; ?>
              <?php if (!empty($p['allergen'])): ?>
                <span class="pill">Alérgenos: <?= htmlspecialchars($p['allergen']) ?></span>
              <?php endif; ?>
            </p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>
<?php else: ?>
  <p class="hint">Este restaurante no tiene platos registrados.</p>
<?php endif; ?>

</body>
</html>
