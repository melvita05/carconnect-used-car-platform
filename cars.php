<?php 
session_start();

/* 🔒 LOGIN REQUIRED */
if(empty($_SESSION['user_id'])){
    header("Location: /carconnect/auth/login.php");
    exit();
}

require_once __DIR__ . "/includes/header.php"; 
require_once __DIR__ . "/includes/db_connect.php"; 
require_once __DIR__ . "/includes/functions.php";

/* FILTERS */
/* FILTERS */

$q = trim($_GET['q'] ?? "");

$brand = trim($_GET['brand'] ?? "");

$category = (int)($_GET['category'] ?? 0);

$min = (int)($_GET['min'] ?? 0);

$max = (int)($_GET['max'] ?? 0);

$fuel = trim($_GET['fuel_type'] ?? "");

$transmission = trim($_GET['transmission'] ?? "");

$color = trim($_GET['color'] ?? "");

$owner = trim($_GET['owner_type'] ?? "");

$body = trim($_GET['body_type'] ?? "");

$seat = (int)($_GET['seating_capacity'] ?? 0);

$location = trim($_GET['location'] ?? "");

/* ROLE */
$role = $_SESSION['role'] ?? 'buyer';

/* ROLE BASED PATH */
$path = "/carconnect/buyer/car_details.php";

if($role === "admin"){
  $path = "/carconnect/admin/car_details.php";
}
elseif($role === "seller"){
  $path = "/carconnect/seller/car_details.php";
}
?>
<style>

.browse-title{
    text-align:center;
    font-size:36px;
    font-weight:800;
    margin:30px 0;
    color:#111827;
}


/* CAR GRID */

.car-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(300px,1fr));
    gap:25px;
    margin-top:35px;
}


/* CARD */

.car-card{
    background:white;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 10px 30px rgba(0,0,0,.12);
    transition:.3s;
}


.car-card:hover{
    transform:translateY(-8px);
    box-shadow:0 20px 45px rgba(0,0,0,.2);
}



/* IMAGE */

.car-card img{

    width:100%;
    height:230px;
    object-fit:cover;

}



/* CONTENT */

.car-content{
    padding:20px;
}


.car-title{

    font-size:22px;
    font-weight:800;
    color:#111827;

}



.car-price{

    font-size:26px;
    font-weight:800;
    color:#2563eb;
    margin:10px 0;

}



/* DETAILS */

.car-info{

display:flex;
flex-wrap:wrap;
gap:10px;

}


.info-tag{

background:#f1f5f9;
padding:8px 12px;
border-radius:20px;
font-size:14px;

}



/* BUTTON */

.view-btn{

margin-top:20px;
width:100%;
text-align:center;
border-radius:12px;

}



</style>
<h2 class="page-title">Browse Cars</h2>

<!-- 🔍 FILTER FORM -->
<style>

.filter-container{

    background:#ffffff;
    padding:25px;
    border-radius:20px;
    box-shadow:0 10px 35px rgba(0,0,0,0.10);
    margin-bottom:30px;

}


.filter-heading{

    font-size:24px;
    font-weight:800;
    color:#111827;
    margin-bottom:20px;

}



.filter-grid{

    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:18px;

}



.filter-group label{

    display:block;
    font-size:14px;
    font-weight:700;
    color:#374151;
    margin-bottom:8px;

}



.filter-group .input{

    width:100%;
    padding:12px 15px;
    border-radius:12px;
    border:1px solid #d1d5db;
    background:#f9fafb;

}



.filter-group .input:focus{

    outline:none;
    border-color:#2563eb;
    background:white;

}



.filter-buttons{

    display:flex;
    gap:15px;
    margin-top:25px;
    flex-wrap:wrap;

}



.filter-buttons .btn{

    padding:12px 25px;
    border-radius:12px;

}



</style>



<form method="GET" class="filter-container">


<div class="filter-heading">

🔍 Search Your Dream Car

</div>



<div class="filter-grid">



<div class="filter-group">

<label>Search</label>

<input 
class="input"
name="q"
value="<?php echo e($q); ?>"
placeholder="Brand or Model">

</div>




<div class="filter-group">

<label>Category</label>

<select class="input" name="category">

<option value="">
All Categories
</option>


<?php

$resCat=mysqli_query($conn,
"SELECT * FROM car_categories ORDER BY name ASC");


while($cat=mysqli_fetch_assoc($resCat)){

$sel=($category==$cat['id'])?"selected":"";


echo "<option value='{$cat['id']}' $sel>"
.e($cat['name']).
"</option>";

}

?>

</select>

</div>




<div class="filter-group">

<label>Brand</label>

<input
class="input"
name="brand"
value="<?php echo e($brand); ?>"
placeholder="Toyota">

</div>




<div class="filter-group">

<label>Min Price</label>

<input
class="input"
type="number"
name="min"
value="<?php echo $min ?: ''; ?>"
placeholder="₹ Minimum">

</div>




<div class="filter-group">

<label>Max Price</label>

<input
class="input"
type="number"
name="max"
value="<?php echo $max ?: ''; ?>"
placeholder="₹ Maximum">

</div>




<div class="filter-group">

<label>Fuel Type</label>

<select class="input" name="fuel_type">

<option value="">
All Fuel
</option>

<option value="Petrol">
Petrol
</option>

<option value="Diesel">
Diesel
</option>

<option value="CNG">
CNG
</option>

<option value="Electric">
Electric
</option>

