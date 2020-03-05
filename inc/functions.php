<?php

/**
 * Fonction de debug de variables
 *
 * @param [all] $variable
 * @return string
 * 
 */
function debug($variable)
{
    echo '
    <div class="row alert alert-light mt-4">
        <div class="col-8">
            <pre>' . print_r($variable, true) . '<pre>
        </div>
    </div>';
}

/**
 * Fonction de génération d'une chaîne de caractères de longueurs variable
 *
 * @param [integer] $length
 * @return string
 */
function str_random($length)
{
    $alphabet = "0123456789azertyuiopqsdfghjklmwxcvbnNBVCXWQSDFGHJKLMPOIUYTREZA";
    return substr(str_shuffle(str_repeat($alphabet, $length)), 0, $length);
}

/**
 * Fonction de détection de session
 *
 * @return void
 */
function loggedOnly()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['auth'])) {
        $_SESSION['flash']['danger'] = "Vous n'êtes pas autoriser à acceder à cette page !";
        header('Location: login.php');
        exit();
    }
}

/**
 * Fonction de reconnexion via un cookie existant
 *
 * @return void
 */
function reconnectCookie()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_COOKIE['remember']) && !isset($_SESSION['auth'])) {
        $remember_token = $_COOKIE['remember'];
        $parts = explode('/==/', $remember_token);
        $user_id = $parts[0];

        require_once "db.php";
        if (!isset($pdo)) {
            global $pdo;
        }

        $req = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $req->execute([$user_id]);
        $user = $req->fetch();
        if ($user) {
            $expected = $user->id . '/==/' . sha1($user->id . 'ratonlaveur');
            if ($expected == $remember_token) {
                session_start();
                $_SESSION['auth'] = $user;
                setcookie('remember', $remember_token, time() + 60 * 60 * 24 * 7);
                $_SESSION['flash'] = '';
            } else {
                setcookie('remember', null, -1);
            }
        } else {
            setcookie('remember', NULL, -1);
        }
    }
}
