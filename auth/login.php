<?php

session_start();

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";

$err = "";


/* =========================================================
   LOGIN PROCESS
   ========================================================= */

if (
    $_SERVER['REQUEST_METHOD'] ===
    "POST"
) {

    $email =
        strtolower(
            trim(
                $_POST['email'] ?? ''
            )
        );

    $password =
        $_POST['password'] ?? '';


    $user = null;
    $role = "";
    $redirect = "";


    /* =========================
       ADMIN
       ========================= */

    $stmt =
        mysqli_prepare(
            $conn,
            "
            SELECT id,password
            FROM admins
            WHERE email=?
            LIMIT 1
            "
        );

    mysqli_stmt_bind_param(
        $stmt,
        "s",
        $email
    );

    mysqli_stmt_execute(
        $stmt
    );

    $res =
        mysqli_stmt_get_result(
            $stmt
        );

    $row =
        mysqli_fetch_assoc(
            $res
        );

    mysqli_stmt_close(
        $stmt
    );


    if (
        $row &&
        password_verify(
            $password,
            $row['password']
        )
    ) {

        $user =
            $row;

        $role =
            "admin";

        $redirect =
            "/carconnect/admin/admin_dashboard.php";
    }


    /* =========================
       SELLER
       ========================= */

    if (!$user) {

        $stmt =
            mysqli_prepare(
                $conn,
                "
                SELECT id,password
                FROM sellers
                WHERE email=?
                LIMIT 1
                "
            );

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $email
        );

        mysqli_stmt_execute(
            $stmt
        );

        $res =
            mysqli_stmt_get_result(
                $stmt
            );

        $row =
            mysqli_fetch_assoc(
                $res
            );

        mysqli_stmt_close(
            $stmt
        );


        if (
            $row &&
            password_verify(
                $password,
                $row['password']
            )
        ) {

            $user =
                $row;

            $role =
                "seller";

            $redirect =
                "/carconnect/seller/seller_dashboard.php";
        }
    }


    /* =========================
       BUYER
       ========================= */

    if (!$user) {

        $stmt =
            mysqli_prepare(
                $conn,
                "
                SELECT id,password
                FROM buyers
                WHERE email=?
                LIMIT 1
                "
            );

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $email
        );

        mysqli_stmt_execute(
            $stmt
        );

        $res =
            mysqli_stmt_get_result(
                $stmt
            );

        $row =
            mysqli_fetch_assoc(
                $res
            );

        mysqli_stmt_close(
            $stmt
        );


        if (
            $row &&
            password_verify(
                $password,
                $row['password']
            )
        ) {

            $user =
                $row;

            $role =
                "buyer";

            $redirect =
                "/carconnect/buyer/home.php";
        }
    }


    /* =========================
       SUCCESS
       ========================= */

    if ($user) {

        session_regenerate_id(
            true
        );

        $_SESSION['user_id'] =
            (int)$user['id'];

        $_SESSION['role'] =
            strtolower($role);


        header(
            "Location: $redirect"
        );

        exit();

    }
    else {

        $err =
            "Invalid email or password.";
    }
}


require_once __DIR__ .
    "/../includes/header.php";

?>


<h2 class="center mt-20">
Login
</h2>


<?php if (
    isset($_GET['registered']) &&
    $_GET['registered'] === '1'
): ?>

<div class="alert success">

Email verified successfully.
Your CarConnect account has been created.
Please login.

</div>

<?php endif; ?>


<?php if ($err): ?>

<div class="alert">

<?php echo e($err); ?>

</div>

<?php endif; ?>


<form
method="POST"
class="form"
>


<label>Email</label>

<input
class="input"
type="email"
name="email"
required
>


<label>Password</label>

<input
class="input"
type="password"
name="password"
required
>


<div class="center mt-20">

<button
class="btn primary"
>

Login

</button>

</div>


<p class="center mt-20 muted">

Don't have an account?

<a
href="/carconnect/auth/register.php"
style="
color:var(--primary);
font-weight:700;
"
>

Register here

</a>

</p>


</form>


<?php
require_once __DIR__ .
    "/../includes/footer.php";
?>