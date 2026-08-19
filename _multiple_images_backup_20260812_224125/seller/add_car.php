<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("seller");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$sellerId = (int) $_SESSION['user_id'];
$err = "";
$ok = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $brand        = ucfirst(strtolower(trim($_POST['brand'] ?? '')));
$model        = trim($_POST['model'] ?? '');
$year         = (int) ($_POST['year'] ?? 0);
$price        = (float) ($_POST['price'] ?? 0);
$mileage      = (int) ($_POST['mileage'] ?? 0);
$fuelType     = trim($_POST['fuel_type'] ?? '');
$transmission = trim($_POST['transmission'] ?? '');

$color        = trim($_POST['color'] ?? '');
$ownerType    = trim($_POST['owner_type'] ?? '');
$bodyType     = trim($_POST['body_type'] ?? '');
$seating      = (int) ($_POST['seating_capacity'] ?? 0);
$location     = trim($_POST['location'] ?? '');

$description  = trim($_POST['description'] ?? '');
$category_id  = (int) ($_POST['category_id'] ?? 0);

    if ($brand === "" || $model === "" || $year <= 0 || $price <= 0 || $category_id <= 0) {
        $err = "Please fill all required fields properly.";
    } else {

        $imagePath = "/carconnect/assets/images/default_car.jpg";

        /* ===================== */
        /* IMAGE UPLOAD */
        /* ===================== */

        if (!empty($_FILES['image']['name'])) {

            if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {

                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowed = ["jpg","jpeg","png","webp"];

                if (!in_array($ext,$allowed)) {
                    $err = "Only JPG, PNG, WEBP allowed";
                } elseif ($_FILES['image']['size'] > 5*1024*1024) {
                    $err = "Max size 5MB allowed";
                } else {

                    $newName = uniqid("car_").".".$ext;
                    $uploadDir = __DIR__."/../uploads/";

                    if(!is_dir($uploadDir)){
                        mkdir($uploadDir,0777,true);
                    }

                    if(move_uploaded_file($_FILES['image']['tmp_name'],$uploadDir.$newName)){
                        $imagePath = "/carconnect/uploads/".$newName;
                    } else {
                        $err = "Upload failed";
                    }
                }
            } else {
                $err = "Upload error";
            }
        }

        /* ===================== */
        /* INSERT */
        /* ===================== */

        if ($err === "") {

            $status = "pending";

            $stmt = mysqli_prepare($conn,"
INSERT INTO car_listings
(
seller_id,
make,
model,
year,
price,
mileage,
fuel_type,
transmission,
color,
owner_type,
body_type,
seating_capacity,
location,
description,
image_path,
status,
category_id
)
VALUES
(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
");
           mysqli_stmt_bind_param(
    $stmt,
    "issidisssssissssi",
    $sellerId,
    $brand,
    $model,
    $year,
    $price,
    $mileage,
    $fuelType,
    $transmission,
    $color,
    $ownerType,
    $bodyType,
    $seating,
    $location,
    $description,
    $imagePath,
    $status,
    $category_id
);

            if(mysqli_stmt_execute($stmt)){
                $ok = "Car added successfully. Waiting for admin approval.";
            } else {
                $err = "Database error";
            }

            mysqli_stmt_close($stmt);
        }
    }
}
?>

<h1>🚗 Add Car Listing</h1>

<?php if($err): ?>
<div class="alert"><?php echo e($err); ?></div>
<?php endif; ?>

<?php if($ok): ?>
<div class="alert success"><?php echo e($ok); ?></div>
<?php endif; ?>


<form class="form" method="POST" enctype="multipart/form-data">

<!-- CATEGORY -->
<label>Category</label>
<select class="input" name="category_id" required>
<option value="">Select Category</option>

<?php
$res = mysqli_query($conn,"SELECT * FROM car_categories ORDER BY name ASC");
while($cat = mysqli_fetch_assoc($res)){
echo "<option value='".$cat['id']."'>".e($cat['name'])."</option>";
}
?>

</select>


<!-- BRAND (PRO LEVEL) -->
<label>Brand</label>

<input 
  class="input" 
  list="brandList" 
  name="brand" 
  placeholder="Type or select brand"
  value="<?php echo isset($_POST['brand']) ? e($_POST['brand']) : ''; ?>"
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


<!-- MODEL -->
<label>Model</label>
<input class="input" type="text" name="model" value="<?php echo $_POST['model'] ?? ''; ?>" required>

<!-- YEAR -->
<label>Manufacturing Year</label>
<input class="input" type="number" name="year" value="<?php echo $_POST['year'] ?? ''; ?>" required>

<!-- PRICE -->
<label>Price (₹)</label>
<input class="input" type="number" step="0.01" name="price" value="<?php echo $_POST['price'] ?? ''; ?>" required>

<!-- MILEAGE -->
<label>Mileage (km)</label>
<input class="input" type="number" name="mileage" value="<?php echo $_POST['mileage'] ?? ''; ?>">

<!-- FUEL -->
<label>Fuel Type</label>
<select class="input" name="fuel_type">
<option>Petrol</option>
<option>Diesel</option>
<option>CNG</option>
<option>Electric</option>
</select>

<!-- TRANSMISSION -->
<label>Transmission</label>
<select class="input" name="transmission">
<option>Manual</option>
<option>Automatic</option>
</select>

<!-- COLOR -->
<label>Color</label>
<select class="input" name="color" required>
    <option value="">Select Color</option>
    <option>White</option>
    <option>Black</option>
    <option>Silver</option>
    <option>Grey</option>
    <option>Blue</option>
    <option>Red</option>
    <option>Brown</option>
    <option>Green</option>
</select>

<!-- OWNER TYPE -->
<label>Owner Type</label>
<select class="input" name="owner_type" required>
    <option value="">Select Owner Type</option>
    <option>First Owner</option>
    <option>Second Owner</option>
    <option>Third Owner</option>
    <option>Fourth Owner</option>
</select>

<!-- BODY TYPE -->
<label>Body Type</label>
<select class="input" name="body_type" required>
    <option value="">Select Body Type</option>
    <option>Hatchback</option>
    <option>Sedan</option>
    <option>SUV</option>
    <option>MUV</option>
    <option>Coupe</option>
    <option>Convertible</option>
</select>

<!-- SEATING -->
<label>Seating Capacity</label>
<select class="input" name="seating_capacity" required>
    <option value="">Select Seats</option>
    <option value="2">2</option>
    <option value="4">4</option>
    <option value="5">5</option>
    <option value="6">6</option>
    <option value="7">7</option>
    <option value="8">8</option>
</select>

<!-- LOCATION -->
<label>Location</label>
<input
class="input"
type="text"
name="location"
placeholder="Mangalore"
required>

<!-- DESCRIPTION -->
<label>Description</label>
<textarea class="input" name="description" rows="4"><?php echo $_POST['description'] ?? ''; ?></textarea>

<!-- IMAGE -->
<label>Car Image</label>
<input class="input" type="file" name="image">

<button class="btn primary" style="margin-top:15px">
🚀 Add Car
</button>

</form>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>