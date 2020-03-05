<?php

if (isset($_GET['id']) && isset($_GET['token'])) {
    require_once 'inc/db.php';
    $req = $pdo->prepare("SELECT * FROM users WHERE id = ? AND token = ? AND reset_at IS NOT NULL AND reset_at > DATE_SUB(NOW(), INTERVAL 30 MINUTE)");
    $req->execute([$_GET['id'], $_GET['token']]);
    $user = $req->fetch();
    if ($user) {
        if (!empty($_POST)) {
            if (!empty($_POST['password'] && $_POST['password'] == $_POST['password_confirm'])) {
                $password = password_hash($$_POST['password'], PASSWORD_BCRYPT);
                $pdo->prepare("UPDATE users SET password = ?, reset_at = NULL, reset_token = NULL")->execute([$password]);
                session_start();
                $_SESSION['flash']['success'] = "Votre mot de passe a nien été modifié !";
                $_SESSION['auth'] = $user;
                header('Location: account.php');
                exit();
            }
        }
    } else {
        session_start();
        $_SESSION['flash']['danger'] = "Ce token n'est pas valide !";
        header('Location: login.php');
        exit();
    }
} else {
    header('Location: login.php');
    exit();
}
require "inc/header.php"; ?>

<?php
if (!empty($_POST) && !empty($_POST['username']) && !empty($_POST['password'])) {
    require_once "inc/functions.php";
    require_once "inc/db.php";

    $req = $pdo->prepare("SELECT * FROM users WHERE (username = :username OR email = :username) AND confirmed_at IS NOT NULL");
    $req->execute(['username' => $_POST['username']]);
    $user = $req->fetch();

    if (password_verify($_POST['password'], $user->password)) {
        session_start();
        $_SESSION['auth'] = $user;
        $_SESSION['flash']['success'] = "Vous êtes bien identifié et connecté !";
        header('Location: account.php');
        exit();
    } else {
        $_SESSION['flash']['danger'] = "Identifiant ou mot de passe incorrect ;( !!!";
    }
}
?>
<h1>Réinitialisation de votre mot de passe</h1>

<form action="" method="POST">
    <div class="form-group">
        <label for="">Password</label>
        <input class="form-control" type="password" name="password">
    </div>
    <div class="form-group">
        <label for="">Confirm password</label>
        <input class="form-control" type="password" name="password-confirm">
    </div>
    <div class="row mt-2 justify-content-center">
        <button type="submit" class="btn btn-success">Submit</button>
    </div>
</form>

<?php require "inc/footer.php"; ?>