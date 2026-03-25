<html>
    <head>
    </head>
    <body>
         <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <div class="navbar-nav">
                        <?php
                            if(!isset($_SESSION['id'])){
                        ?>
                        <a class="nav-link" href="/site_login/inscription.php">Inscription</a>
                        <a class="nav-link" href="/site_login/connexion.php">Connexion</a>
                        <?php
                            }else{
                        ?>
                        <a class="nav-link active" aria-current="page" href="/">Accueil</a>
                        <a class="nav-link" href="/profil/profil.php">Mon Profil</a>
                        <a class="nav-link" href="/profil/membres.php">Personnages</a>
                        <a class="nav-link" href="/profil/membres.php">Membres du forum</a>
                        <a class="nav-link" href="/site_login/logout.php">Déconnexion</a>
                        <?php
                            }
                        ?>
                    </div>
                </div>
            </div>
        </nav>
    </body>
</html>    
    
    
    
    