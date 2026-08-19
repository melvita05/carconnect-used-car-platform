<?php
require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/car_image_helpers.php";
require_once __DIR__ . "/../includes/header.php";

$id =
    (int)($_GET['id'] ?? 0);


$stmt =
    mysqli_prepare(
        $conn,
        "
        SELECT
            c.*,
            s.name AS seller_name,
            s.id AS seller_id,
            s.phone
        FROM car_listings c
        LEFT JOIN sellers s
        ON s.id=c.seller_id
        WHERE c.id=?
        LIMIT 1
        "
    );

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);

mysqli_stmt_execute(
    $stmt
);

$res =
    mysqli_stmt_get_result(
        $stmt
    );

$car =
    mysqli_fetch_assoc(
        $res
    );

mysqli_stmt_close(
    $stmt
);


if (!$car) {

    echo
        '<div class="alert">
        Car not found.
        </div>';

    require_once __DIR__ .
        "/../includes/footer.php";

    exit();
}


$gallery =
    cc_build_car_gallery(
        $conn,
        $id,
        $car['image_path']
    );
?>


<style>

body{
    background:#f4f7fb;
}

.car-details-container{
    max-width:1250px;
    margin:40px auto;
    padding:20px;
}

.car-header{
    text-align:center;
    margin-bottom:25px;
}

.car-header h1{
    font-size:38px;
    color:#111827;
    font-weight:700;
}

.car-layout{
    display:grid;
    grid-template-columns:2.2fr 1fr;
    gap:30px;
    align-items:start;
}

.car-image-card,
.info-card{
    background:#fff;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
}

.car-image{
    width:100%;
    height:520px;
    object-fit:cover;
    display:block;
}

.car-gallery{
    background:#fff;
    display:flex;
    gap:12px;
    padding:14px;
    overflow-x:auto;
}

.car-thumb{
    width:105px;
    height:76px;
    min-width:105px;
    border:3px solid transparent;
    border-radius:10px;
    overflow:hidden;
    cursor:pointer;
    padding:0;
    background:#fff;
}

.car-thumb img{
    width:100%;
    height:100%;
    object-fit:cover;
    display:block;
}

.car-thumb.active{
    border-color:#2563eb;
}

.car-content{
    padding:28px;
}

.price-box{
    background:linear-gradient(
        135deg,
        #2563eb,
        #1d4ed8
    );
    color:#fff;
    border-radius:15px;
    padding:25px;
    text-align:center;
    margin-bottom:25px;
}

.price-box p{
    margin:0;
    opacity:.9;
}

.price-box h2{
    margin:8px 0 0;
    font-size:36px;
}

.section-title{
    margin:30px 0 15px;
    font-size:22px;
    color:#111827;
    border-left:5px solid #2563eb;
    padding-left:12px;
}

.quick-specs,
.spec-table{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:15px;
}

.spec,
.spec-item{
    background:#f8fafc;
    border:1px solid #e5e7eb;
    border-radius:15px;
    padding:18px;
}

.spec b,
.spec-item strong{
    display:block;
    margin-top:6px;
    font-size:17px;
    color:#111827;
}

.seller-box{
    text-align:center;
    background:#f8fafc;
    border-radius:15px;
    padding:25px;
    border:1px solid #e5e7eb;
}

.seller-box h3{
    margin-bottom:10px;
}

.seller-box p{
    margin:15px 0;
    font-size:17px;
}

.badge{
    display:inline-block;
    background:#16a34a;
    color:#fff;
    padding:8px 18px;
    border-radius:30px;
    font-size:14px;
    margin-top:10px;
}

.action-buttons{
    display:flex;
    flex-direction:column;
    gap:15px;
    margin-top:25px;
}

.action-buttons .btn,
.seller-box .btn{
    width:100%;
    text-align:center;
    padding:14px;
    border-radius:12px;
    font-size:16px;
    font-weight:600;
}

.description{
    background:#fafafa;
    padding:20px;
    border-radius:15px;
    border:1px solid #e5e7eb;
    line-height:1.8;
    color:#444;
}

@media(max-width:900px){

    .car-layout{
        grid-template-columns:1fr;
    }

    .car-image{
        height:300px;
    }

    .quick-specs,
    .spec-table{
        grid-template-columns:1fr;
    }
}

</style>


<div class="car-details-container">


<div class="car-header">

