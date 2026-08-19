<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("seller");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/car_image_helpers.php";
require_once __DIR__ . "/../includes/header.php";

$sellerId =
    (int)$_SESSION['user_id'];

$id =
    (int)($_GET['id'] ?? 0);

$stmt =
    mysqli_prepare(
        $conn,
        "
        SELECT *
        FROM car_listings
        WHERE id=? AND seller_id=?
        LIMIT 1
        "
    );

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $id,
    $sellerId
);

mysqli_stmt_execute($stmt);

$res =
    mysqli_stmt_get_result($stmt);

$car =
    mysqli_fetch_assoc($res);

mysqli_stmt_close($stmt);

if (!$car) {

    echo
        '<div class="alert">
        Car not found.
        </div>';

    require_once __DIR__ .
        "/../includes/footer.php";

    exit();
}

$images =
    cc_fetch_car_image_rows(
        $conn,
        $id
    );

$err = "";
$ok  = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $brand =
        trim($_POST['brand'] ?? '');

    $model =
        trim($_POST['model'] ?? '');

    $year =
        (int)($_POST['year'] ?? 0);

    $price =
        (float)($_POST['price'] ?? 0);

    $mileage =
        (int)($_POST['mileage'] ?? 0);

    $fuel =
        trim($_POST['fuel_type'] ?? '');

    $trans =
        trim($_POST['transmission'] ?? '');

    $color =
        trim($_POST['color'] ?? '');

    $ownerType =
        trim($_POST['owner_type'] ?? '');

    $bodyType =
        trim($_POST['body_type'] ?? '');

    $seating =
        (int)($_POST['seating_capacity'] ?? 0);

    $location =
        trim($_POST['location'] ?? '');

    $category_id =
        (int)($_POST['category_id'] ?? 0);

    $desc =
        trim($_POST['description'] ?? '');

    if (
        $brand === "" ||
        $model === "" ||
        $year <= 0 ||
        $price <= 0 ||
        $category_id <= 0 ||
        $color === "" ||
        $ownerType === "" ||
        $bodyType === "" ||
        $seating <= 0 ||
        $location === ""
    ) {
        $err =
            "Please fill all required fields.";
    }

    $deleteIds = [];

    if (
        isset($_POST['delete_images']) &&
        is_array($_POST['delete_images'])
    ) {

        foreach (
            $_POST['delete_images']
            as $deleteId
        ) {

            $deleteId =
                (int)$deleteId;

            if ($deleteId > 0) {
                $deleteIds[] =
                    $deleteId;
            }
        }

        $deleteIds =
            array_values(
                array_unique(
                    $deleteIds
                )
            );
    }

    $imageMap = [];

    foreach ($images as $image) {

        $imageMap[
            (int)$image['id']
        ] =
            $image['image_path'];
    }

    $validDeleteIds = [];
    $deletePaths = [];

    foreach ($deleteIds as $deleteId) {

        if (
            isset(
                $imageMap[$deleteId]
            )
        ) {

            $validDeleteIds[] =
                $deleteId;

            $deletePaths[] =
                $imageMap[$deleteId];
        }
    }

    $uploads = [];

    if ($err === "") {

        try {

            $uploads =
                cc_collect_car_uploads(
                    $_FILES['images'] ?? []
                );

        } catch (Throwable $e) {

            $err =
                $e->getMessage();
        }
    }

    $remainingOld =
        count($images) -
        count($validDeleteIds);

    if (
        $err === "" &&
        ($remainingOld + count($uploads)) >
        CC_MAX_CAR_IMAGES
    ) {
        $err =
            "Maximum 10 images are allowed per car.";
    }

    if ($err === "") {

        $movedFiles = [];

        try {

            mysqli_begin_transaction(
                $conn
            );

            if ($validDeleteIds) {

                $deleteStmt =
                    mysqli_prepare(
                        $conn,
                        "
                        DELETE FROM car_images
                        WHERE id=? AND car_id=?
                        "
                    );

                foreach (
                    $validDeleteIds
                    as $deleteId
                ) {

                    mysqli_stmt_bind_param(
                        $deleteStmt,
                        "ii",
                        $deleteId,
                        $id
                    );

                    if (
                        !mysqli_stmt_execute(
                            $deleteStmt
                        )
                    ) {
                        throw new Exception(
                            "Could not remove image."
                        );
                    }
                }

                mysqli_stmt_close(
                    $deleteStmt
                );
            }

            cc_store_car_images(
                $conn,
                $id,
                $uploads,
                $movedFiles
            );

            $remainingImages =
                cc_fetch_car_image_rows(
                    $conn,
                    $id
                );

            $remainingPaths = [];

            foreach (
                $remainingImages
                as $image
            ) {

                $remainingPaths[] =
                    $image['image_path'];
            }

            $currentCover =
                $car['image_path'];

            if (
                in_array(
                    $currentCover,
                    $remainingPaths,
                    true
                )
            ) {

                $imagePath =
                    $currentCover;

            } elseif ($remainingPaths) {

                $imagePath =
                    $remainingPaths[0];

            } elseif (
                empty($images) &&
                !empty($currentCover) &&
                $currentCover !==
                CC_DEFAULT_CAR_IMAGE
            ) {

                $imagePath =
                    $currentCover;

            } else {

                $imagePath =
                    CC_DEFAULT_CAR_IMAGE;
            }

            $stmt2 =
                mysqli_prepare(
                    $conn,
                    "
                    UPDATE car_listings
                    SET
                        category_id=?,
                        make=?,
                        model=?,
                        year=?,
                        price=?,
                        mileage=?,
                        fuel_type=?,
                        transmission=?,
                        color=?,
                        owner_type=?,
                        body_type=?,
                        seating_capacity=?,
                        location=?,
                        description=?,
                        image_path=?,
                        status='pending'
                    WHERE
                        id=? AND
                        seller_id=?
                    "
                );

            mysqli_stmt_bind_param(
                $stmt2,
                "issidisssssisssii",
                $category_id,
                $brand,
                $model,
                $year,
                $price,
                $mileage,
                $fuel,
                $trans,
                $color,
                $ownerType,
                $bodyType,
                $seating,
                $location,
                $desc,
                $imagePath,
                $id,
                $sellerId
            );

            if (
                !mysqli_stmt_execute(
                    $stmt2
                )
            ) {
                throw new Exception(
                    "Update failed."
                );
            }

            mysqli_stmt_close($stmt2);

            mysqli_commit($conn);

            foreach (
                array_unique($deletePaths)
                as $path
            ) {

                if (
                    $path !== $imagePath
                ) {
                    cc_delete_uploaded_car_file(
                        $path
                    );
                }
            }

            $ok =
                "Updated successfully. Waiting for admin approval.";

            $refreshStmt =
                mysqli_prepare(
                    $conn,
                    "
                    SELECT *
                    FROM car_listings
                    WHERE id=? AND seller_id=?
                    LIMIT 1
                    "
                );

            mysqli_stmt_bind_param(
                $refreshStmt,
                "ii",
                $id,
                $sellerId
            );

            mysqli_stmt_execute(
                $refreshStmt
            );

            $refreshResult =
                mysqli_stmt_get_result(
                    $refreshStmt
                );

            $car =
                mysqli_fetch_assoc(
                    $refreshResult
                );

            mysqli_stmt_close(
                $refreshStmt
            );

            $images =
                cc_fetch_car_image_rows(
                    $conn,
                    $id
                );

        } catch (Throwable $e) {

            mysqli_rollback($conn);

            foreach (
                $movedFiles
                as $file
            ) {

                if (is_file($file)) {
                    @unlink($file);
                }
            }

            $err =
                $e->getMessage();
        }
    }
}
?>

