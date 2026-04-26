<?php
    session_start();
    require "../init-db/db.php";
    require "../init-db/auth.php";

    if(!in_array($_SESSION['role'], [1,2,3])){
        header('Location: /');
        exit;
        }
?>

<html>
    <head>
        <title>Dashboard</title>
        <link rel="stylesheet" href="/css/admin.css">
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
                    <div>mon espace d'admin</div>
                </div>
                <div>
                    <a href="/admin/niveau.php">Modifier le rôle d'un utilisateur</a>
                </div>
            </div>
        </div>
        <script src="/caine/caine.js"></script>
    </body>
</html>