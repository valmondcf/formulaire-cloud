<?php
session_start();
require "../init-db/db.php";
$requete = $pdo->prepare("SELECT id, name, door_url FROM characters ORDER BY id");
$requete->execute();
$personnages = $requete->fetchAll();
?>
<html lang="fr">
<head>
<?php require_once('../head/link.php'); ?>
<title>Personnages du Cirque</title>
<style>
body {
    background-image: url("/media/couloir.jpg");
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
    min-height: 100vh;
}
.navbar {
    background-color: rgba(80, 0, 120, 0.5) !important;
}
.navbar .nav-link {
    color: white !important;
}
.navbar .nav-link:hover {
    color: rgba(255, 255, 255, 0.7) !important;
}
.page-title {
    background: linear-gradient(135deg, #6a0dad, #ff2d78);
    color: yellow;
    text-shadow: 0 0 16px #ffe60060;
    margin-bottom: 8px;
}
.page-subtitle {
    color: #00e5cc;
    font-size: 13px;
    letter-spacing: 3px;
    margin-bottom: 28px;
}
.door-card {
    text-align: center;
    transition: transform 0.2s;
}
.door-card:hover {
    transform: scale(1.05);
}
.door-card img {
    width: 100%;
    border-radius: 12px;
    display: block;
}
.door-name {
    font-size: 18px;
    font-weight: bold;
    color: #ffe600;
    margin-top: 8px;
    margin-bottom: 8px;
    text-shadow: 0 0 8px #ffe60060;
}
.btn-voir {
    background: linear-gradient(135deg, #6a0dad, #ff2d78);
    border: none;
    border-radius: 8px;
    padding: 8px 18px;
    color: white;
    font-size: 13px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    transition: opacity 0.2s;
    margin-bottom: 10px;
}
.btn-voir:hover {
    opacity: 0.85;
    color: white;
    text-decoration: none;
}
</style>
</head>
<body>
<?php require_once('../site_login/menu.php'); ?>
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h1 class="page-title">★ Les Résidents du Cirque ★</h1>
            <p class="page-subtitle">LE CIRQUE NUMÉRIQUE</p>
        </div>
    </div>
    <div class="row g-4">
        <?php foreach ($personnages as $p): ?>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="door-card">
                <img src="<?= htmlspecialchars($p['door_url']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                <div class="door-name"><?= htmlspecialchars($p['name']) ?></div>
                <a href="/personnages/voir_profil_perso.php?id=<?= $p['id'] ?>" class="btn-voir">
                    Découvrir →
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<script src="/cain.js"></script>
</body>
</html>