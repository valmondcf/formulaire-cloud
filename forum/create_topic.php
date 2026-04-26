<?php
session_start();
require "../init-db/db.php";
require "../init-db/auth.php"; 

if(!isset($_SESSION['id'])){
    header('Location: /');
    exit;
}

$req = $pdo->prepare("SELECT id, titre FROM forum");
$req->execute();
$req_forum = $req->fetchAll();

if(!empty($_POST)){
    extract($_POST);

    $valid = true;

    if(isset($_POST['creation'])){

        $titre = (String) ucfirst(trim($titre));
        $categorie = (int) $categorie;
        $contenu= (String) trim($contenu);

        if(empty($titre)){
        $valid = false;
        $err_titre = "Le titre ne peut pas être vide";
    
        }elseif(mb_strlen($titre) < 3){
            $valid = false;
            $err_titre = "Le titre doit faire plus de 2 caractères";
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

            $date_creation = date('Y-m-d H:i:s');

            $req = $pdo->prepare("INSERT INTO topic (id_forum, titre, contenu, date_creation, date_modification, id_users) VALUES (?,?,?,?,?,?)");

            $req->execute([$req_forum_verif['id'], $titre, $contenu, $date_creation, $date_creation, $_SESSION['id']]);

            $UID = (int) $pdo->lastInsertId();

            if($UID >= 0){
                header('Location: topic.php?id=' . $UID);
            }else{
                header('Location: forum.php');
            }
            
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
        <title>Créer une topic</title>
        <link rel="stylesheet" href="/css/create_topic.css">
    </head>
    <body>
        <?php
            require_once('../site_login/menu.php');
        ?>
    <div class="box">
        <form method="post">
            <h1>Créer une topic</h1>
            <label>Titre</label>
            <br/>
            <?php if(isset($err_titre)){ echo '<div>' . $err_titre . '</div>'; }?>
            <input type="text" name="titre" value="<?php if(isset($titre)){ echo $titre; }?>" placeholder="Titre de votre topic"/>
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
            <textarea type="text" name="contenu" placeholder="Votre topic..."><?php if(isset($contenu)){ echo $contenu; }?></textarea>
            <br/>
            <br/>
            <button type="submit" name="creation">Créer mon topic</button>
        </form>
    </div>
        <script src="/caine/caine.js"></script>
    </body>
</html>