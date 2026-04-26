<?php
session_start();
require "../init-db/db.php";
require "../init-db/auth.php";

$requete = $pdo->prepare("SELECT id, name, door_url FROM characters ORDER BY id");
$requete->execute();
$personnages = $requete->fetchAll();
?>

<html lang="fr">
    <head>
        <?php require_once('../head/link.php'); ?>
        <title>Personnages du Cirque</title>
        <link rel="stylesheet" href="/css/personnages.css">
    </head>
    <body>
        <?php require_once('../site_login/menu.php'); ?>
        <div class="container mt-4">
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <h1 class="page-title">★ Les Résidents du Cirque ★</h1>
                    <p class="page-subtitle">LE CIRQUE NUMÉRIQUE</p>
                </div>
            </div>
            <div class="row g-4">
                <?php foreach ($personnages as $p): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="door-card">
                        <img src="<?= htmlspecialchars($p['door_url']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                        <div class="door-name"><?= htmlspecialchars($p['name']) ?></div>
                        <a href="/personnages/voir_profil_perso.php?id=<?= $p['id'] ?>" class="btn-voir">
                            Découvrir →
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <script src="/caine/caine.js"></script>
    </body>
</html>