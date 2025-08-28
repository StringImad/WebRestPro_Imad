<?php
// carta.php — Vista pública de la carta (solo lectura)
require_once __DIR__ . "/src/funciones_ctes.php"; // usamos db()

// Validar parámetro
$id_restaurant = isset($_GET["id_restaurant"]) ? (int)$_GET["id_restaurant"] : 0;
if ($id_restaurant <= 0) {
    http_response_code(400);
    echo "<h2>Petición inválida: falta id_restaurant</h2>";
    exit;
}

// Datos del restaurante
$st = db()->prepare("SELECT id, name, web, tlf FROM restaurant WHERE id = ?");
$st->execute([$id_restaurant]);
$rest = $st->fetch();

if (!$rest) {
    http_response_code(404);
    echo "<h2>Restaurante no encontrado</h2>";
    exit;
}

// Platos (ordenados por categoría y nombre)
$st = db()->prepare("
    SELECT plate_name, descrip, allergen, half_price, price, food_type
    FROM page_web_content
    WHERE id_restaurant = ?
    ORDER BY COALESCE(NULLIF(food_type,''),'Otros'), plate_name
");
$st->execute([$id_restaurant]);
$platos = $st->fetchAll();

// Agrupar por categoría
$por_categoria = [];
foreach ($platos as $p) {
    $cat = trim($p["food_type"] ?? "");
    if ($cat === "") $cat = "Otros";
    $por_categoria[$cat][] = $p;
}

// Helper de escape
function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Carta – <?= e($rest["name"]) ?></title>
<style>
  :root{
    --bg:#f6f7fb; --card:#ffffff; --muted:#6b7280; --ink:#0f172a;
    --brand:#2563eb; --line:#e5e7eb; --accent:#f3f4f6;
  }
  *{ box-sizing:border-box; }
  body{ margin:0; font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; background:var(--bg); color:var(--ink); }
  .wrap{ max-width:920px; margin:0 auto; padding:20px 14px 60px; }
  header{ display:flex; flex-wrap:wrap; gap:8px 12px; align-items:center; justify-content:space-between; margin-bottom:14px; }
  .brand{ font-weight:800; font-size:clamp(20px, 3.8vw, 32px); letter-spacing:.2px; }
  .meta{ color:var(--muted); font-size:.92rem; }
  .card{ background:var(--card); border:1px solid var(--line); border-radius:14px; overflow:hidden; }
  .cat{ padding:14px 16px; background:var(--accent); border-bottom:1px solid var(--line); font-weight:700; }
  .item{ display:grid; grid-template-columns: 1fr auto; gap:6px 12px; padding:14px 16px; border-bottom:1px solid var(--line); }
  .item:last-child{ border-bottom:none; }
  .name{ font-weight:600; }
  .desc{ grid-column:1 / -1; color:var(--muted); font-size:.95rem; }
  .badges{ display:flex; gap:6px; flex-wrap:wrap; align-items:center; }
  .badge{ border:1px dashed var(--line); border-radius:999px; padding:2px 8px; font-size:.8rem; color:#334155; background:#fafafa; }
  .price{ text-align:right; white-space:nowrap; align-self:center; font-weight:700; }
  .two-prices{ display:flex; gap:8px; }
  .chip{ font-weight:500; color:#111827; }
  .footer{ margin-top:18px; color:var(--muted); font-size:.9rem; text-align:center; }
  @media (prefers-color-scheme: dark){
    :root{ --bg:#0b1220; --card:#0f172a; --muted:#9aa4b2; --ink:#e5e7eb; --line:#1f2937; --accent:#0b1324; --brand:#3b82f6; }
    .badge{ background:#0b1324; color:#cbd5e1; border-color:#1f2937; }
  }
</style>
</head>
<body>
<div class="wrap">
  <header>
    <div class="brand"><?= e($rest["name"]) ?></div>
    <div class="meta">
      <?php if(!empty($rest["web"])): ?>
        🌐 <a href="<?= e($rest["web"]) ?>" target="_blank" rel="noopener" style="color:var(--brand); text-decoration:none"><?= e($rest["web"]) ?></a>
      <?php endif; ?>
      <?php if(!empty($rest["tlf"])): ?>
        &nbsp;&nbsp;📞 <?= e($rest["tlf"]) ?>
      <?php endif; ?>
    </div>
  </header>

  <?php if (empty($platos)): ?>
    <div class="card">
      <div class="cat">Carta</div>
      <div class="item"><div class="name">Sin platos por el momento</div></div>
    </div>
  <?php else: ?>
    <?php foreach ($por_categoria as $cat => $lista): ?>
      <div class="card" style="margin-bottom:16px;">
        <div class="cat"><?= e($cat) ?></div>
        <?php foreach ($lista as $p): ?>
          <div class="item">
            <div>
              <div class="name"><?= e($p["plate_name"]) ?></div>
              <?php if (!empty($p["descrip"])): ?>
                <div class="desc"><?= nl2br(e($p["descrip"])) ?></div>
              <?php endif; ?>
              <div class="badges">
                <?php if (!empty($p["allergen"]) && $p["allergen"] !== "-"): ?>
                  <span class="badge">Alérgenos: <?= e($p["allergen"]) ?></span>
                <?php endif; ?>
              </div>
            </div>
            <div class="price">
              <?php
                $half = trim((string)($p["half_price"] ?? ""));
                $full = trim((string)($p["price"] ?? ""));
                $hasHalf = $half !== "" && $half !== "--" && $half !== "-";
              ?>
              <?php if ($hasHalf): ?>
                <div class="two-prices">
                  <span class="chip">½ <?= e($half) ?>€</span>
                  <span class="chip"><?= e($full) ?>€</span>
                </div>
              <?php else: ?>
                <span class="chip"><?= e($full) ?>€</span>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <div class="footer">Generado con WebRestPro · <?= date('Y') ?></div>
</div>
</body>
</html>
