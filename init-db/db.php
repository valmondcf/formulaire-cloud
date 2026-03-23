<?php
$host = "mysql";
$dbname = "formulaire_db";
$db_user = "root";
$pass = "root123";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $db_user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOExeption $e) {
    echo 'Erreur : Impossible de se connecter à la BDD';
    die("Connection error : " . $e->getMessage());
}

?>