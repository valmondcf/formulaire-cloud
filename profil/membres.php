<?php
    session_start();
    require "../init-db/db.php"; 

    $req_sql = "SELECT id, name
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
        <title>Membres</title>
    </head>
    <body>
        <?php
            require_once('../site_login/menu.php');
        ?>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>Membres</h1>
                </div>
                <?php
                    foreach($req_membres as $rm){
                ?> 
                <div class="col-3">
                    <div><?= $rm['name'] ?></div>
                    <div>
                        <a href="voir-profil.php?id<?= $rm['id']?>">Voir profil</a>
                    </div>
                </div>
                <?php
                }
                ?>
            </div>
        </div>
    </body>
</html>