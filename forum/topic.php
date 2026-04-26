<?php
session_start();
require "../init-db/db.php"; 
require "../init-db/auth.php";

if(!isset($_GET['id'])){
    header('Location: /forum/forum.php');
    exit;
}

$get_id_topic = (int) $_GET['id'];

if($get_id_topic <= 0){
    header('Location: /forum/forum.php');
    exit;
}

$requete = $pdo->prepare("SELECT t.*, u.name, u.avatar, f.titre AS titre_forum 
    FROM topic t 
    INNER JOIN users u ON u.id = t.id_users 
    INNER JOIN forum f ON f.id = t.id_forum 
    WHERE t.id = ?");
$requete->execute([$get_id_topic]);
$req_topic = $requete->fetch();

if(!isset($req_topic['id'])){
    header('Location: /forum/forum.php');
    exit;
}

$req = $pdo->prepare("SELECT tc.*, u.name, u.avatar 
    FROM topic_commentaire tc 
    INNER JOIN users u ON u.id = tc.id_users 
    WHERE tc.id_topic = ? 
    ORDER BY tc.date_creation DESC");
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
        } elseif(mb_strlen($commentaire) < 3){
            $valid = false;
            $err_commentaire = "Le commentaire doit faire plus de 3 caractères";
        }

        if($valid && isset($_SESSION['id'])){
            $date_creation = date('Y-m-d H:i:s');
            $req = $pdo->prepare("INSERT INTO topic_commentaire (id_topic, id_users, contenu, date_creation, date_modification) VALUES (?,?,?,?,?)");
            $req->execute([$req_topic['id'], $_SESSION['id'], $commentaire, $date_creation, $date_creation]);
            header('Location: topic.php?id=' . $req_topic['id']);
            exit;
        }

    } elseif(isset($_POST['supp_com'])){
        $id_com = (int) $id_com;

        if($id_com <= 0){
            $valid = false;
            $err_commentaire = "Impossible de supprimer ce commentaire";
        } else {
            $req = $pdo->prepare("SELECT id FROM topic_commentaire WHERE id = ? AND id_users = ?");
            $req->execute([$id_com, $_SESSION['id']]);
            $req_verif_com = $req->fetch();

            if(!isset($req_verif_com['id'])){
                $valid = false;
                $err_commentaire = "Impossible de supprimer ce commentaire";
            }
        }

        if($valid && isset($_SESSION['id'])){
            $req = $pdo->prepare("DELETE FROM topic_commentaire WHERE id = ?");
            $req->execute([$req_verif_com['id']]);
            header('Location: topic.php?id=' . $req_topic['id']);
            exit;
        }

    } elseif(isset($_POST['supp_topic'])){
        if($_SESSION['id'] <> $req_topic['id_users']){
            $valid = false;
            $err_topic = "Impossible de supprimer ce topic";
        }

        if($valid && isset($_SESSION['id'])){
            $req = $pdo->prepare("DELETE FROM topic_commentaire WHERE id_topic = ?");
            $req->execute([$req_topic['id']]);
            $req = $pdo->prepare("DELETE FROM topic WHERE id = ?");
            $req->execute([$req_topic['id']]);
            header('Location: /forum/forum.php');
            exit;
        }
    }
}

function getAvatar($id_user, $avatar) {
    if(!empty($avatar)){
        return '/public/pp/' . $id_user . '/' . $avatar;
    }
    return '/public/pp/defaut/defaut.png';
}
?>

<html>
<head>
    <?php require_once('../head/link.php'); ?>
    <title><?= htmlspecialchars($req_topic['titre']) ?></title>
    <link rel="stylesheet" href="/css/forum.css">
