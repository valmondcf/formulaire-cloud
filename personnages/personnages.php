<?php
    session_start();
    require "../init-db/db.php"; 

    $req_sql = "SELECT id, name, avatar_url, species, status FROM characters";
    $requete = $pdo->prepare($req_sql);
    $requete->execute();
    $req_membres= $requete->fetchAll();

?>

    <html>
    <head>
        <?php
            require_once('../head/link.php');
        ?>
        <title>liste des Personnages</title>
    </head>
    <body>
        <?php
            require_once('../site_login/menu.php');
        ?>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>Personnages</h1>
                </div>
                <?php
                    foreach($req_membres as $rm){
                ?> 
                <div class="col-3">
                    <div><?= $rm['name'] ?></div>
                    <div>
                        <a href="/profil/voir_profil_perso.php?id<?= $rm['id']?>">Voir personnage</a>
                    </div>
                </div>
                <?php
                }
                ?>
            </div>
        </div>
    </body>
</html>