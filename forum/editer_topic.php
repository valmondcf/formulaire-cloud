<?php
session_start();
require "../init-db/db.php"; 

if(!isset($_SESSION['id'])){
    header('Location: /');
    exit;
}

if(!isset($_GET['id'])){
    header('Location: /forum/forum.php');
    exit;
}

$get_id_topic = (int) $_GET['id'];

if($get_id_topic <= 0){
    header('Location: /forum/forum.php');
    exit;
}

$req = $pdo->prepare("SELECT t.*, f.titre AS titre_forum FROM topic t INNER JOIN forum f ON f.id = t.id_forum WHERE t.id = ? ");
$req->execute([$get_id_topic]);
$req_topic = $req->fetch();

if(!isset($req_topic['id'])){
    header('Location: /forum/forum.php');
    exit;
}

$req = $pdo->prepare("SELECT id, titre FROM forum");
$req->execute();
$req_forum = $req->fetchAll();

if(!empty($_POST)){
    extract($_POST);

    $valid = true;

    if(isset($_POST['modification'])){

        $titre = (String) ucfirst(trim($titre));
        $categorie = (int) $categorie;
        $contenu= (String) trim($contenu);

        if(empty($titre)){
        $valid = false;
        $err_titre = "Le titre ne peut pas être vide";
    
        }elseif(mb_strlen($titre) < 3){
            $valid = false;
            $err_titre = "Le titre doit faire plus de 5 caractères";
        }elseif(mb_strlen($titre) > 50){
            $valid = false;
            $err_titre = "Le titre doit faire moins de 51 caractères (" . mb_strlen($titre) . "/50)";
        }

        $req = $pdo->prepare("SELECT id, titre
            FROM forum
            WHERE id = ?");
        $req->execute([$categorie]);
        $req_forum_verif = $req->fetch();

        if(!isset($req_forum_verif['id'])){
            $valid = false;
            $categorie = null;
            $err_cat = "Cette catégorie n'existe pas";
        }

        if(empty($contenu)){
        $valid = false;
        $err_contenu = "Le contenu ne peut pas être vide";
    
        }elseif(mb_strlen($contenu) < 3){
            $valid = false;
            $err_contenu = "Le contenu doit faire plus de 5 caractères";
        }

        if($valid){

            $date_modification = date('Y-m-d H:i:s');

            $req = $pdo->prepare("UPDATE topic SET id_forum = ?, titre = ?, contenu = ?, date_modification = ? WHERE id = ?");

            $req->execute([$req_forum_verif['id'], $titre, $contenu, $date_modification, $req_topic['id']]);

            header('Location: topic.php?id=' . $req_topic['id']);
            exit;
        }
    }
}


?>

    <html>
    <head>
        <link rel="stylesheet" href="./style_login.css">
        <?php
            require_once('../head/link.php');
        ?>
        <title>Éditer mon topic</title>
    </head>
    <body>
        <?php
            require_once('../site_login/menu.php');
        ?>
    <div class="box">
        <h1>Éditer mon topic</h1>
        <form method="post">
            <label>Titre</label>
            <br/>
            <?php if(isset($err_titre)){ echo '<div>' . $err_titre . '</div>'; }?>
            <input type="text" name="titre" value="<?php if(isset($titre)){ echo $titre; }else{ echo $req_topic['titre']; }?>" placeholder="Titre de votre topic"/>
            <br/>
            <br/>
            <?php if(isset($err_cat)){ echo '<div>' . $err_cat . '</div>'; }?>

            <label>Catégorie</label>
            <select name="categorie">
            <br/>
            <?php
                if(isset($categorie)){
            ?>
            <option value="<?= $req_forum_verif['id'] ?>"><?= $req_forum_verif['titre'] ?></option>
            <?php
                }elseif(isset($req_topic['id_forum'])){
            ?>
            <option value="<?= $req_topic['id_forum'] ?>"><?= $req_topic['titre_forum'] ?></option>
            <?php
                }else{
            ?>
            <option hidden>Choisir une catégorie</option>
            <?php
                }
            ?>
            <?php
                foreach($req_forum as $rf){
            ?>
            <option value="<?= $rf['id'] ?>"><?= $rf['titre']?></option>
            <?php
                }
            ?>
            </select>
            <br/>
            <br/>
            <?php if(isset($err_contenu)){ echo '<div>' . $err_contenu . '</div>'; }?>
            <label>Contenu</label>
            <textarea type="text" name="contenu" placeholder="Votre topic..."><?php if(isset($contenu)){ echo $contenu; } else{ echo $req_topic['contenu']; } ?></textarea>
            <br/>
            <br/>
            <button type="submit" name="modification">Modifier mon topic</button>
        </form>
    </div>
        <script src="/caine/caine.js"></script>
    </body>
</html>