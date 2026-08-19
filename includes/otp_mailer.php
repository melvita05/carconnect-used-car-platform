<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/mail_config.php';


function sendCarConnectOTP(
    string $email,
    string $name,
    string $otp
): bool {

    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();

        $mail->Host =
            CARCONNECT_MAIL_HOST;

        $mail->SMTPAuth = true;

        $mail->Username =
            CARCONNECT_MAIL_USERNAME;

        /*
         * Gmail App Password may contain spaces when copied.
         * Remove spaces automatically.
         */
        $mail->Password =
            str_replace(
                ' ',
                '',
                CARCONNECT_MAIL_APP_PASSWORD
            );

        $mail->SMTPSecure =
            PHPMailer::ENCRYPTION_STARTTLS;

        $mail->Port =
            CARCONNECT_MAIL_PORT;

        $mail->CharSet =
            'UTF-8';

        $mail->setFrom(
            CARCONNECT_MAIL_USERNAME,
            CARCONNECT_MAIL_FROM_NAME
        );

        $mail->addAddress(
            $email,
            $name
        );

        $mail->isHTML(true);

        $mail->Subject =
            'CarConnect Email Verification OTP';

        $safeName =
            htmlspecialchars(
                $name,
                ENT_QUOTES,
                'UTF-8'
            );

        $safeOtp =
            htmlspecialchars(
                $otp,
                ENT_QUOTES,
                'UTF-8'
            );

        $mail->Body = "
        <div style='
            max-width:560px;
            margin:auto;
            padding:35px;
            font-family:Arial,sans-serif;
            border:1px solid #e5e7eb;
            border-radius:18px;
            background:#ffffff;
        '>

            <h2 style='
                margin:0 0 25px;
                color:#2563eb;
            '>
                CarConnect
            </h2>

            <p>
                Hello <strong>{$safeName}</strong>,
            </p>

            <p>
                Thank you for registering with CarConnect.
            </p>

            <p>
                Use the OTP below to verify your email address.
            </p>

            <div style='
                margin:30px 0;
                padding:20px;
                text-align:center;
                border-radius:12px;
                background:#eff6ff;
                color:#1d4ed8;
                font-size:36px;
                font-weight:700;
                letter-spacing:10px;
            '>
                {$safeOtp}
            </div>

            <p>
                This OTP will expire in
                <strong>5 minutes</strong>.
            </p>

            <p style='
                color:#6b7280;
                margin-top:30px;
                font-size:13px;
            '>
                If you did not create a CarConnect account,
                you can safely ignore this email.
            </p>

        </div>
        ";

        $mail->AltBody =
            "Hello {$name},\n\n" .
            "Your CarConnect email verification OTP is: {$otp}\n\n" .
            "The OTP expires in 5 minutes.";

        $mail->send();

        return true;

    }
    catch (Exception $e) {

        error_log(
            'CarConnect OTP mail error: ' .
            $mail->ErrorInfo
        );

        return false;
    }
}

?>