<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("seller");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$sellerId = (int)$_SESSION['user_id'];

/* FILTERS */
$keyword      = trim($_GET['q'] ?? '');
$brand        = trim($_GET['brand'] ?? '');
$minPrice     = (int)($_GET['min'] ?? 0);
$maxPrice     = (int)($_GET['max'] ?? 0);

$fuel         = trim($_GET['fuel_type'] ?? '');
$transmission = trim($_GET['transmission'] ?? '');
$status       = trim($_GET['status'] ?? '');
$year         = trim($_GET['year'] ?? '');
$location     = trim($_GET['location'] ?? '');
?>
<div class="search-wrapper">

<div class="hero">
    <h1>🚗 My Car Listings</h1>
    <p>Search and manage all your uploaded vehicles</p>
</div>

<form method="GET" class="filter-box">

<div class="filter-grid">

<div>
<label>Search</label>
<input
class="input"
name="q"
value="<?php echo e($keyword); ?>"
placeholder="Brand or Model">
</div>

<div>
<label>Brand</label>
<select class="input" name="brand">
<option value="">All</option>
<option <?php if($brand=="Maruti") echo "selected"; ?>>Maruti</option>
<option <?php if($brand=="Tata") echo "selected"; ?>>Tata</option>
<option <?php if($brand=="Mahindra") echo "selected"; ?>>Mahindra</option>
<option <?php if($brand=="Hyundai") echo "selected"; ?>>Hyundai</option>
<option <?php if($brand=="Honda") echo "selected"; ?>>Honda</option>
<option <?php if($brand=="Toyota") echo "selected"; ?>>Toyota</option>
<option <?php if($brand=="Kia") echo "selected"; ?>>Kia</option>
<option <?php if($brand=="BMW") echo "selected"; ?>>BMW</option>
<option <?php if($brand=="Audi") echo "selected"; ?>>Audi</option>
</select>
</div>

<div>
<label>Min Price</label>
<input
class="input"
type="number"
name="min"
value="<?php echo $minPrice ?: ''; ?>">
</div>

<div>
<label>Max Price</label>
<input
class="input"
type="number"
name="max"
value="<?php echo $maxPrice ?: ''; ?>">
</div>

<div>
<label>Fuel</label>
<select class="input" name="fuel_type">
<option value="">All</option>
<option <?php if($fuel=="Petrol") echo "selected"; ?>>Petrol</option>
<option <?php if($fuel=="Diesel") echo "selected"; ?>>Diesel</option>
<option <?php if($fuel=="CNG") echo "selected"; ?>>CNG</option>
<option <?php if($fuel=="Electric") echo "selected"; ?>>Electric</option>
</select>
</div>

<div>
<label>Transmission</label>
<select class="input" name="transmission">
<option value="">All</option>
<option <?php if($transmission=="Manual") echo "selected"; ?>>Manual</option>
<option <?php if($transmission=="Automatic") echo "selected"; ?>>Automatic</option>
</select>
</div>

<div>
<label>Status</label>
<select class="input" name="status">
<option value="">All</option>
<option <?php if($status=="approved") echo "selected"; ?>>Approved</option>
<option <?php if($status=="pending") echo "selected"; ?>>Pending</option>
<option <?php if($status=="sold") echo "selected"; ?>>Sold</option>
</select>
</div>

<div>
<label>Year</label>
<input
class="input"
type="number"
name="year"
value="<?php echo e($year); ?>"
placeholder="2024">
</div>

<div>
<label>Location</label>
<input
class="input"
name="location"
value="<?php echo e($location); ?>"
placeholder="Mangalore">
</div>

</div>

<div class="filter-buttons">

<button class="search-btn">
🔍 Search
</button>

<a href="search_cars.php" class="reset-btn">
Reset
</a>

</div>

</form>

</div>
<div class="grid">

<?php

$sql = "SELECT * FROM car_listings WHERE seller_id=?";
$params = [$sellerId];
$types = "i";