<h1>✏️ Edit Car</h1>

<?php if ($err): ?>

<div class="alert">
<?php echo e($err); ?>
</div>

<?php endif; ?>


<?php if ($ok): ?>

<div class="alert success">
<?php echo e($ok); ?>
</div>

<?php endif; ?>


<form
    class="form"
    method="POST"
    enctype="multipart/form-data"
>


<label>Category</label>

<select
    class="input"
    name="category_id"
    required
>

<option value="">
Select Category
</option>

<?php
$resCat =
    mysqli_query(
        $conn,
        "
        SELECT *
        FROM car_categories
        ORDER BY name
        "
    );

while (
    $cat =
    mysqli_fetch_assoc($resCat)
) {

    $selected =
        ((int)$car['category_id'] ===
        (int)$cat['id'])
        ? "selected"
        : "";

    echo
        "<option value='" .
        (int)$cat['id'] .
        "' $selected>" .
        e($cat['name']) .
        "</option>";
}
?>

</select>


<label>Brand</label>

<input
    class="input"
    list="brandList"
    name="brand"
    value="<?php echo e($car['make']); ?>"
    required
>

<datalist id="brandList">
<option value="Maruti">
<option value="Tata">
<option value="Mahindra">
<option value="Hyundai">
<option value="Honda">
<option value="Toyota">
<option value="Kia">
<option value="BMW">
<option value="Audi">
</datalist>


<label>Model</label>

<input
    class="input"
    type="text"
    name="model"
    value="<?php echo e($car['model']); ?>"
    required
>


<label>Year</label>

<input
    class="input"
    type="number"
    name="year"
    value="<?php echo e($car['year']); ?>"
    required
>


