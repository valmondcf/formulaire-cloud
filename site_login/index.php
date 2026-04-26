<?php
session_start();
require "../init-db/db.php";
if(isset($_SESSION['id'])) {
    $var = "Ravi de vous revoir, " . $_SESSION['name'] . " !";
} else {
    $var = "Inscrivez vous pour découvrir le site dans son entièreté!";
}

$req_topics = $pdo->prepare("
    SELECT t.titre, t.id, t.date_creation, u.name as auteur
    FROM topic t
    JOIN users u ON t.id_users = u.id
    ORDER BY t.date_creation DESC
    LIMIT 5
");
$req_topics->execute();
$derniers_topics = $req_topics->fetchAll();

$req_annonces = $pdo->prepare("
    SELECT a.titre, a.contenu, a.date_creation, u.name as auteur
    FROM annonces a
    JOIN users u ON a.id_users = u.id
    ORDER BY a.date_creation DESC
    LIMIT 3
");
$req_annonces->execute();
$annonces = $req_annonces->fetchAll();
?>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.rtl.min.css"
        integrity="sha384-CfCrinSRH2IR6a4e6fy2q6ioOX7O6Mtm1L9vRvFZ1trBncWmMePhzvafv7oIcWiW" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/accueil.css">
    <title>Accueil</title>
</head>
<body>
<?php require_once('./menu.php'); ?>

<div class="container">
    <div class="welcome-title">★ Bienvenue dans le Cirque Digital ★</div>
    <div class="welcome-sub"><?= htmlspecialchars($var) ?></div>
    <div class="row">
        <div class="col-12 col-md-6">
            <div class="section-box">
                <div class="section-title">📢 Annonces du Cirque</div>
                <?php if(empty($annonces)): ?>
                    <div class="no-content">Aucune annonce pour le moment.</div>
                <?php else: ?>
                    <?php foreach($annonces as $a): ?>
                        <div class="annonce-item">
                            <div class="annonce-titre"><?= htmlspecialchars($a['titre']) ?></div>
                            <div class="annonce-contenu"><?= htmlspecialchars($a['contenu']) ?></div>
                            <div class="annonce-meta">
                                Par <?= htmlspecialchars($a['auteur']) ?> —
                                <?= date('d/m/Y', strtotime($a['date_creation'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="section-box">
                <div class="section-title">💬 Derniers Topics</div>
                <?php if(empty($derniers_topics)): ?>
                    <div class="no-content">Aucun topic pour le moment.</div>
                <?php else: ?>
                    <?php foreach($derniers_topics as $t): ?>
                        <div class="topic-item">
                            <?php if(isset($_SESSION['id'])): ?>
                                <a href="/forum/topic.php?id=<?= $t['id'] ?>" class="topic-titre">
                                    <?= htmlspecialchars($t['titre']) ?>
                                </a>
                            <?php else: ?>
                                <span class="topic-titre" style="cursor: not-allowed;">
                                    <?= htmlspecialchars($t['titre']) ?>
                                </span>
                            <?php endif; ?>
                            <div class="topic-meta">
                                <?= htmlspecialchars($t['auteur']) ?><br>
                                <?= date('d/m/Y', strtotime($t['date_creation'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="door-nav">
            <?php if(isset($_SESSION['id'])): ?>
                <a href="/personnages/personnages.php" class="door-nav-item">
                    <img src="/media/door_personnage.png" alt="Personnages">
                    <div class="door-nav-label">Personnages</div>
                    <span class="btn-voir">Découvrir </span>
                </a>
                <a href="/forum/forum.php" class="door-nav-item">
                    <img src="/media/door_forum.png" alt="Forum">
                    <div class="door-nav-label">Forum</div>
                    <span class="btn-voir">Découvrir </span>
                </a>
                <a href="/profil/profil.php" class="door-nav-item">
                    <img src="/media/door_profil.png" alt="Mon Profil">
                    <div class="door-nav-label">Mon Profil</div>
                    <span class="btn-voir">Découvrir </span>
                </a>
            <?php else: ?>
                <div class="door-nav-item" style="cursor: not-allowed;">
                    <img src="/media/door_personnage.png" alt="Personnages">
                    <div class="door-nav-label">Personnages</div>
                    <span class="btn-voir">Découvrir </span>
                </div>
                <div class="door-nav-item" style="cursor: not-allowed;">
                    <img src="/media/door_forum.png" alt="Forum">
                    <div class="door-nav-label">Forum</div>
                    <span class="btn-voir">Découvrir </span>
                </div>
                <div class="door-nav-item" style="cursor: not-allowed;">
                    <img src="/media/door_profil.png" alt="Mon Profil">
                    <div class="door-nav-label">Mon Profil</div>
                    <span class="btn-voir">Découvrir </span>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<script src="/caine/caine.js"></script>
</body>
</html>