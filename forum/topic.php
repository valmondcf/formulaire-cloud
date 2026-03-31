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

    if(!isset($req_topic['id'])){
        header('Location: /forum/forum.php');
        exit;
    }

    $req = $pdo->prepare("SELECT tc.*, u.name FROM topic_commentaire tc INNER JOIN users u ON u.id = tc.id_users WHERE tc.id_topic = ? ORDER BY tc.date_creation DESC");
    $req->execute([$req_topic['id']]);
    $req_topic_commentaire = $req->fetchAll();

    if(!empty($_POST)){
    extract($_POST);

    $valid = true;

    if(isset($_POST['poster'])){

        $commentaire = (String) trim($commentaire);

        if(empty($commentaire)){
        $valid = false;
        $err_commentaire = "Ce champ ne peut pas être vide";
    
        }elseif(mb_strlen($commentaire) < 3){
            $valid = false;
            $err_commentaire = "Le titre doit faire plus de 5 caractères";
        }

        if($valid){

            $date_creation = date('Y-m-d H:i:s');
            $req = $pdo->prepare("INSERT INTO topic_commentaire (id_topic, id_users, contenu, date_creation, date_modification) VALUES (?,?,?,?,?)");
            $req->execute([$req_topic['id'], $_SESSION['id'], $commentaire, $date_creation, $date_creation]);

            header('Location: topic.php?id=' . $req_topic['id']);
            exit;
        }
    }
    }

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
                    <div>
                        <a href="/forum/editer_topic.php?id=<?= $req_topic['id']?>">Éditer mon topic</a>
                    </div>
                    </br>
                    </br>
                    <div><?= nl2br($req_topic['contenu']) ?></div>
                    </br>
                    <div>Écrit par <?= $req_topic['name'] ?>
                    <div>Catégorie : <?= $req_topic['titre_forum'] ?></div>
                    <div>Le <?= date_format(date_create($req_topic['date_creation']), "d/m/Y à H:i") ?></div>
                    <?php
                        if($req_topic['date_creation'] < $req_topic['date_modification']){
                    ?>
                    <div>Modifié le <?= date_format(date_create($req_topic['date_modification']), "d/m/Y à H:i") ?></div>
                    <?php
                        }
                    ?>
                </div>
                <div class="col-3"></div>
                <div class="col-3"></div>
                <div class="col-6"></div>
                    </br>
                    <h1>Commentaires</h1>
                <div class="col-3"></div>
                <div class="col-3"></div>
                <div class="col-6">
                    <br>
                    <form method="post">
                        <div class="mb-3">
                            <?php if(isset($err_commentaire)){ echo '<div>' . $err_commentaire . '</div>'; }?>
                            <label>Votre commentaire</label>
                            <textarea type="text" name="commentaire" placeholder="Votre commentaire..."><?php if(isset($commentaire)){ echo $commentaire; }?></textarea>
                        </div>
                        <div class="mb-3">
                            <button type="submit" name="poster" class="btn btn-primary">Poster</button>
                        </div>
                    </form>
                </div>
                <div class="col-3"></div>
                <?php
                    foreach($req_topic_commentaire as $rtc){
                ?>
                </br>
                <div class="col-3"></div>
                <div class="col-6">
                    </br>
                    <div><?= nl2br($rtc['contenu']) ?></div>
                    </br>
                    <div>Écrit par <?= $rtc['name'] ?>
                    <div>
                        <a href="/forum/editer_commentaire.php?id=<?=$rtc['id'] ?>">Éditer mon commentaire</a>
                    </div>
                    <div>Le <?= date_format(date_create($rtc['date_creation']), "d/m/Y à H:i") ?></div>
                    <?php
                        if($rtc['date_creation'] < $rtc['date_modification']){
                    ?>
                    <div>Modifié le <?= date_format(date_create($rtc['date_modification']), "d/m/Y à H:i") ?></div>
                    <?php
                        }
                    ?>
                </div>
                <div class="col-3"></div>
                <?php
                    }
                ?>
            </div>
        </div>
        <script src="/caine/caine.js"></script>
    </body>
</html>