<?php
session_start();
require "../init-db/db.php"; 

if(!isset($_GET['id'])){
    header('Location: /forum/forum.php');
    exit;
}

$get_id_forum = (int) $_GET['id'];

if($get_id_forum <= 0){
    header('Location: /forum/forum.php');
    exit;
}

$requete = $pdo->prepare("SELECT * FROM forum WHERE id = ?");
$requete->execute([$get_id_forum]);
$req_forum = $requete->fetch();

$requete = $pdo->prepare("SELECT t.*, u.name FROM topic t INNER JOIN users u ON u.id = t.id_users WHERE id_forum = ? ORDER BY date_creation DESC");
$requete->execute([$get_id_forum]);
$req_liste_topics = $requete->fetchAll();
?>
<html>
<head>
    <?php require_once('../head/link.php'); ?>
    <title>Forum - <?= htmlspecialchars($req_forum['titre']) ?></title>
    <link rel="stylesheet" href="/css/forum-index.css">
</head>
<body>
    <?php require_once('../site_login/menu.php'); ?>
    <div class="container">
        <div class="row">

            <div class="col-3"></div>
            <div class="col-6">
                <h1><?= htmlspecialchars($req_forum['titre']) ?></h1>
            </div>
            <div class="col-3"></div>

            <div style="width: 100%; max-width: 720px; text-align: right; margin-bottom: 8px;">
                <a href="/forum/create_topic.php" class="btn-tadc">+ Créer une topic</a>
            </div>

            <?php if(empty($req_liste_topics)): ?>
            <div style="width: 100%; max-width: 720px; text-align: center; color: #aaa; font-size: 15px; padding: 40px 0;">
                Aucun topic dans ce salon pour le moment.
            </div>
            <?php else: ?>
                <?php foreach($req_liste_topics as $rlt): ?>
                <div class="topic-item">
                    <div class="topic-item-titre"><?= htmlspecialchars($rlt['titre']) ?></div>
                    <a href="/forum/topic.php?id=<?= $rlt['id'] ?>" class="btn-tadc">Lire →</a>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </div>
    <script src="/caine/caine.js"></script>
</body>
</html>