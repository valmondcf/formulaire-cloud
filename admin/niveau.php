<?php
    session_start();
    require "../init-db/db.php";

    if(!in_array($_SESSION['role'], [1,2,3])){
        header('Location: /');
        exit;
        }
    
    $req = $pdo->prepare("SELECT u.*, ar.libelle FROM users u LEFT JOIN admin_role ar ON ar.role = u.role WHERE u.id <> ?");
    $req->execute([$_SESSION['id']]);
    $req_liste_users = $req->fetchAll();

    $req = $pdo->prepare("SELECT * FROM admin_role");
    $req->execute();
    $req_liste_role = $req->fetchAll();

    $tab_list_role =[];

    foreach($req_liste_role as $rlr){
        array_push($tab_list_role, [$rlr['role'], $rlr['libelle']]);
    }
?>

<html>
    <head>
        <title>changement rôle</title>
        <?php
            require_once('../head/link.php');
        ?>
        <title>Profil de <?= $req_user['name'] ?></title>
        <link rel="stylesheet" href="/css/admin.css">
    </head>
    <body>
        <?php
            require_once('../site_login/menu.php');
        ?>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div>Changement des rôles</div>
                <div>
                    <form method="post">
                        <?php
                            foreach($req_liste_users as $rlu){
                        ?>
                        <div>
                            <div><?= $rlu['name']?></div>
                            <select name="role">
                                <option value="<?= $rlu['role']?>"hidden><?= $rlu['libelle']?></option>
                                <?php
                                    foreach($tab_list_role as $tlr){
                                ?>
                                <option value="<?= $rlu['0']?>"><?= $tlr['1']?></option>
                                <?php
                                    }
                                ?>
                            </select>
                        </div>
                        <br>
                        <?php
                            }
                        ?>
                </div>
            </div>
        </div>
        <script src="/caine/caine.js"></script>
    </body>
</html>