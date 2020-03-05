<?php
require "inc/header.php";

if (!empty($_POST) && !empty($_POST['email'])) {
    require_once "inc/functions.php";
    require_once "inc/db.php";

    $req = $pdo->prepare("SELECT * FROM users WHERE email = ? AND confirmed_at IS NOT NULL");
    $req->execute([$_POST['email']]);
    $user = $req->fetch();

    if ($user) {
        session_start();
        $reset_token = str_random(60);
        $pdo->prepare("UPDATE users SET reset_token = ?, reset_at = NOW() WHERE id = ?")->execute([$reset_token, $user->id]);

        $_SESSION['flash']['success'] = "Les instructions du rappel de mot de passe vous ont été envoyées par email";

        mail($_POST['email'], 'Réinitialisation de votre mot de passe', "Afin de réinitialiser votre mot de passe, merci de cliquer sur ce lien http://localhost:3000/reset.php?id={$user->id}&token=$reset_token");

        header('Location: login.php');
        exit();
    } else {
        $_SESSION['flash']['danger'] = "Aucun compte ne correspond à cette adresse !";
    }
}
?>

<div class="container">
    <div class="row justify-content-center mt-4">
        <div class="col-6">
            <h1>Forget Password</h1>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="">Email</label>
                    <input class="form-control" type="email" name="email">
                </div>
                <div class="row mt-2 justify-content-center">
                    <button type="submit" class="btn btn-success">Connect</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require 'inc/footer.php'; ?>