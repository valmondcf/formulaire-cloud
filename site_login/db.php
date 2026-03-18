<?php
// modifié
$host = "mysql";
$dbname = "formulaire_db";
$user = "root";
$pass = "root123";

try {
    $pdo = new PDO(
        // modifié
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOExeption $e) {
    die("Connection error : " . $e->getMessage());
}

?>