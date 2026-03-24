<?php 
session_start();
if(isset($_SESSION['id'])) {
    header('Location: /site_login/index.php');
} else {
    header('Location: /site_login/connexion.php');
}
exit;
?>
