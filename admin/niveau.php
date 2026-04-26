<?php
    session_start();
    require "../init-db/db.php";
    require "../init-db/auth.php";

    if(!in_array($_SESSION['role'], [1,2,3,4])){
        header('Location: /');
        exit;
    }

    // Traitement du POST en premier
    if(!empty($_POST)){
        $valid = true;

        if(isset($_POST['changement_role'])){
            $id_users = (int) $_POST['id_users'];
            $role     = (int) $_POST['role'];

            // Vérif que l'utilisateur existe
            $stmt_verif_user = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt_verif_user->execute([$id_users]);
            $verif_users = $stmt_verif_user->fetch();

            if(!$verif_users){
                $valid = false;
                $err_role = "Cet utilisateur n'existe plus";
            } else {
                // Vérif que le rôle cible existe ET est bien en dessous de soi
                $stmt_verif_role = $pdo->prepare("SELECT * FROM admin_role WHERE role = ? AND ordre > ?");
                $stmt_verif_role->execute([$role, $_SESSION['role_ordre']]);
                $verif_role = $stmt_verif_role->fetch();

                if(!$verif_role){
                    $valid = false;
                    $err_role = "Ce rôle n'est pas autorisé";
                }
            }

            if($valid){
                $stmt_update = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
                $stmt_update->execute([$verif_role['role'], $id_users]);
                header('Location: /admin/niveau.php');
                exit;
            }
        }
    }

    // Récupération des utilisateurs en dessous de soi
    $stmt_users = $pdo->prepare("
        SELECT u.*, ar.libelle 
        FROM users u 
        LEFT JOIN admin_role ar ON ar.role = u.role 
        WHERE u.id <> ? AND ar.ordre > ? 
        ORDER BY ar.ordre, u.name
    ");
    $stmt_users->execute([$_SESSION['id'], $_SESSION['role_ordre']]);
    $req_liste_users = $stmt_users->fetchAll();

    // Récupération des rôles assignables (en dessous de soi)
    $stmt_roles = $pdo->prepare("SELECT * FROM admin_role WHERE ordre > ? ORDER BY ordre ASC");
    $stmt_roles->execute([$_SESSION['role_ordre']]);
    $req_liste_role = $stmt_roles->fetchAll();

    $tab_list_role = [];
    foreach($req_liste_role as $rlr){
        $tab_list_role[] = [$rlr['role'], $rlr['libelle']];
    }
?>
<html>
<head>
    <title>Changement rôle</title>
    <?php require_once('../head/link.php'); ?>
    <link rel="stylesheet" href="/css/admin.css">
</head>
<body>
<?php require_once('../site_login/menu.php'); ?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <div>Changement des rôles</div>

            <?php if(isset($err_role)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($err_role) ?></div>
            <?php endif; ?>

            <div>
                <?php foreach($req_liste_users as $rlu): ?>
                    <form method="post">
                        <div>
                            <div><?= htmlspecialchars($rlu['name']) ?></div>
                            <select name="role">
                                <option value="<?= $rlu['role'] ?>" hidden><?= htmlspecialchars($rlu['libelle']) ?></option>
                                <?php foreach($tab_list_role as $tlr): ?>
                                    <option value="<?= $tlr[0] ?>"><?= htmlspecialchars($tlr[1]) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="id_users" value="<?= $rlu['id'] ?>"/>
                            <button type="submit" name="changement_role">Modifier</button>
                        </div>
                    </form>
                    <br>
                <?php endforeach; ?>
            </div>

        </div>
    </div>
</div>

<script src="/caine/caine.js"></script>
</body>
</html>