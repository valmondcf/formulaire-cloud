<?php
require "./db.php"; 

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $name = $_POST['name'];
    $mail = $_POST['mail'];
    $password = $_POST['password'];
    $password_hache=password_hash($password, PASSWORD_DEFAULT);

    $requete = $pdo->prepare("INSERT INTO users VALUES (0, :name, :mail, :password)");
    $requete->execute(
        array(
            "name" => $name,
            "mail" => $mail,
            "password" => $password_hache
        )
    );
        echo'vous êtes co';
}
?>