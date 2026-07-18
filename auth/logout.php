<?php
session_start();

/* REMOVE ALL SESSION DATA */
$_SESSION = [];

/* DESTROY SESSION COOKIE */
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

/* DESTROY SESSION */
session_destroy();

/* REDIRECT */
header("Location: /carconnect/index.php");
exit();
?>