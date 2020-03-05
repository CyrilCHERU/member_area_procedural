<?php
require "inc/functions.php";

loggedOnly();

if (!empty($_POST)) {
    if (empty($_POST['password']) || $_POST['password'] != $_POST['password_confirm']) {
        $_SESSION['flash']['danger'] = "Les mots de passe ne correspondent pas !";
    } else {
        $user_id = $_SESSION['auth']->id;
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        require_once "inc/db.php";
        $req = $pdo->prepare("UPDATE users SET password = ?");
        $req->execute([$password]);
        $_SESSION['flash']['success'] = "Votre mot de passe a bien été mis à jour !";
    }
}
require "inc/header.php"; ?>

<h1>Bonjour <?= $_SESSION['auth']->username; ?></h1>
<form action="" method="POST">
    <div class="form-group">
        <input class="form-control" type="password" nema="passord" placeholder="Changer votre mot de passe">
        <input class="form-control" type="password_confirm" nema="passord" placeholder="Confirmer votre nouveau mot de passe">
    </div>
    <button class="btn btn-primary">Modifier</button>

</form>

<?php require "inc/footer.php"; ?>