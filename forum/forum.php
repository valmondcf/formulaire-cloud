<?php
    session_start();
    require "../init-db/db.php"; 

    $requete = $pdo->prepare("SELECT * FROM forum ORDER BY ordre");

    $requete->execute();
    
    $req_forum = $requete->fetchAll();

?>

    <html>
    <head>
        <?php
            require_once('../head/link.php');
        ?>
        <title>Forum</title>
    </head>
    <body>
        <?php
            require_once('../site_login/menu.php');
        ?>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>Forum</h1>
                </div>
                <?php
                    foreach($req_forum as $rf){
                ?> 
                <div class="col-3">
                    <div><?= $rf['titre'] ?></div>
                    <div>
                        <a href="/forum/liste-topics.php?id=<?= $rf['id']?>">Voir topic</a>
                    </div>
                </div>
                <?php
                }
                ?>
            </div>
        </div>
        <script src="/cain.js"></script>
        <script src="/caine_tadc__1_.glb"></script>
    </body>
</html>