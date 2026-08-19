<?php

session_start();

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/otp_helpers.php";


if (
    empty(
        $_SESSION[
            'pending_registration'
        ]
    )
) {

    header(
        "Location: /carconnect/auth/register.php"
    );

    exit();
}


$pending =
    $_SESSION[
        'pending_registration'
    ];


$name =
    $pending['name'];

$phone =
    $pending['phone'];

$email =
    $pending['email'];

$passwordHash =
    $pending['password_hash'];

$role =
    $pending['role'];


$err = "";


/* =========================================================
   VERIFY OTP
   ========================================================= */

if (
    $_SERVER['REQUEST_METHOD'] ===
    'POST'
) {

    $enteredOtp =
        trim(
            $_POST['otp'] ?? ''
        );


    if (
        !preg_match(
            '/^[0-9]{6}$/',
            $enteredOtp
        )
    ) {

        $err =
            "Please enter the 6-digit OTP.";

    }
    else {

        $record =
            cc_get_otp_record(
                $conn,
                $email,
                $role
            );


        if (!$record) {

            $err =
                "OTP not found. Please request a new OTP.";

        }
        elseif (
            (int)$record['attempts'] >=
            CARCONNECT_OTP_MAX_ATTEMPTS
        ) {

            $err =
                "Too many incorrect attempts. Please resend OTP.";

        }
        elseif (
            strtotime(
                $record['expires_at']
            ) < time()
        ) {

            $err =
                "OTP has expired. Please resend OTP.";

        }
        elseif (
            !password_verify(
                $enteredOtp,
                $record['otp_hash']
            )
        ) {

            cc_increment_otp_attempt(
                $conn,
                (int)$record['id']
            );

            $remaining =
                CARCONNECT_OTP_MAX_ATTEMPTS -
                ((int)$record['attempts'] + 1);

            if ($remaining > 0) {

                $err =
                    "Incorrect OTP. " .
                    $remaining .
                    " attempt(s) remaining.";

            }
            else {

                $err =
                    "Too many incorrect attempts. Please resend OTP.";
            }

        }
        else {

            try {

                mysqli_begin_transaction(
                    $conn
                );


                /*
                 * Final duplicate email check.
                 */

                if (
                    cc_email_exists(
                        $conn,
                        $email
                    )
                ) {

                    throw new Exception(
                        "This email is already registered."
                    );
                }


                if (
                    $role === 'buyer'
                ) {

                    $stmt =
                        mysqli_prepare(
                            $conn,
                            "
                            INSERT INTO buyers
                            (
                                name,
                                phone,
                                email,
                                password
                            )
                            VALUES
                            (?,?,?,?)
                            "
                        );

                }
                elseif (
                    $role === 'seller'
                ) {

                    $stmt =
                        mysqli_prepare(
                            $conn,
                            "
                            INSERT INTO sellers
                            (
                                name,
                                phone,
                                email,
                                password
                            )
                            VALUES
                            (?,?,?,?)
                            "
                        );

                }
                else {

                    throw new Exception(
                        "Invalid registration role."
                    );
                }


                mysqli_stmt_bind_param(
                    $stmt,
                    "ssss",
                    $name,
                    $phone,
                    $email,
                    $passwordHash
                );


                if (
                    !mysqli_stmt_execute(
                        $stmt
                    )
                ) {

                    throw new Exception(
                        "Account could not be created."
                    );
                }


                mysqli_stmt_close(
                    $stmt
                );


                cc_delete_otp(
                    $conn,
                    $email,
                    $role
                );


                mysqli_commit(
                    $conn
                );


                unset(
                    $_SESSION[
                        'pending_registration'
                    ]
                );


                header(
                    "Location: /carconnect/auth/login.php?registered=1"
                );

                exit();

            }
            catch (Throwable $e) {

                mysqli_rollback(
                    $conn
                );

                $err =
                    $e->getMessage();
            }
        }
    }
}


require_once __DIR__ .
    "/../includes/header.php";

?>


<style>

.otp-box{
    max-width:500px;
    margin:40px auto;
}

.otp-email{
    text-align:center;
    margin-bottom:20px;
}

.otp-input{
    text-align:center;
    font-size:28px;
    font-weight:700;
    letter-spacing:10px;
}

</style>


<div class="otp-box">


<h2 class="center">
Verify Your Email
</h2>


<p class="otp-email muted">

We sent a 6-digit OTP to

<br>

<strong>
<?php echo e($email); ?>
</strong>

</p>


<?php if (!empty($_SESSION['otp_message'])): ?>

<div class="alert success">

<?php
echo e($_SESSION['otp_message']);
unset($_SESSION['otp_message']);
?>

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


<label>
Enter OTP
</label>


<input
class="input otp-input"
type="text"
name="otp"
inputmode="numeric"
maxlength="6"
pattern="[0-9]{6}"
autocomplete="one-time-code"
placeholder="000000"
required
>


<div class="center mt-20">

<button
class="btn primary"
type="submit"
>

Verify OTP

</button>

</div>


<p class="center mt-20 muted">

Didn't receive the OTP?

<a
href="/carconnect/auth/resend_otp.php"
style="
color:var(--primary);
font-weight:700;
"
>

Resend OTP

</a>

</p>


<p class="center muted">

Wrong email?

<a
href="/carconnect/auth/register.php"
>

Register Again

</a>

</p>


</form>


</div>


<?php
require_once __DIR__ .
    "/../includes/footer.php";
?>