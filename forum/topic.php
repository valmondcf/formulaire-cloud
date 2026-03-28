<?php
    session_start();
    require "../init-db/db.php"; 

    if(!isset($_GET['id'])){
        header('Location: /forum/forum.php');
        exit;
    }

    $get_id_topic = (int) $_GET['id'];

    if($get_id_topic <= 0){
        header('Location: /forum/forum.php');
        exit;
    }

    $requete = $pdo->prepare("SELECT t.*, u.name, f.titre AS titre_forum FROM topic t INNER JOIN users u ON u.id = t.id_users INNER JOIN forum f ON f.id = t.id_forum WHERE t.id = ? ORDER BY t.date_creation DESC");
    $requete->execute([$get_id_topic]);
    $req_topic = $requete->fetch();

?>

    <html>
    <head>
        <?php
            require_once('../head/link.php');
        ?>
        <title><?= $req_topic['titre'] ?></title>
    </head>
    <body>
        <?php
            require_once('../site_login/menu.php');
        ?>
        <div class="container">
            <div class="row">
                <div class="col-3"></div>
                <div class="col-6">
                    <h1><?= $req_topic['titre'] ?></h1>
                </div>
                <div class="col-3"></div>
                <div class="col-3"></div>
                <div class="col-6">
                    <div><?= nl2br($req_topic['contenu']) ?></div>
                    </br>
                    <div>Écrit par <?= $req_topic['name'] ?>
                    <div>Catégorie : <?= $req_topic['titre_forum'] ?></div>
                    <div>Le <?= date_format(date_create($req_topic['date_creation']), "d/m/Y à H:i") ?></div>
                </div>
                <div class="col-3"></div>
            </div>
        </div>
        <script src="/cain.js"></script>
        <script src="/caine_tadc__1_.glb"></script>
    </body>
</html>