</select>

</div>




<div class="filter-group">

<label>Transmission</label>

<select class="input" name="transmission">

<option value="">
All Transmission
</option>

<option>
Manual
</option>

<option>
Automatic
</option>

</select>

</div>




<div class="filter-group">

<label>Color</label>

<input
class="input"
name="color"
placeholder="White"
value="<?php echo e($color); ?>">

</div>




<div class="filter-group">

<label>Owner Type</label>

<select class="input" name="owner_type">


<option value="">
All Owners
</option>

<option>
First Owner
</option>

<option>
Second Owner
</option>

<option>
Third Owner
</option>

<option>
Fourth Owner
</option>


</select>

</div>




<div class="filter-group">

<label>Body Type</label>

<select class="input" name="body_type">


<option value="">
All Types
</option>

<option>
Hatchback
</option>

<option>
Sedan
</option>

<option>
SUV
</option>

<option>
MUV
</option>

<option>
Coupe
</option>


</select>

</div>




<div class="filter-group">

<label>Seating Capacity</label>


<select class="input" name="seating_capacity">


<option value="">
All Seats
</option>

<option>2</option>

<option>4</option>

<option>5</option>

<option>6</option>

<option>7</option>

<option>8</option>


</select>


</div>




<div class="filter-group">

<label>Location</label>

<input
class="input"
name="location"
placeholder="Mangalore"
value="<?php echo e($location); ?>">


</div>



</div>




<div class="filter-buttons">


<button class="btn primary">

🔍 Apply Filters

</button>



<a class="btn" href="/carconnect/cars.php">

Reset

</a>


</div>



</form>

<div class="car-grid">
<?php

/* ===================== */
/* QUERY BUILD */
/* ===================== */

$sql = "SELECT * FROM car_listings WHERE status='approved'";
$params = [];
$types = "";

/* SEARCH */
if($q){
    $sql .= " AND (make LIKE ? OR model LIKE ?)";
    $types .= "ss";
    $like = "%".$q."%";
    $params[] = $like;
    $params[] = $like;
}

/* BRAND */
if($brand){
    $sql .= " AND make LIKE ?";
    $types .= "s";
    $params[] = "%".$brand."%";
}

/* CATEGORY */
if($category > 0){
    $sql .= " AND category_id=?";
    $types .= "i";
    $params[] = $category;
}

/* MIN PRICE */
if($min > 0){
    $sql .= " AND price >= ?";
    $types .= "i";
    $params[] = $min;
}

/* MAX PRICE */
if($max > 0){
    $sql .= " AND price <= ?";
    $types .= "i";
    $params[] = $max;
}

/* FUEL */
if($fuel){
    $sql .= " AND fuel_type=?";
    $types .= "s";
    $params[] = $fuel;
}

/* TRANSMISSION */
if($transmission){
    $sql .= " AND transmission=?";
    $types .= "s";
    $params[] = $transmission;
}

/* COLOR */
if($color){
    $sql .= " AND color=?";
    $types .= "s";
    $params[] = $color;
}

/* OWNER TYPE */
if($owner){
    $sql .= " AND owner_type=?";
    $types .= "s";
    $params[] = $owner;
}

/* BODY TYPE */
if($body){
    $sql .= " AND body_type=?";
    $types .= "s";
    $params[] = $body;
}

/* SEATING */
if($seat > 0){
    $sql .= " AND seating_capacity=?";
    $types .= "i";
    $params[] = $seat;
}

/* LOCATION */
if($location){
    $sql .= " AND location LIKE ?";
    $types .= "s";
    $params[] = "%".$location."%";
}

$sql .= " ORDER BY created_at DESC";

/* EXECUTE */
$stmt = mysqli_prepare($conn,$sql);

if(!empty($params)){
    mysqli_stmt_bind_param($stmt,$types,...$params);
}

mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

/* ===================== */
/* RESULT */
/* ===================== */

if(mysqli_num_rows($res)>0){

while($c = mysqli_fetch_assoc($res)){

$img = $c['image_path'] 
? e($c['image_path']) 
: "/carconnect/assets/images/default_car.jpg";
?>

<div class="car-card">


<img src="<?php echo $img; ?>">


<div class="car-content">

<div class="car-title">
<?php echo e($c['make']." ".$c['model']); ?>
</div>

<div class="muted">
📅 <?php echo e($c['year']); ?>
</div>


<div class="car-price">

₹<?php echo number_format($c['price']); ?>

</div>



<div class="car-info">


<span class="info-tag">
⛽ <?php echo e($c['fuel_type']); ?>
</span>


<span class="info-tag">
⚙ <?php echo e($c['transmission']); ?>
</span>


<span class="info-tag">
🛣 <?php echo number_format($c['mileage']); ?> km
</span>


<span class="info-tag">
📍 <?php echo e($c['location']); ?>
</span>


</div>
<div class="mt-10">

<a
class="btn primary view-btn"
href="<?php echo $path; ?>?id=<?php echo intval($c['id']); ?>">

View Details →

</a>

</div>

</div>

</div>

<?php
}

}else{
?>

<div class="empty-state">

<p class="empty-title">🚗 No cars found</p>

<p class="muted">
Try changing filters or search again.
</p>

<a class="btn primary" href="/carconnect/cars.php">
Browse All Cars
</a>

</div>

<?php } ?>

</div>

<?php require_once __DIR__ . "/includes/footer.php"; ?>