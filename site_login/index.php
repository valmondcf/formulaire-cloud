<?php
session_start();
if(!isset($_SESSION["user_id"])) {
   header("Location: ./login.php");
   exit;
}
?>


<html>
    <head>

    </head>

    <body>
        <h1>Welcome <?= htmlspecialchars($_SESSION["user_name"]) ?></h1>
        <a href="./logout.php" type=button>me déconnecter</button></a>
    </body>

</html>
