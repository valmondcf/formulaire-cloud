<html>
    <head>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
    <div class="box">
        <form method="post" action="traitement.php">
            <h1>CRÉATION DU COMPTE</h1>
            <label>nom</label>
            <input type="text" name="name" placeholder="Entrez votre nom..." required />
            <br/>
            <br/>
            <label>mail</label>
            <input type="mail" name="mail" placeholder="Entrez votre mail..."required />
            <br/>
            <br/>
            <label>mot de passe</label>
            <input type="password" name="password" placeholder="Entrez votre mot de passe..." required />
            <br/>
            <br/>
            <a href="./login.php"><button class="button">j'ai déjà un compte</button></a>
            <button type="submit">m'inscrire</button>
        </form>
    </div>
    </body>
</html>