</head>
<body>
    <?php require_once('../site_login/menu.php'); ?>
    <div class="container">
        <div class="row">

            <div class="col-3"></div>
            <div class="col-6">
                <h1><?= htmlspecialchars($req_topic['titre']) ?></h1>
            </div>
            <div class="col-3"></div>

            <div class="col-3"></div>
            <div class="col-6">

                <?php if(isset($err_topic)): ?>
                    <div class="err"><?= $err_topic ?></div>
                <?php endif; ?>

                <?php if(isset($_SESSION['id']) && $_SESSION['id'] == $req_topic['id_users']): ?>
                <div class="topic-actions">
                    <form method="post">
                        <button type="submit" name="supp_topic" class="btn-tadc btn-danger">Supprimer le topic</button>
                    </form>
                    <a href="/forum/editer_topic.php?id=<?= $req_topic['id'] ?>" class="btn-tadc">Éditer le topic</a>
                </div>
                <?php endif; ?>

                <div class="topic-bloc">
                    <div class="topic-contenu"><?= nl2br(htmlspecialchars($req_topic['contenu'])) ?></div>
                    <div class="topic-meta">
                        <img src="<?= getAvatar($req_topic['id_users'], $req_topic['avatar']) ?>" class="meta-avatar" alt="avatar">
                        <div>
                            <span class="meta-auteur">Écrit par <?= htmlspecialchars($req_topic['name']) ?></span>
                            <span class="meta-info">Catégorie : <?= htmlspecialchars($req_topic['titre_forum']) ?></span>
                            <span class="meta-info">Le <?= date_format(date_create($req_topic['date_creation']), "d/m/Y à H:i") ?></span>
                            <?php if($req_topic['date_creation'] < $req_topic['date_modification']): ?>
                                <span class="meta-info">Modifié le <?= date_format(date_create($req_topic['date_modification']), "d/m/Y à H:i") ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-3"></div>

            <div class="col-3"></div>
            <div class="col-6">
                <h1>Commentaires</h1>

                <div class="form-commentaire">
                    <form method="post">
                        <div class="mb-3">
                            <?php if(isset($err_commentaire)): ?>
                                <div class="err"><?= $err_commentaire ?></div>
                            <?php endif; ?>
                            <label>Votre commentaire</label>
                            <textarea name="commentaire" placeholder="Votre commentaire..."><?php if(isset($commentaire)){ echo htmlspecialchars($commentaire); } ?></textarea>
                        </div>
                        <div class="mb-3">
                            <button type="submit" name="poster" class="btn-tadc">Poster</button>
                        </div>
                    </form>
                </div>

                <?php foreach($req_topic_commentaire as $rtc): ?>
                <div class="topic-bloc">
                    <div class="topic-contenu"><?= nl2br(htmlspecialchars($rtc['contenu'])) ?></div>
                    <div class="topic-meta">
                        <img src="<?= getAvatar($rtc['id_users'], $rtc['avatar']) ?>" class="meta-avatar" alt="avatar">
                        <div>
                            <span class="meta-auteur">Écrit par <?= htmlspecialchars($rtc['name']) ?></span>
                            <span class="meta-info">Le <?= date_format(date_create($rtc['date_creation']), "d/m/Y à H:i") ?></span>
                            <?php if($rtc['date_creation'] < $rtc['date_modification']): ?>
                                <span class="meta-info">Modifié le <?= date_format(date_create($rtc['date_modification']), "d/m/Y à H:i") ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if(isset($_SESSION['id']) && $_SESSION['id'] == $rtc['id_users']): ?>
                    <div class="topic-actions" style="margin-top: 14px;">
                        <form method="post">
                            <button type="submit" name="supp_com" class="btn-tadc btn-danger">Supprimer</button>
                            <input type="hidden" name="id_com" value="<?= $rtc['id'] ?>" />
                        </form>
                        <a href="/forum/editer_commentaire.php?id=<?= $rtc['id'] ?>" class="btn-tadc">Éditer</a>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>

            </div>
            <div class="col-3"></div>

        </div>
    </div>
    <script src="/caine/caine.js"></script>
</body>
</html>