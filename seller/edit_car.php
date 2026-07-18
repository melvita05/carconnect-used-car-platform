<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("seller");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$sellerId = (int)$_SESSION['user_id'];
$id = (int)($_GET['id'] ?? 0);

/* ===================== */
/* FETCH CAR */
/* ===================== */

$stmt = mysqli_prepare($conn,"
SELECT * FROM car_listings 
WHERE id=? AND seller_id=? 
LIMIT 1
");
mysqli_stmt_bind_param($stmt,"ii",$id,$sellerId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$car = mysqli_fetch_assoc($res);

if(!$car){
  echo '<div class="alert">Car not found</div>';
  require_once __DIR__ . "/../includes/footer.php";
  exit();
}

$err="";
$ok="";

/* ===================== */
/* UPDATE */
/* ===================== */

if($_SERVER['REQUEST_METHOD']==='POST'){
$brand        = trim($_POST['brand'] ?? '');
$model        = trim($_POST['model'] ?? '');
$year         = (int)($_POST['year'] ?? 0);
$price        = (float)($_POST['price'] ?? 0);
$mileage      = (int)($_POST['mileage'] ?? 0);

$fuel         = trim($_POST['fuel_type'] ?? '');
$trans        = trim($_POST['transmission'] ?? '');

$color        = trim($_POST['color'] ?? '');
$ownerType    = trim($_POST['owner_type'] ?? '');
$bodyType     = trim($_POST['body_type'] ?? '');
$seating      = (int)($_POST['seating_capacity'] ?? 0);
$location     = trim($_POST['location'] ?? '');

$category_id  = (int)($_POST['category_id'] ?? 0);

$desc         = trim($_POST['description'] ?? '');
$imgPath = $car['image_path'];

/* VALIDATION */
if(
    $brand=="" ||
    $model=="" ||
    $year<=0 ||
    $price<=0 ||
    $category_id<=0
){
    $err="Please fill all required fields.";
}

/* IMAGE */
if(!$err && !empty($_FILES['image']['name'])){

$ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
$allow = ["jpg","jpeg","png","webp"];

if(!in_array($ext,$allow)){
  $err="Invalid image format";
}
elseif($_FILES['image']['size'] > 5*1024*1024){
  $err="Max 5MB allowed";
}
else{

$newName = uniqid("car_").".".$ext;
$uploadDir = __DIR__."/../uploads/";

if(!is_dir($uploadDir)){
  mkdir($uploadDir,0777,true);
}

$targetAbs = $uploadDir.$newName;
$targetRel = "/carconnect/uploads/".$newName;

if(move_uploaded_file($_FILES['image']['tmp_name'],$targetAbs)){
  $imgPath = $targetRel;
}else{
  $err="Upload failed";
}

}

}

/* UPDATE QUERY */

if(!$err){

$stmt2 = mysqli_prepare($conn,"
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
WHERE id=? AND seller_id=?
");
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
$imgPath,
$id,
$sellerId
);
if(mysqli_stmt_execute($stmt2)){
  $ok="Updated successfully. Waiting for admin approval.";

  // refresh data
  $stmt = mysqli_prepare($conn,"SELECT * FROM car_listings WHERE id=? AND seller_id=?");
  mysqli_stmt_bind_param($stmt,"ii",$id,$sellerId);
  mysqli_stmt_execute($stmt);
  $res = mysqli_stmt_get_result($stmt);
  $car = mysqli_fetch_assoc($res);

}else{
  $err="Update failed";
}

}

}
?>

<h1>✏️ Edit Car</h1>

<?php if($err): ?>
<div class="alert"><?php echo e($err); ?></div>
<?php endif; ?>

<?php if($ok): ?>
<div class="alert success"><?php echo e($ok); ?></div>
<?php endif; ?>

<form class="form" method="POST" enctype="multipart/form-data">

<label>Category</label>

<select class="input" name="category_id" required>

<option value="">Select Category</option>

<?php

$resCat=mysqli_query($conn,"SELECT * FROM car_categories ORDER BY name");

while($cat=mysqli_fetch_assoc($resCat)){

$sel=($car['category_id']==$cat['id'])?"selected":"";

echo "<option value='{$cat['id']}' $sel>".e($cat['name'])."</option>";

}

?>

</select>

<!-- BRAND -->
<label>Brand</label>
<input
class="input"
list="brandList"
name="brand"
value="<?php echo e($car['make']); ?>"
required>

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

<!-- MODEL -->
<label>Model</label>
<input class="input" name="model" value="<?php echo e($car['model']); ?>" required>

<!-- YEAR -->
<label>Year</label>
<input class="input" type="number" name="year" value="<?php echo e($car['year']); ?>" required>

<!-- PRICE -->
<label>Price (₹)</label>
<input class="input" type="number" name="price" value="<?php echo e($car['price']); ?>" required>

<!-- MILEAGE -->
<label>Mileage</label>
<input class="input" type="number" name="mileage" value="<?php echo e($car['mileage']); ?>">

<!-- FUEL -->
<label>Fuel Type</label>
<select class="input" name="fuel_type">
<?php
$fuels = ["Petrol","Diesel","CNG","Electric"];
foreach($fuels as $f){
$sel = ($car['fuel_type']==$f)?"selected":"";
echo "<option $sel>$f</option>";
}
?>
</select>

<!-- TRANSMISSION -->
<label>Transmission</label>
<select class="input" name="transmission">
<?php
$trs = ["Manual","Automatic"];
foreach($trs as $t){
$sel = ($car['transmission']==$t)?"selected":"";
echo "<option $sel>$t</option>";
}
?>
</select>

<label>Color</label>

<select class="input" name="color">

<?php

$colors=["White","Black","Silver","Grey","Blue","Red","Brown","Green"];

foreach($colors as $c){

$sel=($car['color']==$c)?"selected":"";

echo "<option $sel>$c</option>";

}

?>

</select>

<label>Owner Type</label>

<select class="input" name="owner_type">

<?php

$list=["First Owner","Second Owner","Third Owner","Fourth Owner"];

foreach($list as $o){

$sel=($car['owner_type']==$o)?"selected":"";

echo "<option $sel>$o</option>";

}

?>

</select>

<label>Body Type</label>

<select class="input" name="body_type">

<?php

$list=["Hatchback","Sedan","SUV","MUV","Coupe","Convertible"];

foreach($list as $b){

$sel=($car['body_type']==$b)?"selected":"";

echo "<option $sel>$b</option>";

}

?>

</select>

<label>Seating Capacity</label>

<select class="input" name="seating_capacity">

<?php

for($i=2;$i<=8;$i++){

$sel=($car['seating_capacity']==$i)?"selected":"";

echo "<option value='$i' $sel>$i</option>";

}

?>

</select>

<label>Location</label>

<input
class="input"
name="location"
value="<?php echo e($car['location']); ?>">

<!-- DESCRIPTION -->
<label>Description</label>
<textarea class="input" name="description"><?php echo e($car['description']); ?></textarea>

<!-- IMAGE -->
<label>Change Image</label>
<input class="input" type="file" name="image">

<?php if($car['image_path']): ?>
<img src="<?php echo $car['image_path']; ?>" style="height:80px;margin-top:8px">
<?php endif; ?>
</br>
<button class="btn primary" style="margin-top:15px">
Update Car
</button>

</form>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>