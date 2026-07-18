<?php

/* ===================== */
/* SAFE OUTPUT */
/* ===================== */

function e($str){
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

/* ===================== */
/* INPUT HELPERS */
/* ===================== */

function post($key, $default=""){
    return isset($_POST[$key]) ? trim((string)$_POST[$key]) : $default;
}

function get($key, $default=""){
    return isset($_GET[$key]) ? trim((string)$_GET[$key]) : $default;
}

/* ===================== */
/* VALIDATION */
/* ===================== */

function is_valid_email($email){
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/* ===================== */
/* REDIRECT */
/* ===================== */

function redirect($path){
    header("Location: $path");
    exit();
}

/* ===================== */
/* METHOD CHECK */
/* ===================== */

function require_post(){
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        http_response_code(405);
        exit("Method Not Allowed");
    }
}

/* ===================== */
/* AUTH HELPERS */
/* ===================== */

function is_logged_in(){
    return !empty($_SESSION['user_id']);
}

function require_login(){
    if(!is_logged_in()){
        redirect("/carconnect/auth/login.php");
    }
}

function require_role($role){
    if(empty($_SESSION['role']) || $_SESSION['role'] !== $role){
        exit("Access Denied");
    }
}

/* ===================== */
/* FLASH MESSAGES 🔥 */
/* ===================== */

function set_flash($msg, $type="success"){
    $_SESSION['flash'] = [
        "msg"=>$msg,
        "type"=>$type
    ];
}

function show_flash(){
    if(!empty($_SESSION['flash'])){
        $f = $_SESSION['flash'];
        echo "<div class='alert {$f['type']}'>".e($f['msg'])."</div>";
        unset($_SESSION['flash']);
    }
}

/* ===================== */
/* CSRF PROTECTION 🔒 */
/* ===================== */

function csrf_token(){
    if(empty($_SESSION['csrf'])){
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function verify_csrf($token){
    return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}

?>