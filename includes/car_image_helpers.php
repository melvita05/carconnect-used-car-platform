<?php

define('CC_MAX_CAR_IMAGES', 10);
define('CC_MAX_CAR_IMAGE_SIZE', 5 * 1024 * 1024);
define('CC_DEFAULT_CAR_IMAGE', '/carconnect/assets/images/default_car.jpg');


function cc_collect_car_uploads(array $files): array
{
    $uploads = [];

    if (
        empty($files) ||
        !isset($files['name']) ||
        !is_array($files['name'])
    ) {
        return [];
    }

    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    $total = count($files['name']);

    for ($i = 0; $i < $total; $i++) {

        $error = $files['error'][$i] ?? UPLOAD_ERR_NO_FILE;

        if ($error === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        if ($error !== UPLOAD_ERR_OK) {
            throw new Exception(
                'Image ' . ($i + 1) . ' upload failed.'
            );
        }

        $name = $files['name'][$i] ?? '';
        $tmp  = $files['tmp_name'][$i] ?? '';
        $size = (int)($files['size'][$i] ?? 0);

        $ext = strtolower(
            pathinfo($name, PATHINFO_EXTENSION)
        );

        if (!in_array($ext, $allowed, true)) {
            throw new Exception(
                'Only JPG, JPEG, PNG and WEBP images are allowed.'
            );
        }

        if ($size > CC_MAX_CAR_IMAGE_SIZE) {
            throw new Exception(
                'Each image must be maximum 5MB.'
            );
        }

        if (!is_uploaded_file($tmp)) {
            throw new Exception(
                'Invalid uploaded image detected.'
            );
        }

        $uploads[] = [
            'tmp' => $tmp,
            'ext' => $ext
        ];
    }

    if (count($uploads) > CC_MAX_CAR_IMAGES) {
        throw new Exception(
            'Maximum 10 images are allowed per car.'
        );
    }

    return $uploads;
}


function cc_store_car_images(
    mysqli $conn,
    int $carId,
    array $uploads,
    array &$movedFiles
): array {

    if (!$uploads) {
        return [];
    }

    $uploadDir = __DIR__ . '/../uploads/';

    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            throw new Exception(
                'Could not create uploads folder.'
            );
        }
    }

    $stmt = mysqli_prepare(
        $conn,
        "
        INSERT INTO car_images
        (car_id, image_path)
        VALUES (?,?)
        "
    );

    if (!$stmt) {
        throw new Exception(
            'Could not prepare image database query.'
        );
    }

    $savedPaths = [];

    foreach ($uploads as $upload) {

        $newName =
            'car_' .
            bin2hex(random_bytes(12)) .
            '.' .
            $upload['ext'];

        $absolute =
            $uploadDir . $newName;

        $relative =
            '/carconnect/uploads/' . $newName;

        if (
            !move_uploaded_file(
                $upload['tmp'],
                $absolute
            )
        ) {
            throw new Exception(
                'One of the car images could not be uploaded.'
            );
        }

        $movedFiles[] = $absolute;

        mysqli_stmt_bind_param(
            $stmt,
            'is',
            $carId,
            $relative
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(
                'Could not save car image information.'
            );
        }

        $savedPaths[] = $relative;
    }

    mysqli_stmt_close($stmt);

    return $savedPaths;
}


function cc_fetch_car_image_rows(
    mysqli $conn,
    int $carId
): array {

    $stmt = mysqli_prepare(
        $conn,
        "
        SELECT id, car_id, image_path, created_at
        FROM car_images
        WHERE car_id=?
        ORDER BY id ASC
        "
    );

    if (!$stmt) {
        return [];
    }

    mysqli_stmt_bind_param(
        $stmt,
        'i',
        $carId
    );

    mysqli_stmt_execute($stmt);

    $result =
        mysqli_stmt_get_result($stmt);

    $rows = [];

    while (
        $row =
        mysqli_fetch_assoc($result)
    ) {
        $rows[] = $row;
    }

    mysqli_stmt_close($stmt);

    return $rows;
}


function cc_build_car_gallery(
    mysqli $conn,
    int $carId,
    ?string $coverImage
): array {

    $gallery = [];

    $rows =
        cc_fetch_car_image_rows(
            $conn,
            $carId
        );

    if (
        !empty($coverImage) &&
        $coverImage !== CC_DEFAULT_CAR_IMAGE
    ) {
        $gallery[] = $coverImage;
    }

    foreach ($rows as $row) {

        $path = $row['image_path'];

        if (
            !empty($path) &&
            !in_array(
                $path,
                $gallery,
                true
            )
        ) {
            $gallery[] = $path;
        }
    }

    if (!$gallery) {

        if (!empty($coverImage)) {
            $gallery[] = $coverImage;
        } else {
            $gallery[] =
                CC_DEFAULT_CAR_IMAGE;
        }
    }

    return $gallery;
}


function cc_delete_uploaded_car_file(
    string $webPath
): void {

    $prefix =
        '/carconnect/uploads/';

    if (
        strpos($webPath, $prefix) !== 0
    ) {
        return;
    }

    $absolute =
        __DIR__ .
        '/../uploads/' .
        basename($webPath);

    if (is_file($absolute)) {
        @unlink($absolute);
    }
}