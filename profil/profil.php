<?php
    session_start();
    require "../init-db/db.php"; 
    require "../init-db/auth.php";

    if(!isset($_SESSION['id'])){
        header('Location: /');
        exit ;
    }

    $requete = $pdo->prepare("SELECT *
        FROM users
        WHERE id = ?");

    $requete->execute([$_SESSION['id']]);

    $req_user= $requete->fetch();

    $date = date_create($req_user['date_creation']);
    $date_inscription = date_format($date, "d/m/Y");

    $date = date_create($req_user['date_connexion']);
    $date_connexion = date_format($date, "d/m/Y à H:i");
    
    switch($req_user['role']){
        case 0:
            $role= "User";
        break;
        case 1:
            $role = "Admin";
        break;
        case 2:
            $role = "Modérateur";
        break;
        case 3:
            $role = "Superadmin";
        break;
        default:
        $role = "Inconnu";
        break;
    }

    $chemin_avatar = null;

    if(!empty($req_user['avatar'])){
        $chemin_avatar = '/public/pp/' . $_SESSION['id'] . '/'  . $_SESSION['avatar'];

    }else{
        $chemin_avatar = '/public/pp/defaut/defaut.png';
    }
?>

    <html>
    <head>
        <?php
            require_once('../head/link.php');
        ?>
        <title>Profil de <?= $req_user['name'] ?></title>
        <link rel="stylesheet" href="/css/profil.css">
    </head>
    <body>
        <?php
            require_once('../site_login/menu.php');
        ?>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>Bonjour <?= $req_user['name'] ?></h1>
                    <div class="avatar-bloc">
                        <img src="<?= $chemin_avatar ?>" class="profil_pp"/>
                        <a href="/profil/pp.php" class="btn-avatar">Changer d'avatar</a>
                    </div>

                    <div class="info-bloc">Date d'inscription : Le <?= $date_inscription ?></div>
                    <div class="info-bloc">Dernière connexion : <?= $date_connexion ?></div>
                    <div class="info-bloc">Rôle utilisateur : <?= $role ?></div>

                    <div>
                        <a href="/profil/edit-profil.php" class="btn-modifier">Modifier le compte</a>
                    </div>
                </div>
            </div>
        </div>
        <script src="/caine/caine.js"></script>
    </body>
</html>