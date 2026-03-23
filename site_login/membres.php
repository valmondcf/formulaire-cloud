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
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.rtl.min.css"
         integrity="sha384-CfCrinSRH2IR6a4e6fy2q6ioOX7O6Mtm1L9vRvFZ1trBncWmMePhzvafv7oIcWiW" crossorigin="anonymous">
        <title>Membres</title>
    </head>
    <body>
        <?php
            require_once('./menu.php');
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