<h1>

🚗 <?php echo e($car['make']); ?>

<span style="color:#2563eb;">

<?php echo e($car['model']); ?>

</span>

</h1>

</div>


<div class="car-layout">


<div>


<div class="car-image-card">


<img
id="buyerMainCarImage"
class="car-image"
src="<?php echo e($gallery[0]); ?>"
alt="Car Image"
>


<?php if (count($gallery) > 1): ?>

<div class="car-gallery">

<?php
foreach (
    $gallery
    as $index => $image
):
?>

<button
type="button"
class="car-thumb <?php echo $index === 0 ? 'active' : ''; ?>"
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


<div class="car-content">


<div class="price-box">

<p>Price</p>

<h2>

₹<?php echo number_format((float)$car['price']); ?>

</h2>

</div>


<h2 class="section-title">
🚘 Quick Details
</h2>


<div class="quick-specs">


<div class="spec">

📅 Year

<br>

<b>
<?php echo e($car['year']); ?>
</b>

</div>


<div class="spec">

🛣 Mileage

<br>

<b>

<?php echo number_format((int)$car['mileage']); ?> km

</b>

</div>


<div class="spec">

⛽ Fuel

<br>

<b>
<?php echo e($car['fuel_type']); ?>
</b>

</div>


<div class="spec">

⚙ Transmission

<br>

<b>
<?php echo e($car['transmission']); ?>
</b>

</div>


</div>


<h2 class="section-title">
📋 Specifications
</h2>


<div class="spec-table">


<div class="spec-item">
<strong>Brand</strong>
<?php echo e($car['make']); ?>
</div>


<div class="spec-item">
<strong>Model</strong>
<?php echo e($car['model']); ?>
</div>


<div class="spec-item">
<strong>Color</strong>
<?php echo e($car['color']); ?>
</div>


<div class="spec-item">
<strong>Owner</strong>
<?php echo e($car['owner_type']); ?>
</div>


<div class="spec-item">
<strong>Body Type</strong>
<?php echo e($car['body_type']); ?>
</div>


<div class="spec-item">
<strong>Seats</strong>
<?php echo e($car['seating_capacity']); ?> Seats
</div>


<div class="spec-item">
<strong>Location</strong>
📍 <?php echo e($car['location']); ?>
</div>


</div>


<h2 class="section-title">
📝 Description
</h2>


<div class="description">

<?php
echo nl2br(
    e($car['description'])
);
?>

</div>


<div style="margin-top:20px;">

<a
class="btn primary"
href="add_review.php?car_id=<?php echo (int)$car['id']; ?>"
>

⭐ Add Review

</a>

</div>


</div>

</div>

</div>


<div>


<div class="info-card">

<div class="car-content">


<h2 class="section-title">
👤 Seller Information
</h2>


<div class="seller-box">


<img
src="/carconnect/assets/images/user.png"
style="
width:90px;
height:90px;
border-radius:50%;
margin-bottom:15px;
"
alt="Seller"
>


<h3>

<?php echo e($car['seller_name'] ?? 'Seller'); ?>

</h3>


<p>

📞 <?php echo e($car['phone'] ?? 'N/A'); ?>

</p>


<?php if (!empty($car['phone'])): ?>

<a
class="btn primary"
style="
display:block;
margin-top:15px;
background:#16a34a;
"
href="tel:<?php echo e($car['phone']); ?>"
>

📞 Call Seller

</a>

<?php endif; ?>


<br><br>


<span class="badge">
✔ Verified Seller
</span>


</div>

</div>

</div>


<div class="action-buttons">


<a
class="btn"
href="/carconnect/buyer/wishlist.php?add=<?php echo (int)$car['id']; ?>"
>
❤️ Wishlist
</a>


<a
class="btn primary"
href="/carconnect/buyer/checkout.php?id=<?php echo (int)$car['id']; ?>"
>
💳 Buy Now
</a>


<a
class="btn"
href="/carconnect/buyer/chat.php?car_id=<?php echo (int)$car['id']; ?>&seller=<?php echo (int)$car['seller_id']; ?>"
>
💬 Chat
</a>


</div>


</div>


</div>

</div>


<script>

document
.querySelectorAll('.car-thumb')
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
                    'buyerMainCarImage'
                )
                .src = image;

            document
                .querySelectorAll(
                    '.car-thumb'
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