<?php

require_once __DIR__ . '/../config/mail_config.php';


function cc_email_exists(
    mysqli $conn,
    string $email
): bool {

    $stmt = mysqli_prepare(
        $conn,
        "
        SELECT id
        FROM buyers
        WHERE email=?
        LIMIT 1
        "
    );

    mysqli_stmt_bind_param(
        $stmt,
        's',
        $email
    );

    mysqli_stmt_execute($stmt);

    $result =
        mysqli_stmt_get_result($stmt);

    $exists =
        mysqli_fetch_assoc($result)
        ? true
        : false;

    mysqli_stmt_close($stmt);

    if ($exists) {
        return true;
    }


    $stmt = mysqli_prepare(
        $conn,
        "
        SELECT id
        FROM sellers
        WHERE email=?
        LIMIT 1
        "
    );

    mysqli_stmt_bind_param(
        $stmt,
        's',
        $email
    );

    mysqli_stmt_execute($stmt);

    $result =
        mysqli_stmt_get_result($stmt);

    $exists =
        mysqli_fetch_assoc($result)
        ? true
        : false;

    mysqli_stmt_close($stmt);

    return $exists;
}


function cc_generate_otp(): string
{
    return (string)random_int(
        100000,
        999999
    );
}


function cc_save_otp(
    mysqli $conn,
    string $email,
    string $role,
    string $otp
): bool {

    /*
     * Remove previous OTP for the same email + role.
     */
    $delete =
        mysqli_prepare(
            $conn,
            "
            DELETE FROM otp_verifications
            WHERE email=? AND role=?
            "
        );

    mysqli_stmt_bind_param(
        $delete,
        'ss',
        $email,
        $role
    );

    mysqli_stmt_execute($delete);

    mysqli_stmt_close($delete);


    $otpHash =
        password_hash(
            $otp,
            PASSWORD_DEFAULT
        );

    $expiresAt =
        date(
            'Y-m-d H:i:s',
            time() +
            CARCONNECT_OTP_EXPIRY_SECONDS
        );

    $resendAfter =
        date(
            'Y-m-d H:i:s',
            time() +
            CARCONNECT_OTP_RESEND_SECONDS
        );


    $stmt =
        mysqli_prepare(
            $conn,
            "
            INSERT INTO otp_verifications
            (
                email,
                role,
                otp_hash,
                expires_at,
                resend_after,
                attempts
            )
            VALUES
            (?,?,?,?,?,0)
            "
        );

    mysqli_stmt_bind_param(
        $stmt,
        'sssss',
        $email,
        $role,
        $otpHash,
        $expiresAt,
        $resendAfter
    );

    $success =
        mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);

    return $success;
}


function cc_get_otp_record(
    mysqli $conn,
    string $email,
    string $role
): ?array {

    $stmt =
        mysqli_prepare(
            $conn,
            "
            SELECT
                id,
                email,
                role,
                otp_hash,
                expires_at,
                resend_after,
                attempts,
                created_at
            FROM otp_verifications
            WHERE email=? AND role=?
            ORDER BY id DESC
            LIMIT 1
            "
        );

    mysqli_stmt_bind_param(
        $stmt,
        'ss',
        $email,
        $role
    );

    mysqli_stmt_execute($stmt);

    $result =
        mysqli_stmt_get_result($stmt);

    $record =
        mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    return $record ?: null;
}


function cc_increment_otp_attempt(
    mysqli $conn,
    int $otpId
): void {

    $stmt =
        mysqli_prepare(
            $conn,
            "
            UPDATE otp_verifications
            SET attempts=attempts+1
            WHERE id=?
            "
        );

    mysqli_stmt_bind_param(
        $stmt,
        'i',
        $otpId
    );

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
}


function cc_delete_otp(
    mysqli $conn,
    string $email,
    string $role
): void {

    $stmt =
        mysqli_prepare(
            $conn,
            "
            DELETE FROM otp_verifications
            WHERE email=? AND role=?
            "
        );

    mysqli_stmt_bind_param(
        $stmt,
        'ss',
        $email,
        $role
    );

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
}

?>