<?php
session_start();
require "../init-db/db.php";
require "../init-db/auth.php";

$get_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($get_id <= 0) {
    header('Location: /personnages/personnages.php');
    exit;
}

$req = $pdo->prepare("SELECT * FROM characters WHERE id = ?");
$req->execute([$get_id]);
$perso = $req->fetch();

if (!$perso) {
    header('Location: /personnages/personnages.php');
    exit;
}

$req2 = $pdo->prepare("SELECT * FROM character_details WHERE character_id = ?");
$req2->execute([$get_id]);
$details = $req2->fetch();

$req_nav = $pdo->prepare("SELECT id, name FROM characters WHERE id != ? ORDER BY id");
$req_nav->execute([$get_id]);
$tous = $req_nav->fetchAll();
$prev = null; $next = null;
foreach ($tous as $i => $t) {
    if ($t['id'] < $get_id) $prev = $t;
    if ($t['id'] > $get_id && !$next) $next = $t;
}
?>
<html lang="fr">
<head>
<?php require_once('../head/link.php'); ?>
<title><?= htmlspecialchars($perso['name']) ?> — Le Cirque</title>
<link rel="stylesheet" href="/css/voir_perso.css">
</head>
<body>
<?php require_once('../site_login/menu.php'); ?>

<div class="container mt-4 mb-5">

  <div class="perso-header">
    <div class="d-flex align-items-center gap-4 flex-wrap">
      <?php if (!empty($perso['avatar_url'])): ?>
        <img src="<?= htmlspecialchars($perso['avatar_url']) ?>"
             alt="<?= htmlspecialchars($perso['name']) ?>"
             class="perso-avatar"
             onerror="this.style.display='none'">
      <?php endif; ?>
      <div>
        <div class="perso-nom"><?= htmlspecialchars($perso['name']) ?></div>
        <div class="perso-espece"><?= htmlspecialchars($perso['species']) ?></div>
        <span class="badge-status"><?= htmlspecialchars(strtoupper($perso['status'])) ?></span>
      </div>
    </div>
  </div>

  <div class="row g-3">

    <div class="col-12 col-md-6">
      <div class="info-block">
        <h5>✦ Informations</h5>
        <div class="info-row">
          <span class="info-label">Genre</span>
          <span class="info-value"><?= htmlspecialchars($perso['gender']) ?></span>
        </div>
        <div class="info-row">
          <span class="info-label">Première apparition</span>
          <span class="info-value"><?= htmlspecialchars($perso['first_appearance']) ?></span>
        </div>
        <div class="info-row">
          <span class="info-label">Doubleur</span>
          <span class="info-value">🎙 <?= htmlspecialchars($perso['voice_actor']) ?></span>
        </div>
        <?php if (!empty($perso['description'])): ?>
        <div class="info-row">
          <span class="info-label">Description</span>
          <span class="info-value"><?= htmlspecialchars($perso['description']) ?></span>
        </div>
        <?php endif; ?>
      </div>

      <?php if ($details && !empty($details['personality'])): ?>
      <div class="info-block">
        <h5>✦ Personnalité</h5>
        <div class="info-value" style="font-size:14px;line-height:1.65">
          <?= htmlspecialchars($details['personality']) ?>
        </div>
      </div>
      <?php endif; ?>

      <?php if ($details && !empty($details['abilities'])): ?>
      <div class="info-block">
        <h5>✦ Capacités</h5>
        <div class="info-value" style="font-size:14px;line-height:1.65">
          <?= htmlspecialchars($details['abilities']) ?>
        </div>
      </div>
      <?php endif; ?>

    </div>

    <div class="col-12 col-md-6">

      <?php if ($details && !empty($details['quote'])): ?>
      <div class="quote-block">
        <?= htmlspecialchars($details['quote']) ?>
      </div>
      <?php endif; ?>

      <?php if ($details && !empty($details['relationships'])): ?>
      <div class="info-block">
        <h5>✦ Relations</h5>
        <div class="info-value" style="font-size:14px;line-height:1.65">
          <?= htmlspecialchars($details['relationships']) ?>
        </div>
      </div>
      <?php endif; ?>

      <?php if ($details && !empty($details['trivia'])): ?>
      <div class="info-block">
        <h5>✦ Le saviez-vous ?</h5>
        <div class="info-value" style="font-size:14px;line-height:1.65">
          <?= htmlspecialchars($details['trivia']) ?>
        </div>
      </div>
      <?php endif; ?>

      <?php if ($details && !empty($details['theories'])): ?>
      <div class="theory-block">
        <?= htmlspecialchars($details['theories']) ?>
      </div>
      <?php endif; ?>

    </div>
  </div>

  <div class="nav-perso">
    <?php if ($prev): ?>
      <a href="/personnages/voir_profil_perso.php?id=<?= $prev['id'] ?>" class="btn-nav">
        ← <?= htmlspecialchars($prev['name']) ?>
      </a>
    <?php else: ?>
      <span></span>
    <?php endif; ?>

    <a href="/personnages/personnages.php" class="btn-retour">★ Tous les personnages</a>

    <?php if ($next): ?>
      <a href="/personnages/voir_profil_perso.php?id=<?= $next['id'] ?>" class="btn-nav">
        <?= htmlspecialchars($next['name']) ?> →
      </a>
    <?php else: ?>
      <span></span>
    <?php endif; ?>
  </div>

</div>

  <script src="/caine/caine.js"></script>
</body>
</html>