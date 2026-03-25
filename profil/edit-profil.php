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

    if(!empty($_POST)){
        extract($_POST);

        $valid = true;

        if(isset($_POST['form1'])){
            $mail = (String) trim($mail);

            if($mail == $_SESSION['mail']){
                $valid = false;

            }elseif(!isset($mail)){
                $valid = false;
                $err_mail = "Ce champ ne peut pas être vide";

            }elseif(!filter_var($mail, FILTER_VALIDATE_EMAIL)){
                $valid = false;
                $err_mail = "format invalide pour ce mail";

            }else{
                $req = $pdo->prepare("SELECT id
                    FROM users
                    WHERE mail = ?");
                
                $req->execute([$mail]);

                $req = $req->fetch();

                if(isset($req['id'])){
                    $valid = false;
                    $err_mail = "Ce mail est déjà utilisé";
                }
            }

            if($valid){

                $req_update = $pdo->prepare('UPDATE users SET mail = ? WHERE id = ?');
                $req_update->execute([$mail, $_SESSION['id']]);

                $_SESSION['mail'] = $mail;

                header('Location: /profil/edit-profil.php');
                exit;
            }
        }elseif(isset($_POST['form2'])){
            $oldpasswd = (String) trim($oldpasswd);
            $passwd = (String) trim($passwd);
            $confpasswd = (String) trim($confpasswd);

            if(!isset($oldpasswd)){
                $valid = false;
                $err_password = "Ce champ ne peut pas être vide";
            
            }else{
        
                $req = $pdo->prepare("SELECT password FROM users WHERE id = ?");

                $req->execute([$_SESSION['id']]);

                $req = $req->fetch();

                if(isset($req['password'])){
                    if(!password_verify($oldpasswd, $req['password'])) {
                        $valid = false;
                        $err_password = "Le mot de pass est incorrect";
                    }
                }else{
                    $valid = false;
                    $err_password = "Le mot de pass est incorrect";
                }
            }

            if($valid){
                if(empty($passwd)){
                    $valid = false;
                    $err_password = "Ce champ ne peut pas être vide";

                }elseif($passwd <> $confpasswd) {
                    $valid = false;
                    $err_password = "Le mot de passe est différent de la confirmation";
                }elseif($passwd == $oldpasswd){
                    $valid = false;
                    $err_password = "Le mot de passe doit être différent de l'ancient";
                }
            }
        
        }

            if($valid){
               
                $password_hache = password_hash($passwd,  PASSWORD_BCRYPT);

                $req_update = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
                $req_update->execute([$password_hache, $_SESSION['id']]);

                header('Location: /profil/edit-profil.php');
                exit;
            }
        
    }

    if(!isset($mail)){
        $mail = $req_user['mail'];
    }
?>

    <html>
    <head>
        <?php
            require_once('../head/link.php');
        ?>
        <title>Modifier le compte</title>
    </head>
Modifier le
    <body>
        <?php
            require_once('../site_login/menu.php');
        ?>
        <div class="container">
            <div class="row">
                <div class="col-3"></div>
                <div class="col-6">
                    <h1>Modifier les informations</h1>
                    <form method="post">
                        <div class="mb-3">
                            <?php if(isset($err_mail)){ echo '<div>' . $err_mail . '</div>'; }?>
                            <input class="form-control" type="email" name="mail" value="<?= $mail ?>" placeholder="Mail"/>
                        </div>
                        <div class="mb-3">
                            <input class="btn btn-primary" type="submit" name="form1" value="Modifier" />
                        </div>
                    </form>
Modifier le
                    <br/>
                    <form method="post">
                        <div class="mb-3">
                            <?php if(isset($err_password)){ echo '<div>' . $err_password . '</div>'; }?>
                            <input class="form-control" type="password" name="oldpasswd" value="" placeholder="Mot de passe actuel"/>
                        </div>
                        <div class="mb-3">
                            <input class="form-control" type="password" name="passwd" value="" placeholder="Nouveau mot de passe"/>
                        </div>
                        <div class="mb-3">
                            <input class="form-control" type="password" name="confpasswd" value="" placeholder="Confirmation du mot de passe"/>
                        </div>
                        <div class="mb-3">
                            <input class="btn btn-primary" type="submit" name="form2" value="Modifier" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
Modifier le