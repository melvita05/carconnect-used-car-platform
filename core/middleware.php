<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ===================== */
/* REQUIRE LOGIN */
/* ===================== */
function requireLogin() {
    if (empty($_SESSION['user_id'])) {
        header("Location: /carconnect/auth/login.php");
        exit();
    }
}

/* ===================== */
/* REQUIRE ROLE */
/* ===================== */
function requireRole($role) {
    requireLogin();

    if (empty($_SESSION['role']) || $_SESSION['role'] !== $role) {

        // 🔥 Optional debug (remove later)
        // echo "Access Denied. Required role: $role";
        // exit();

        header("Location: /carconnect/index.php");
        exit();
    }
}

/* ===================== */
/* REDIRECT BY ROLE */
/* ===================== */
function redirectByRole($role) {

    switch($role){

        case 'admin':
            header("Location: /carconnect/admin/admin_dashboard.php");
            break;

        case 'buyer':
            header("Location: /carconnect/buyer/home.php");
            break;

        case 'seller':
            header("Location: /carconnect/seller/seller_dashboard.php");
            break;

        default:
            header("Location: /carconnect/index.php");
            break;
    }

    exit();
}
?>