<?php
if(!isset($_SESSION['id'])) {
    header('Location: /site_login/connexion.php');
    exit;
}