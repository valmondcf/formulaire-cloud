<?php
 
require "../init-db/db.php"; 

if(isset($_SESSION['id'])){
    header('Location: /');
    exit;
}

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $name = $_POST['name'];
    $mail = $_POST['mail'];
    $password = $_POST['password'];
    $confmail = $_POST['confmail'] ?? '';
    $confpass = $_POST['confpass'] ?? '';

    $valid = (boolean) true;

    if(empty($name)){
        $valid = false;
        $err_name = "Ce champ ne peut pas être vide";
    
    }elseif(mb_strlen($name) < 3){
        $valid = false;
        $err_name = "Le nom doit faire plus de 5 caractères";
    }elseif(mb_strlen($name) > 25){
        $valid = false;
        $err_name = "Le nom doit faire moins de 25 caractères (" . mb_strlen($name) . "/25)";
    }else{
        $req = $pdo->prepare("SELECT id
            FROM users
            WHERE name = ?");
        
        $req->execute(array($name));

        $req =$req->fetch();

        if(isset($req['id'])){
            $valid = false;
            $err_name = "Ce nom est déjà pris";
        }
    }
    if(empty($mail)){
        $valid = false;
        $err_mail = "Ce champ ne peut pas être vide";

    }elseif(!filter_var($mail, FILTER_VALIDATE_EMAIL)){
        $valid = false;
        $err_mail = "format invalide pour ce mail";

    }elseif($mail <> $confmail){
        $valid = false;
        $err_mail = "le mail est différent de la confirmation";

    }else{
        $req = $pdo->prepare("SELECT id
            FROM users
            WHERE mail = ?");
        
        $req->execute(array($mail));

        $req =$req->fetch();

        if(isset($req['id'])){
            $valid = false;
            $err_mail = "Ce mail est déjà pris";
        }
    }

    if(empty($password)){
        $valid = false;
        $err_password = "Ce champ ne peut pas être vide";

    } elseif($password <> $confpass) {
        $valid = false;
        $err_password = "Le mot de passe est différent de la confirmation";
    } 

    if($valid){

    $password_hache = password_hash($password,  PASSWORD_BCRYPT);

    $requete = $pdo->prepare("
        INSERT INTO users (name, mail, password)
        VALUES (:name, :mail, :password)");
    $requete->execute(
        array(
            "name" => $name,
            "mail" => $mail,
            "password" => $password_hache
        )
    );
    header('Location: connexion.php');
    exit();
    }
}
?>

    <html>
    <head>
        <link rel="stylesheet" href="./style_login.css">
    </head>
    <body>
    <div class="box">
        <form method="post">
            <h1>CRÉATION DU COMPTE</h1>
            <label>nom</label>
            <br/>
            <?php if(isset($err_name)){ echo '<div>' . $err_name . '</div>'; }?>
            <input type="text" name="name" value="<?php if(isset($name)){ echo $name; }?>" placeholder="Entrez votre nom..."/>
            <br/>
            <br/>
            <label>mail</label>
            <br/>
            <?php if(isset($err_mail)){ echo '<div>' . $err_mail . '</div>'; }?>
            <input type="email" name="mail" value="<?php if(isset($mail)){ echo $mail; }?>" placeholder="Entrez votre mail..."/>
            <br/>
            <br/>
            <label>Confirmation du mail</label>
            <br/>
            <input type="email" name="confmail" value="" placeholder="Confirmez votre mail" required/>
            <br/>
            <br/>
            <label>mot de passe</label>
            <br/>
            <?php if(isset($err_password)){ echo '<div>' . $err_password . '</div>'; }?>
            <input type="password" name="password" value="<?php if(isset($password)){ echo $password; }?>" placeholder="Entrez votre mot de passe..." />
            <br/>
            <br/>
            <label>Confirmation du mdp</label>
            <br/>
            <input type="password" name="confpass" value="" placeholder="Confirmez votre mot de passe" />
            <br/>
            <br/>
            <a href="./connexion.php" class="button">j'ai déjà un compte</a>
            <button type="submit">m'inscrire</button>
        </form>
    </div>
        <script src="/cain.js"></script>
        <script src="/caine_tadc__1_.glb"></script>
    </body>
</html>