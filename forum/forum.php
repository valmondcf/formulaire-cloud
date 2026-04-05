<?php
session_start();
require "../init-db/db.php"; 
$requete = $pdo->prepare("SELECT * FROM forum ORDER BY ordre");
$requete->execute();
$req_forum = $requete->fetchAll();
?>
<html>
<head>
    <?php require_once('../head/link.php'); ?>
    <title>Forum</title>
    <link rel="stylesheet" href="/css/forum-index.css">
</head>
<body>
    <?php require_once('../site_login/menu.php'); ?>
    <div class="container">
        <div class="row">

            <div class="col-12">
                <h1>★ Forum du Cirque ★</h1>
            </div>

            <div style="width: 100%; max-width: 720px; text-align: right; margin-bottom: 8px;">
                <a href="/forum/create_topic.php" class="btn-tadc">+ Créer une topic</a>
            </div>

            <?php foreach($req_forum as $rf): ?>
            <div class="col-3-salon">
                <div class="salon-titre"><?= htmlspecialchars($rf['titre']) ?></div>
                <a href="/forum/liste-topics.php?id=<?= $rf['id'] ?>" class="btn-tadc">Voir les topics →</a>
            </div>
            <?php endforeach; ?>

        </div>
    </div>
    <script src="/caine/caine.js"></script>
</body>
</html>