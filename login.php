<?php
require_once "inc/functions.php";
require "inc/header.php";

reconnectCookie();


if (isset($_SESSION['auth'])) {
    header('Location: account.php');
    exit();
}
if (!empty($_POST) && !empty($_POST['username']) && !empty($_POST['password'])) {

    require_once "inc/db.php";

    $req = $pdo->prepare("SELECT * FROM users WHERE (username = :username OR email = :username) AND confirmed_at IS NOT NULL");
    $req->execute(['username' => $_POST['username']]);
    $user = $req->fetch();

    if (password_verify($_POST['password'], $user->password)) {

        $_SESSION['auth'] = $user;
        $_SESSION['flash']['success'] = "Vous êtes bien identifié et connecté !";

        if ($_POST['remember']) {
            require_once 'inc/functions.php';
            $remember_token = str_random(250);
            $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?")->execute([$remember_token, $user->id]);
            setcookie('remember', $user->id . '/==/' . sha1($user->id . 'ratonlaveur') . $remember_token, time() + 60 * 60 * 24 * 7);
        }

        header('Location: account.php');
        exit();
    } else {
        $_SESSION['flash']['danger'] = "Identifiant ou mot de passe incorrect ;( !!!";
    }
}
?>
<div class="row justify-content-center mt-5">
    <div class="col-6">
        <h1>Connexion</h1>

        <form action="" method="POST">
            <div class="form-group">
                <label for="">Username or Email</label>
                <input class="form-control" type="text" name="username">
            </div>
            <div class="form-group">
                <label for="">Password</label>
                <input class="form-control" type="password" name="password">
            </div>
            <a href="forget.php">( Mot de passe oublié )</a>
            <div class="form-group float-right">
                <label>
                    <input type="checkbox" name="remember" value="1"> Se souvenir de moi
                </label>
            </div>
            <div class="row mt-4 justify-content-center clear-fix">
                <button type="submit" class="btn btn-success">Connect</button>
            </div>
        </form>
    </div>
</div>


<?php require "inc/footer.php"; ?>