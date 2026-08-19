<?php

session_start();

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/otp_helpers.php";
require_once __DIR__ . "/../includes/otp_mailer.php";


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


$email =
    $pending['email'];

$name =
    $pending['name'];

$role =
    $pending['role'];


$record =
    cc_get_otp_record(
        $conn,
        $email,
        $role
    );


if (
    $record &&
    !empty($record['resend_after']) &&
    strtotime(
        $record['resend_after']
    ) > time()
) {

    $seconds =
        strtotime(
            $record['resend_after']
        ) -
        time();


    $_SESSION['otp_message'] =
        "Please wait " .
        $seconds .
        " second(s) before requesting another OTP.";


    header(
        "Location: /carconnect/auth/verify_otp.php"
    );

    exit();
}


$otp =
    cc_generate_otp();


if (
    !sendCarConnectOTP(
        $email,
        $name,
        $otp
    )
) {

    $_SESSION['otp_message'] =
        "Unable to send OTP. Please try again.";

    header(
        "Location: /carconnect/auth/verify_otp.php"
    );

    exit();
}


if (
    !cc_save_otp(
        $conn,
        $email,
        $role,
        $otp
    )
) {

    $_SESSION['otp_message'] =
        "Could not save the new OTP.";

    header(
        "Location: /carconnect/auth/verify_otp.php"
    );

    exit();
}


$_SESSION['otp_message'] =
    "A new OTP has been sent to your email.";


header(
    "Location: /carconnect/auth/verify_otp.php"
);

exit();

?>