<?php
session_start();
require "./db.php";  #connexion bdd

$error = "";  #message d'erreur si la connexion échoue

if($_SERVER["REQUEST_METHOD"] === "POST"){   #Vérifie si le formulaire a été envoyé en POST
    $mail =  trim($_POST["mail"]);  #Récupère l’email envoyé, trim enlève les espaces
    $password = $_POST["password"];   #Récupère le mot de passe envoyé

    $stmt = $pdo->prepare("SELECT * FROM users WHERE mail = ?");   #Cherche l’utilisateur dont le mail correspond
    $stmt->execute([$mail]); 
    $user = $stmt->fetch();  #Récupère le résultat sous forme de tableau

    if ($user && password_verify($password, $user["password"])) {  #que l’utilisateur existe , que le mot de passe correspond (hashé en base), password_verify() compare le mot de passe tapé avec le hash stocké.
        if($user["desactive"] === null){  
            $_SESSION["user_name"] = $user["name"];  #Stocke les infos de l’utilisateur dans la session
            $_SESSION["user_id"] = $user["id"];
            header("Location: ./");  #Redirige vers la page d’accueil après connexion(index.php)
            exit;  #Stoppe l’exécution du script (important après un header)
        } else {
            $error = "This account is disactivated";
        }
    } else {
        $error = "Incorrect Email Adress  /  password";  #Message d’erreur si l’authentification échoue
   } 
}
?>


<html>
    <head>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
    <div class="box">
        <form method="post">
            <h1>CONNEXION</h1>
            <p><?php echo $error ?></p>
            <label>mail</label>
            <input type="mail" name="mail" required />
            <br/>
            <br/>
            <label>mot de passe</label>
            <input type="password" name="password" required />
            <br/>
            <br/>
            <a href="./inscription.php" class="button">je n'ai pas de compte</a>
            <button type="submit">confirmer</button>
        </form>
    </div>
    </body>
</html>