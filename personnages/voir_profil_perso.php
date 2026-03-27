<?php
session_start();
require "../init-db/db.php";

// Récupérer et valider l'id
$get_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($get_id <= 0) {
    header('Location: /personnages/personnages.php');
    exit;
}

// Infos principales (table characters)
$req = $pdo->prepare("SELECT * FROM characters WHERE id = ?");
$req->execute([$get_id]);
$perso = $req->fetch();

if (!$perso) {
    header('Location: /personnages/personnages.php');
    exit;
}

// Détails supplémentaires (table character_details)
$req2 = $pdo->prepare("SELECT * FROM character_details WHERE character_id = ?");
$req2->execute([$get_id]);
$details = $req2->fetch();

// Personnage précédent / suivant pour navigation
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
<style>
  body { color: #fff8f0; }

  .perso-header {
    background: linear-gradient(135deg, rgba(106,13,173,0.4), rgba(10,0,26,0.9));
    border: 2px solid #6a0dad;
    border-radius: 16px;
    padding: 28px;
    margin-bottom: 24px;
  }
  .perso-avatar {
    width: 120px; height: 120px;
    border-radius: 50%; object-fit: cover;
    border: 3px solid #ffe600;
    box-shadow: 0 0 20px #ffe60050;
  }
  .perso-nom {
    font-size: 32px; font-weight: bold;
    color: #ffe600;
    text-shadow: 0 0 16px #ffe60060;
    margin-bottom: 4px;
  }
  .perso-espece {
    color: #00e5cc; font-size: 15px; letter-spacing: 2px; margin-bottom: 6px;
  }
  .badge-status {
    display: inline-block;
    background: #ffe600; color: #0a0010;
    border-radius: 20px; padding: 3px 12px;
    font-size: 11px; font-weight: bold; letter-spacing: 1px;
  }

  .info-block {
    background: rgba(15,0,35,0.85);
    border: 1px solid #6a0dad50;
    border-radius: 12px; padding: 20px;
    margin-bottom: 16px;
  }
  .info-block h5 {
    color: #ff2d78; font-size: 13px;
    letter-spacing: 3px; text-transform: uppercase;
    margin-bottom: 12px;
    border-bottom: 1px solid #6a0dad40;
    padding-bottom: 8px;
  }
  .info-row {
    display: flex; gap: 10px;
    margin-bottom: 8px; font-size: 14px;
    line-height: 1.55;
  }
  .info-label {
    color: #ffe600; font-weight: bold;
    min-width: 130px; flex-shrink: 0;
    font-size: 12px; text-transform: uppercase; letter-spacing: 1px;
    padding-top: 1px;
  }
  .info-value { color: #fff8f0; }

  .quote-block {
    background: linear-gradient(135deg, rgba(106,13,173,0.2), rgba(255,45,120,0.1));
    border-left: 4px solid #ffe600;
    border-radius: 0 10px 10px 0;
    padding: 16px 20px; margin-bottom: 16px;
    font-style: italic; font-size: 16px;
    color: #ffe600;
  }
  .quote-block::before { content: '"'; font-size: 36px; line-height: 0; vertical-align: -12px; margin-right: 4px; opacity: 0.5; }
  .quote-block::after  { content: '"'; font-size: 36px; line-height: 0; vertical-align: -12px; margin-left:  4px; opacity: 0.5; }

  .theory-block {
    background: rgba(0,229,204,0.07);
    border: 1px solid #00e5cc40;
    border-radius: 10px; padding: 14px 18px;
    font-size: 13px; color: #c0f0ec; line-height: 1.6;
  }
  .theory-block::before { content: '🔍 Théorie : '; color: #00e5cc; font-weight: bold; }

  .nav-perso {
    display: flex; justify-content: space-between; align-items: center;
    margin-top: 28px; flex-wrap: wrap; gap: 10px;
  }
  .btn-nav {
    background: rgba(106,13,173,0.4); border: 1px solid #6a0dad;
    border-radius: 8px; padding: 8px 16px; color: #fff8f0;
    text-decoration: none; font-size: 13px; transition: all 0.2s;
  }
  .btn-nav:hover { background: #6a0dad; color: white; text-decoration: none; }
  .btn-retour {
    background: linear-gradient(135deg, #6a0dad, #ff2d78);
    border: none; border-radius: 8px; padding: 9px 20px;
    color: white; text-decoration: none; font-size: 13px;
    transition: opacity 0.2s;
  }
  .btn-retour:hover { opacity: 0.85; color: white; text-decoration: none; }
</style>
</head>
<body>
<?php require_once('../site_login/menu.php'); ?>

<div class="container mt-4 mb-5">

  <!-- Header personnage -->
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

    <!-- Colonne gauche -->
    <div class="col-12 col-md-6">

      <!-- Infos de base -->
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
      <!-- Personnalité -->
      <div class="info-block">
        <h5>✦ Personnalité</h5>
        <div class="info-value" style="font-size:14px;line-height:1.65">
          <?= htmlspecialchars($details['personality']) ?>
        </div>
      </div>
      <?php endif; ?>

      <?php if ($details && !empty($details['abilities'])): ?>
      <!-- Capacités -->
      <div class="info-block">
        <h5>✦ Capacités</h5>
        <div class="info-value" style="font-size:14px;line-height:1.65">
          <?= htmlspecialchars($details['abilities']) ?>
        </div>
      </div>
      <?php endif; ?>

    </div>

    <!-- Colonne droite -->
    <div class="col-12 col-md-6">

      <?php if ($details && !empty($details['quote'])): ?>
      <!-- Citation -->
      <div class="quote-block">
        <?= htmlspecialchars($details['quote']) ?>
      </div>
      <?php endif; ?>

      <?php if ($details && !empty($details['relationships'])): ?>
      <!-- Relations -->
      <div class="info-block">
        <h5>✦ Relations</h5>
        <div class="info-value" style="font-size:14px;line-height:1.65">
          <?= htmlspecialchars($details['relationships']) ?>
        </div>
      </div>
      <?php endif; ?>

      <?php if ($details && !empty($details['trivia'])): ?>
      <!-- Anecdotes -->
      <div class="info-block">
        <h5>✦ Le saviez-vous ?</h5>
        <div class="info-value" style="font-size:14px;line-height:1.65">
          <?= htmlspecialchars($details['trivia']) ?>
        </div>
      </div>
      <?php endif; ?>

      <?php if ($details && !empty($details['theories'])): ?>
      <!-- Théories -->
      <div class="theory-block">
        <?= htmlspecialchars($details['theories']) ?>
      </div>
      <?php endif; ?>

    </div>
  </div>

  <!-- Navigation -->
  <div class="nav-perso">
    <?php if ($prev): ?>
      <a href="/personnages/voir_personnage.php?id=<?= $prev['id'] ?>" class="btn-nav">
        ← <?= htmlspecialchars($prev['name']) ?>
      </a>
    <?php else: ?>
      <span></span>
    <?php endif; ?>

    <a href="/personnages/personnages.php" class="btn-retour">★ Tous les personnages</a>

    <?php if ($next): ?>
      <a href="/personnages/voir_personnage.php?id=<?= $next['id'] ?>" class="btn-nav">
        <?= htmlspecialchars($next['name']) ?> →
      </a>
    <?php else: ?>
      <span></span>
    <?php endif; ?>
  </div>

</div>

<script src="/cain.js"></script>
</body>
</html>