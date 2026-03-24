<?php
session_start();
require "../init-db/db.php"; 

    $valid = (boolean) true;

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $name = $_POST['name'];
    $password = $_POST['password'];

    if(empty($name)){
        $valid = false;
        $err_name = "Ce champ ne peut pas être vide";
    }

    if(empty($password)){
        $valid = false;
        $err_password = "Ce champ ne peut pas être vide";
    }

    if($valid){
        $req = $pdo->prepare("SELECT * FROM users WHERE name = ?");
        $req->execute(array($name));
        $row = $req->fetch();

        if ($row && password_verify($password, $row["password"])) {  #que l’utilisateur existe , que le mot de passe correspond (hashé en base), password_verify() compare le mot de passe tapé avec le hash stocké.
            if($row["desactive"] === null){  
                $requete = $pdo->prepare("UPDATE users SET date_connexion = NOW() WHERE id = ?");
                $requete->execute(array($row['id']));

                $_SESSION['id'] = $row['id'];
                $_SESSION['name'] = $row['name'];
                $_SESSION['mail'] = $row['mail'];
                $_SESSION['role'] = $row['role'];

                header('Location: ./index.php');
                exit();
            } else {
                $error = "This account is disactivated";
            }
        }else{ 
            $valid = false;
            $err_name = "La combinaison du pseudo /mdp est incorrect";
        }
        }
    }
?>

    <html>
    <head>
        <link rel="stylesheet" href="style_login.css">
    </head>
    <body>
        <div class="box">
            <form method="post">
                <h1>CONNEXION</h1>
                <label>nom</label>
                <br/>
                <?php if(isset($err_name)){ echo '<div>' . $err_name . '</div>'; }?>
                <input type="text" name="name" value="<?php if(isset($name)){ echo $name; }?>" placeholder="Entrez votre nom..."/>
                <br/>
                <br/>
                <label>mot de passe</label>
                <br/>
                <?php if(isset($err_password)){ echo '<div>' . $err_password . '</div>'; }?>
                <input type="password" name="password" value="<?php if(isset($password)){ echo $password; }?>" placeholder="Entrez votre mot de passe..." />
                <br/>
                <br/>
                <a href="./inscription.php" class="button">je n'ai pas de compte</a>
                <button type="submit" name="connexion" >Se connecter</button>
            </form>
        </div>
    </body>
</html>