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


    $requete = $pdo->prepare("SELECT * FROM topic WHERE id_forum = ? ORDER BY date_creation DESC");
    $requete->execute([$get_id_forum]);
    $req_liste_topics = $requete->fetchAll();

?>

    <html>
    <head>
        <?php
            require_once('../head/link.php');
        ?>
        <title>Forum - <?= $req_forum['titre'] ?></title>
    </head>
    <body>
        <?php
            require_once('../site_login/menu.php');
        ?>
        <div class="container">
            <div class="row">
                <div class="col-3"></div>
                <div class="col-6">
                    <h1><?= $req_forum['titre'] ?></h1>
                </div>
                <div class="col-3"></div>
                <?php
                    foreach($req_liste_topics as $rlt){
                ?> 
                <div class="col-3"></div>
                <div class="col-6">
                    <div><?= $rlt['titre'] ?></div>
                    <div>
                        <a href="/forum/topic.php?id=<?= $rlt['id']?>">Lire topic</a>
                    </div>
                </div>
                <div class="col-3"></div>
                <?php
                }
                ?>
            </div>
        </div>
        <script src="/cain.js"></script>
        <script src="/caine_tadc__1_.glb"></script>
    </body>
</html>