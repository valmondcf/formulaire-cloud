<?php
    session_start();
    require "../init-db/db.php"; 

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
?>

    <html>
    <head>
        <?php
            require_once('../head/link.php');
        ?>
        <title>Profil de <?= $req_user['name'] ?></title>
    </head>
    <body>
        <?php
            require_once('../site_login/menu.php');
        ?>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>Bonjour <?= $req_user['name'] ?></h1>
                    <div>
                        Date d'inscription : Le <?= $date_inscription ?>
                    </div>
                    <div>
                        Dernière connexion : <?= $date_connexion ?>
                    </div>
                    <div>
                        Rôle utilisateur : <?= $role ?>
                    </div>
                    <div>
                        <a href="/profil/edit-profil.php">Modifier le compte</a>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>