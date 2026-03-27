<?php
    session_start();
    require "../init-db/db.php"; 

    $get_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    if($get_id <= 0){
        header('Location: /profil/membres.php');
        exit;
    }

    if(isset($_SESSION['id']) && $get_id == $_SESSION['id']){
        header('Location: /profil/profil.php');
        exit;
    }

    
    $requete = $pdo->prepare("SELECT *
        FROM users
        WHERE id = ?");

    $requete->execute([$get_id]);

    $req_user= $requete->fetch();

    $date_inscription = $req_user['date_creation'] 
    ? date_format(date_create($req_user['date_creation']), "d/m/Y") 
    : "Non renseignée";

    $date_connexion = $req_user['date_connexion'] 
    ? date_format(date_create($req_user['date_connexion']), "d/m/Y à H:i") 
    : "Jamais connecté";
    
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
                </div>
            </div>
        </div>
        <script src="/cain.js"></script>
        <script src="/caine_tadc__1_.glb"></script>
    </body>
</html>