/* SEARCH */
if($keyword){
  $sql .= " AND (make LIKE ? OR model LIKE ?)";
  $types .= "ss";
  $search = "%$keyword%";
  $params[] = $search;
  $params[] = $search;
}

if($brand){
    $sql .= " AND make=?";
    $types .= "s";
    $params[] = $brand;
}

if($fuel){
    $sql .= " AND fuel_type=?";
    $types .= "s";
    $params[] = $fuel;
}

if($transmission){
    $sql .= " AND transmission=?";
    $types .= "s";
    $params[] = $transmission;
}

if($status){
    $sql .= " AND status=?";
    $types .= "s";
    $params[] = $status;
}

if($year){
    $sql .= " AND year=?";
    $types .= "i";
    $params[] = (int)$year;
}

if($location){
    $sql .= " AND location LIKE ?";
    $types .= "s";
    $params[] = "%".$location."%";
}

/* PRICE FILTER */
if($minPrice > 0){
  $sql .= " AND price >= ?";
  $types .= "i";
  $params[] = $minPrice;
}

if($maxPrice > 0){
  $sql .= " AND price <= ?";
  $types .= "i";
  $params[] = $maxPrice;
}

$sql .= " ORDER BY created_at DESC";

/* EXECUTE */
$stmt = mysqli_prepare($conn,$sql);
mysqli_stmt_bind_param($stmt,$types,...$params);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

/* RESULT */

if(mysqli_num_rows($res)>0){

while($c=mysqli_fetch_assoc($res)){

$img = $c['image_path'] ?: "/carconnect/assets/images/default_car.jpg";

/* STATUS COLOR */
$statusColor = "#888";
if($c['status']=="approved") $statusColor="#4caf50";
if($c['status']=="pending")  $statusColor="#ff9800";
if($c['status']=="sold")     $statusColor="#9c27b0";

echo "

<div class='card'>

<img src='$img' style='height:180px;object-fit:cover'>

<div class='p'>

<div style='font-weight:800;font-size:16px'>
".e($c['make'])." ".e($c['model'])."
</div>

<div class='muted'>
".e($c['year'])."
</div>

<div style='margin-top:6px;font-weight:700;color:#00d2ff'>
₹".number_format($c['price'])."
</div>

<div style='margin-top:6px;color:$statusColor;font-weight:700'>
".ucfirst($c['status'])."
</div>

<div style='margin-top:10px'>
<a class='btn' href='edit_car.php?id=".$c['id']."'>✏️ Edit</a>
</div>

</div>

</div>

";

}

}else{

echo "

<div style='text-align:center;width:100%'>

<p class='muted' style='font-size:18px'>
No cars found 🚗
</p>

<p class='muted'>
Try adjusting filters or add new listings.
</p>

<a class='btn primary' href='add_car.php'>
➕ Add Car
</a>

</div>

";

}

?>

</div>
<style>

.search-wrapper{
max-width:1200px;
margin:auto;
padding:20px;
}

.hero{
background:linear-gradient(135deg,#0f172a,#2563eb);
padding:40px;
border-radius:20px;
color:white;
text-align:center;
margin-bottom:30px;
}

.hero h1{
font-size:38px;
margin-bottom:8px;
}

.hero p{
opacity:.9;
}

.filter-box{
background:#fff;
padding:25px;
border-radius:20px;
box-shadow:0 12px 30px rgba(0,0,0,.08);
margin-bottom:30px;
}

.filter-grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
gap:18px;
}

.filter-grid label{
font-weight:700;
margin-bottom:6px;
display:block;
}

.filter-buttons{
display:flex;
gap:15px;
margin-top:25px;
}

.search-btn{
background:#2563eb;
color:white;
border:none;
padding:12px 25px;
border-radius:10px;
font-weight:700;
cursor:pointer;
}

.reset-btn{
background:#f1f5f9;
padding:12px 25px;
border-radius:10px;
text-decoration:none;
color:#111;
font-weight:700;
}

</style>
<?php require_once __DIR__ . "/../includes/footer.php"; ?>