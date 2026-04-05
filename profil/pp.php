<?php
session_start();
require "../init-db/db.php"; 

if(!isset($_SESSION['id'])){
    header('Location: /');
    exit;
}

if(!empty($_POST)){
    extract($_POST);
    $valid = true;

    if(isset($_POST['modifier'])){
        if(isset($_FILES['file']) && !empty($_FILES['file']['name'])){
            $tailleMax = 5242880; // 5mo
            if($_FILES['file']['size'] <= $tailleMax){
                $extensionValides = array('jpg', 'png', 'jpeg');
                $extensionUpload = strtolower(substr(strrchr($_FILES['file']['name'], '.'), 1));

                if(in_array($extensionUpload, $extensionValides)){
                    $dossier = __DIR__ . '/../public/pp/' . $_SESSION['id'] . '/';
                    if(!is_dir($dossier)){
                        mkdir($dossier, 0755, true);
                    }
                    $nom = md5(uniqid(rand(), true));
                    $chemin = $dossier . $nom . '.' . $extensionUpload;
                    $resultat = move_uploaded_file($_FILES['file']['tmp_name'], $chemin);

                    if($resultat){
                        $req = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
                        $req->execute([($nom . '.' . $extensionUpload), $_SESSION['id']]);
                        header('Location: /profil/profil.php');
                        exit;
                    } else {
                        $err_pp = "Impossible d'importer votre fichier";
                    }
                } else {
                    $err_pp = "L'extension du fichier n'est pas valide";
                }
            } else {
                $err_pp = "Le fichier est trop gros, il doit faire au maximum 5mo";
            }
        } else {
            $err_pp = "Le fichier n'est pas valide";
        }
    }
}
?>

    <html>
    <head>
        <?php
            require_once('../head/link.php');
        ?>
        <title>Changer d'avatar</title>
        <link rel="stylesheet" href="/css/edit-profil.css">
    </head>
    <body>
        <?php
            require_once('../site_login/menu.php');
        ?>
        <div class="container">
            <div class="row">
                <div class="col-3"></div>
                <div class="col-6">
                    <h1>Changer d'avatar</h1>

                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <?php if(isset($err_pp)){ echo '<div>' . $err_pp . '</div>'; }?>
                            <input class="form-control" type="file" name="file" />
                        </div>
                        <div class="mb-3">
                            <input class="btn btn-primary" type="submit" name="modifier" />
                        </div>
                    </form>                   
                </div>
            </div>
        </div>
        <script src="/caine/caine.js"></script>
    </body>
</html>
