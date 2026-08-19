<?php

session_start();

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/otp_helpers.php";
require_once __DIR__ . "/../includes/otp_mailer.php";

$err = "";


/* =========================================================
   REGISTRATION PROCESS
   ========================================================= */

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {

    $name =
        trim($_POST['name'] ?? '');

    $phone =
        trim($_POST['phone'] ?? '');

    $email =
        strtolower(
            trim($_POST['email'] ?? '')
        );

    $password =
        $_POST['password'] ?? '';

    $role =
        strtolower(
            trim($_POST['role'] ?? '')
        );


    if (
        $name === "" ||
        $phone === "" ||
        $email === "" ||
        $password === "" ||
        $role === ""
    ) {

        $err =
            "All fields are required.";

    }
    elseif (
        !in_array(
            $role,
            ['buyer','seller'],
            true
        )
    ) {

        $err =
            "Invalid account role.";

    }
    elseif (
        !preg_match(
            '/^[6-9][0-9]{9}$/',
            $phone
        )
    ) {

        $err =
            "Please enter a valid 10-digit mobile number.";

    }
    elseif (
        !filter_var(
            $email,
            FILTER_VALIDATE_EMAIL
        )
    ) {

        $err =
            "Please enter a valid email address.";

    }
    elseif (
        strlen($password) < 6
    ) {

        $err =
            "Password must contain at least 6 characters.";

    }
    elseif (
        cc_email_exists(
            $conn,
            $email
        )
    ) {

        $err =
            "Email already registered. Please login.";

    }
    else {

        try {

            /*
             * Password is already hashed before storing
             * temporary registration information.
             * Plain password is NOT stored in session.
             */

            $passwordHash =
                password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );


            $otp =
                cc_generate_otp();


            /*
             * Send email first.
             */

            if (
                !sendCarConnectOTP(
                    $email,
                    $name,
                    $otp
                )
            ) {

                throw new Exception(
                    "OTP could not be sent. Please check the email address and try again."
                );
            }


            /*
             * Store OTP information.
             */

            if (
                !cc_save_otp(
                    $conn,
                    $email,
                    $role,
                    $otp
                )
            ) {

                throw new Exception(
                    "Could not create OTP verification."
                );
            }


            /*
             * Store registration data temporarily.
             */

            $_SESSION[
                'pending_registration'
            ] = [

                'name' =>
                    $name,

                'phone' =>
                    $phone,

                'email' =>
                    $email,

                'password_hash' =>
                    $passwordHash,

                'role' =>
                    $role
            ];


            header(
                "Location: /carconnect/auth/verify_otp.php"
            );

            exit();

        }
        catch (Throwable $e) {

            $err =
                $e->getMessage();
        }
    }
}


/*
 * Header is loaded AFTER redirect logic.
 */

require_once __DIR__ .
    "/../includes/header.php";

?>


<h2 class="center mt-20">
Create Account
</h2>


<?php if ($err): ?>

<div class="alert">

<?php echo e($err); ?>

</div>

<?php endif; ?>


<form
method="POST"
class="form"
autocomplete="off"
>


<label>Select Role</label>

<select
name="role"
class="input"
required
>

<option value="">
-- Select --
</option>

<option
value="buyer"
<?php
echo (
    ($_POST['role'] ?? '') ===
    'buyer'
)
? 'selected'
: '';
?>
>
Buyer
</option>

<option
value="seller"
<?php
echo (
    ($_POST['role'] ?? '') ===
    'seller'
)
? 'selected'
: '';
?>
>
Seller
</option>

</select>


<label>Name</label>

<input
class="input"
name="name"
value="<?php echo e($_POST['name'] ?? ''); ?>"
required
>


<label>Phone Number</label>

<input
type="tel"
name="phone"
class="input"
maxlength="10"
pattern="[6-9]{1}[0-9]{9}"
title="Enter a valid 10-digit mobile number"
placeholder="9876543210"
value="<?php echo e($_POST['phone'] ?? ''); ?>"
required
>


<label>Email</label>

<input
class="input"
type="email"
name="email"
value="<?php echo e($_POST['email'] ?? ''); ?>"
required
>


<label>Password</label>

<input
class="input"
type="password"
name="password"
minlength="6"
required
>


<div class="center mt-20">

<button class="btn primary">

Send OTP

</button>

</div>


<p class="center mt-20 muted">

Already have an account?

<a
href="/carconnect/auth/login.php"
style="
color:var(--primary);
font-weight:700;
"
>
Login here
</a>

</p>


</form>


<?php
require_once __DIR__ .
    "/../includes/footer.php";
?>