<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("seller");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/car_image_helpers.php";
require_once __DIR__ . "/../includes/header.php";

$id =
    (int)($_GET['id'] ?? 0);

$seller =
    (int)$_SESSION['user_id'];


/* ===================== */
/* FETCH CAR */
/* ===================== */

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
    $seller
);

mysqli_stmt_execute($stmt);

$res =
    mysqli_stmt_get_result($stmt);

$car =
    mysqli_fetch_assoc($res);

mysqli_stmt_close($stmt);


if (!$car) {

    echo
        "<div class='alert'>
        Car not found.
        </div>";

    require_once __DIR__ .
        "/../includes/footer.php";

    exit();
}


/* ===================== */
/* DELETE CAR */
/* ===================== */

if (isset($_POST['delete'])) {

    $imageRows =
        cc_fetch_car_image_rows(
            $conn,
            $id
        );

    $pathsToDelete = [];

    foreach ($imageRows as $image) {
        $pathsToDelete[] =
            $image['image_path'];
    }

    if (
        !empty($car['image_path'])
    ) {
        $pathsToDelete[] =
            $car['image_path'];
    }

    $deleteStmt =
        mysqli_prepare(
            $conn,
            "
            DELETE FROM car_listings
            WHERE id=? AND seller_id=?
            "
        );

    mysqli_stmt_bind_param(
        $deleteStmt,
        "ii",
        $id,
        $seller
    );

    mysqli_stmt_execute(
        $deleteStmt
    );

    $deleted =
        mysqli_stmt_affected_rows(
            $deleteStmt
        );

    mysqli_stmt_close(
        $deleteStmt
    );

    if ($deleted > 0) {

        foreach (
            array_unique($pathsToDelete)
            as $path
        ) {
            cc_delete_uploaded_car_file(
                $path
            );
        }

        header(
            "Location: seller_dashboard.php?msg=deleted"
        );

        exit();
    }
}


$gallery =
    cc_build_car_gallery(
        $conn,
        $id,
        $car['image_path']
    );


/* CATEGORY */

$categoryName = "N/A";

$catStmt =
    mysqli_prepare(
        $conn,
        "
        SELECT name
        FROM car_categories
        WHERE id=?
        LIMIT 1
        "
    );

mysqli_stmt_bind_param(
    $catStmt,
    "i",
    $car['category_id']
);

mysqli_stmt_execute(
    $catStmt
);

$catRes =
    mysqli_stmt_get_result(
        $catStmt
    );

$cat =
    mysqli_fetch_assoc(
        $catRes
    );

if ($cat) {
    $categoryName =
        $cat['name'];
}

mysqli_stmt_close(
    $catStmt
);
?>

<style>

.car-gallery-main{
    width:100%;
    height:430px;
    object-fit:cover;
    border-radius:12px 12px 0 0;
    display:block;
}

.car-gallery-thumbs{
    display:flex;
    gap:10px;
    overflow-x:auto;
    padding:12px;
    background:#fff;
}

.car-gallery-thumb{
    width:110px;
    height:78px;
    min-width:110px;
    padding:0;
    border:3px solid transparent;
    border-radius:10px;
    overflow:hidden;
    cursor:pointer;
    background:#fff;
}

.car-gallery-thumb img{
    width:100%;
    height:100%;
    object-fit:cover;
}

.car-gallery-thumb.active{
    border-color:#00d2ff;
}

</style>


<h1 style="text-align:center;margin-bottom:20px">

🚗
<?php
echo e(
    $car['make'] .
    " " .
    $car['model']
);
?>

</h1>


<div
class="card"
style="max-width:900px;margin:auto"
>


<img
id="sellerMainCarImage"
class="car-gallery-main"
src="<?php echo e($gallery[0]); ?>"
alt="Car Image"
>


<?php if (count($gallery) > 1): ?>

<div class="car-gallery-thumbs">

<?php
foreach (
    $gallery
    as $index => $image
):
?>

<button
type="button"
class="car-gallery-thumb <?php echo $index === 0 ? 'active' : ''; ?>"
data-image="<?php echo e($image); ?>"
>

<img
src="<?php echo e($image); ?>"
alt="Car Thumbnail"
>

</button>

<?php endforeach; ?>

</div>

<?php endif; ?>


<div class="p">


<h2 style="color:#00d2ff;margin:5px 0">

₹<?php echo number_format((float)$car['price']); ?>

</h2>


<p>

<b>Category:</b>

<?php echo e($categoryName); ?>

</p>


<table class="table">


<tr>
<td><b>Year</b></td>
<td><?php echo e($car['year']); ?></td>
</tr>


<tr>
<td><b>Price</b></td>
<td>
₹<?php echo number_format((float)$car['price']); ?>
</td>
</tr>


<tr>
<td><b>Mileage</b></td>
<td>
<?php echo number_format((int)$car['mileage']); ?> km
</td>
</tr>


<tr>
<td><b>Fuel</b></td>
<td><?php echo e($car['fuel_type']); ?></td>
</tr>


<tr>
<td><b>Transmission</b></td>
<td><?php echo e($car['transmission']); ?></td>
</tr>


<tr>
<td><b>Color</b></td>
<td><?php echo e($car['color']); ?></td>
</tr>


<tr>
<td><b>Owner</b></td>
<td><?php echo e($car['owner_type']); ?></td>
</tr>


<tr>
<td><b>Body Type</b></td>
<td><?php echo e($car['body_type']); ?></td>
</tr>


<tr>
<td><b>Seats</b></td>
<td>
<?php echo e($car['seating_capacity']); ?>
</td>
</tr>


<tr>
<td><b>Location</b></td>
<td><?php echo e($car['location']); ?></td>
</tr>


</table>


<p style="line-height:1.6">

<?php
echo nl2br(
    e($car['description'])
);
?>

</p>


<p class="muted">

Status:
<b><?php echo e($car['status']); ?></b>

</p>


<div
style="
margin-top:20px;
display:flex;
gap:10px;
flex-wrap:wrap;
"
>

<a
class="btn"
href="edit_car.php?id=<?php echo (int)$car['id']; ?>"
>
✏️ Edit
</a>


<form
method="POST"
onsubmit="return confirm('Delete this car permanently?')"
>

<button
type="submit"
name="delete"
value="1"
class="btn"
style="
background:#ff4d4d;
color:white;
"
>
🗑️ Delete
</button>

</form>

</div>


</div>

</div>


<script>

document
.querySelectorAll('.car-gallery-thumb')
.forEach(function(button){

    button.addEventListener(
        'click',
        function(){

            const image =
                this.getAttribute(
                    'data-image'
                );

            document
                .getElementById(
                    'sellerMainCarImage'
                )
                .src = image;

            document
                .querySelectorAll(
                    '.car-gallery-thumb'
                )
                .forEach(function(item){
                    item.classList.remove(
                        'active'
                    );
                });

            this.classList.add(
                'active'
            );
        }
    );
});

</script>


<?php
require_once __DIR__ .
    "/../includes/footer.php";
?>