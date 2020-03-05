<?php require_once 'inc/functions.php';
session_start();

if (!empty($_POST)) {
    $errors = [];
    require_once 'inc/db.php';

    if (empty($_POST['username']) || !preg_match('/^[a-zA-Z0-9]+$/', $_POST['username'])) {
        $errors['username'] = "Ce champ est requis et doit être valide (caractères a-z ou 0-9) !";
    } else {
        $req = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $req->execute([$_POST['username']]);
        $user = $req->fetch();

        if ($user) {
            $errors['username'] = "Ce pseudo existe déjà ;( !";
        }
    }

    if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Cet email n'est pas valide !";
    } else {
        $req = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $req->execute([$_POST['email']]);
        $email = $req->fetch();

        if ($email) {
            $errors['email'] = "Cet email existe déjà pour un autre compte ;( !";
        }
    }

    if (empty($_POST['password']) || ($_POST['password'] != $_POST['password_confirm'])) {
        $errors['password'] = "Les mots de passe ne sont pas identiques !";
    }

    if (empty($errors)) {

        $req = $pdo->prepare("INSERT INTO users SET username = ?, password = ?, email = ?, confirmation_token = ?");

        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $token = str_random(60);
        debug($token);


        $req->execute([$_POST['username'], $password, $_POST['email'], $token]);

        $user_id = $pdo->lastInsertId();

        mail($_POST['email'], 'Confirmation de la création de votre compte', "Afin de valider votre compte, merci de cliquer sur ce lien http://localhost:3000/confirm.php?id=$user_id&confirmation_token=$token");

        $_SESSION['flash']['success'] = "Un email de confirmation vous a été envoyé pour valider votre compte !";
        header('location: login.php');

        exit();
    }
}

require 'inc/header.php';

?>
<div class="container">
    <div class="row justify-content-center mt-4">
        <div class="col-6">
            <h1 class="my-5">Register</h1>

            <?php if (!empty($errors)) : ?>
                <div class="aler alert-danger p-3 rounded mb-3 text-light">
                    <p>Le formulaire n'est pas rempli correctement !</p>
                    <?php foreach ($errors as $error) : ?>
                        <ul>
                            <li><?= $error; ?></li>
                        </ul>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label for="">Username</label>
                    <input class="form-control" type="text" name="username">
                </div>
                <div class="form-group">
                    <label for="">Email</label>
                    <input class="form-control" type="text" name="email">
                </div>
                <div class="form-group">
                    <label for="">Password</label>
                    <input class="form-control" type="password" name="password">
                </div>
                <div class="form-group">
                    <label for="">Confirm Password</label>
                    <input class="form-control" type="password" name="password_confirm">

                </div>
                <button type="submit" class="btn btn-success">Register</button>
            </form>
        </div>
    </div>
</div>

<?php require 'inc/footer.php'; ?>