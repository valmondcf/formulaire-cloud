<?php
    session_start();
    require "../init-db/db.php"; 
    require "../init-db/auth.php";

    $req_sql = "SELECT id, name, avatar
        FROM users";
    
    if(isset($_SESSION['id'])){
        $req_sql .= " WHERE id <> ?";
    }

    $requete = $pdo->prepare($req_sql);

    if(isset($_SESSION['id'])){
        $requete->execute([$_SESSION['id']]);
    }else{
        $requete->execute();
    }

    $req_membres= $requete->fetchAll();

?>

    <html>
    <head>
        <?php
            require_once('../head/link.php');
        ?>
        <title>Membres du forum</title>
        <link rel="stylesheet" href="/css/membres.css">
    </head>
    <body>
        <?php
            require_once('../site_login/menu.php');
        ?>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>Membres du forum</h1>
                </div>
                <?php
                    foreach($req_membres as $rm){

                        $chemin_avatar = null;

                        if(!empty($rm['avatar'])){
                            $chemin_avatar = '/public/pp/' . $rm['id'] . '/'  . $rm['avatar'];
                        }else{
                            $chemin_avatar = '/public/pp/defaut/defaut.png';
                        }
                ?> 
                <div class="col-3">
                    <div><?= $rm['name'] ?></div>
                    <div>
                        <img src="<?= $chemin_avatar ?>" class ="profil_pp"/>
                    </div>
                    <div>
                        <a href="/profil/voir_profil.php?id=<?= $rm['id']?>">Voir profil</a>
                    </div>
                </div>
                <?php
                }
                ?>
            </div>
        </div>
        <script src="/caine/caine.js"></script>
    </body>
</html>