<label>Price (₹)</label>

<input
    class="input"
    type="number"
    step="0.01"
    name="price"
    value="<?php echo e($car['price']); ?>"
    required
>


<label>Mileage</label>

<input
    class="input"
    type="number"
    name="mileage"
    value="<?php echo e($car['mileage']); ?>"
>


<label>Fuel Type</label>

<select
    class="input"
    name="fuel_type"
>

<?php
foreach (
    ["Petrol","Diesel","CNG","Electric"]
    as $fuel
) {

    $selected =
        ($car['fuel_type'] === $fuel)
        ? "selected"
        : "";

    echo
        "<option $selected>" .
        e($fuel) .
        "</option>";
}
?>

</select>


<label>Transmission</label>

<select
    class="input"
    name="transmission"
>

<?php
foreach (
    ["Manual","Automatic"]
    as $trans
) {

    $selected =
        ($car['transmission'] === $trans)
        ? "selected"
        : "";

    echo
        "<option $selected>" .
        e($trans) .
        "</option>";
}
?>

</select>


<label>Color</label>

<select
    class="input"
    name="color"
    required
>

<?php
foreach (
    [
        "White",
        "Black",
        "Silver",
        "Grey",
        "Blue",
        "Red",
        "Brown",
        "Green"
    ]
    as $color
) {

    $selected =
        ($car['color'] === $color)
        ? "selected"
        : "";

    echo
        "<option $selected>" .
        e($color) .
        "</option>";
}
?>

</select>


<label>Owner Type</label>

<select
    class="input"
    name="owner_type"
    required
>

<?php
foreach (
    [
        "First Owner",
        "Second Owner",
        "Third Owner",
        "Fourth Owner"
    ]
    as $owner
) {

    $selected =
        ($car['owner_type'] === $owner)
        ? "selected"
        : "";

    echo
        "<option $selected>" .
        e($owner) .
        "</option>";
}
?>

</select>


<label>Body Type</label>

<select
    class="input"
    name="body_type"
    required
>

<?php
foreach (
    [
        "Hatchback",
        "Sedan",
        "SUV",
        "MUV",
        "Coupe",
        "Convertible"
    ]
    as $body
) {

    $selected =
        ($car['body_type'] === $body)
        ? "selected"
        : "";

    echo
        "<option $selected>" .
        e($body) .
        "</option>";
}
?>

</select>


<label>Seating Capacity</label>

<select
    class="input"
    name="seating_capacity"
    required
>

<?php
foreach ([2,4,5,6,7,8] as $seat) {

    $selected =
        ((int)$car['seating_capacity'] ===
        $seat)
        ? "selected"
        : "";

    echo
        "<option value='$seat' $selected>
        $seat
        </option>";
}
?>

</select>


<label>Location</label>

<input
    class="input"
    type="text"
    name="location"
    value="<?php echo e($car['location']); ?>"
    required
>


<label>Description</label>

<textarea
    class="input"
    name="description"
    rows="4"
><?php echo e($car['description']); ?></textarea>


<h3 style="margin-top:25px;">
Current Images
</h3>


<?php if ($images): ?>

<div style="
display:grid;
grid-template-columns:repeat(auto-fill,minmax(150px,1fr));
gap:15px;
margin:15px 0 25px;
">

<?php foreach ($images as $image): ?>

<label style="
background:#fff;
border:1px solid #ddd;
border-radius:12px;
padding:8px;
cursor:pointer;
">

<img
src="<?php echo e($image['image_path']); ?>"
style="
width:100%;
height:110px;
object-fit:cover;
border-radius:8px;
"
>

<div style="margin-top:8px;">

<input
    type="checkbox"
    name="delete_images[]"
    value="<?php echo (int)$image['id']; ?>"
>

Remove Image

</div>

</label>

<?php endforeach; ?>

</div>

<?php else: ?>

<p class="muted">
This is an older single-image listing.
</p>

<?php
if (
    !empty($car['image_path']) &&
    $car['image_path'] !==
    CC_DEFAULT_CAR_IMAGE
):
?>

<img
src="<?php echo e($car['image_path']); ?>"
style="
width:180px;
height:120px;
object-fit:cover;
border-radius:10px;
margin-bottom:20px;
"
>

<?php endif; ?>

<?php endif; ?>


<label>Add More Images</label>

<input
    class="input"
    type="file"
    name="images[]"
    accept=".jpg,.jpeg,.png,.webp"
    multiple
>

<small>
Maximum 10 images total.
Maximum 5MB per image.
Tick images above to remove them.
</small>

<br>

<button
    class="btn primary"
    style="margin-top:15px"
>
Update Car
</button>

</form>

<?php
require_once __DIR__ .
    "/../includes/footer.php";
?>