
<?php
require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

/* FILTERS */
$q = trim($_GET['q'] ?? '');
$brand = trim($_GET['brand'] ?? '');
$min = (int)($_GET['min'] ?? 0);
$max = (int)($_GET['max'] ?? 0);

$fuel = trim($_GET['fuel_type'] ?? '');
$transmission = trim($_GET['transmission'] ?? '');
$color = trim($_GET['color'] ?? '');
$owner = trim($_GET['owner_type'] ?? '');
$body = trim($_GET['body_type'] ?? '');
$seat = trim($_GET['seating_capacity'] ?? '');
$location = trim($_GET['location'] ?? '');

/* QUERY BUILD */
$sql = "SELECT id,make,model,year,price,image_path FROM car_listings WHERE status='approved'";
$params = [];
$types = "";

/* SEARCH */
if($q){
  $sql .= " AND (make LIKE ? OR model LIKE ?)";
  $types .= "ss";
  $like = "%$q%";
  $params[] = $like;
  $params[] = $like;
}

/* BRAND */
if($brand){
  $sql .= " AND make=?";
  $types .= "s";
  $params[] = $brand;
}

/* PRICE */
if($min > 0){
  $sql .= " AND price >= ?";
  $types .= "i";
  $params[] = $min;
}

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

/* OWNER */
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
if($seat){
    $sql .= " AND seating_capacity=?";
    $types .= "i";
    $params[] = (int)$seat;
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
if($params){
  mysqli_stmt_bind_param($stmt,$types,...$params);
}
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
?>


<div class="browse-wrapper">

<section class="hero">
    <h1>🚗 Find Your Dream Car</h1>
    <p>Browse verified used cars available near you</p>
</section>


<!-- FILTER BOX -->
<form method="GET" class="filter-box">

<div class="filter-grid">

<div>
<label>Search</label>
<input class="input" 
name="q"
value="<?php echo e($q); ?>"
placeholder="Search car">
</div>


<div>
<label>Brand</label>
<select class="input" name="brand">
<option value="">All Brands</option>
<option>Maruti</option>
<option>Tata</option>
<option>Mahindra</option>
<option>Hyundai</option>
<option>Honda</option>
<option>Toyota</option>
<option>Kia</option>
</select>
</div>


<div>
<label>Min Price</label>
<input class="input" 
type="number"
name="min">
</div>


<div>
<label>Max Price</label>
<input class="input"
type="number"
name="max">
</div>


<div>
<label>Fuel</label>
<select class="input" name="fuel_type">
<option value="">All</option>
<option>Petrol</option>
<option>Diesel</option>
<option>CNG</option>
<option>Electric</option>
</select>
</div>


<div>
<label>Transmission</label>
<select class="input" name="transmission">
<option value="">All</option>
<option>Manual</option>
<option>Automatic</option>
</select>
</div>


</div>


<div class="filter-buttons">

<button class="search-btn">
🔍 Search Cars
</button>


<a href="car_listings.php"
class="reset-btn">
Reset
</a>

</div>


</form>



<h2 class="section-title">
Available Cars
</h2>



<div class="car-grid">


<?php if(mysqli_num_rows($res)>0): ?>

<?php while($c=mysqli_fetch_assoc($res)):

$img=$c['image_path']
?$c['image_path']
:"/carconnect/assets/images/default_car.jpg";

?>


<div class="car-card">


<div class="image-box">

<img src="<?php echo e($img); ?>">


<span class="price-tag">
₹<?php echo number_format($c['price']); ?>
</span>


</div>



<div class="car-content">


<h2>
<?php echo e($c['make']." ".$c['model']); ?>
</h2>


<p>
📅 <?php echo e($c['year']); ?>
</p>



<div class="features">


<span>
🚘 Used
</span>


<span>
⭐ Verified
</span>


<span>
⚡ Petrol
</span>


</div>




<div class="actions">


<a class="details-btn"
href="car_details.php?id=<?php echo $c['id']; ?>">
View Details
</a>


<a class="wish-btn"
href="wishlist.php?add=<?php echo $c['id']; ?>">
❤️
</a>


</div>



</div>


</div>


<?php endwhile; ?>


<?php else: ?>


<h2>No Cars Found</h2>


<?php endif; ?>


</div>

</div>

<style>

.browse-wrapper{
max-width:1200px;
margin:auto;
padding:20px;
}


.hero{

background:linear-gradient(135deg,#111827,#2563eb);

padding:50px;

border-radius:25px;

color:white;

margin-bottom:30px;

text-align:center;

}


.hero h1{
font-size:40px;
}


.hero p{
opacity:.9;
}




.filter-box{

background:white;

padding:25px;

border-radius:25px;

box-shadow:0 10px 30px #0002;

}



.filter-grid{

display:grid;

grid-template-columns:
repeat(auto-fit,minmax(220px,1fr));

gap:20px;

}



.filter-grid label{

font-weight:700;

display:block;

margin-bottom:8px;

}



.filter-buttons{

margin-top:25px;

display:flex;

gap:15px;

}



.search-btn,
.reset-btn{

padding:12px 25px;

border-radius:12px;

border:none;

cursor:pointer;

font-weight:700;

}



.search-btn{

background:#2563eb;

color:white;

}


.reset-btn{

background:#e5e7eb;

text-decoration:none;

color:#111;

}




.section-title{

margin:40px 0 20px;

font-size:32px;

}




.car-grid{

display:grid;

grid-template-columns:
repeat(auto-fit,minmax(300px,1fr));

gap:30px;

}



.car-card{

background:white;

border-radius:25px;

overflow:hidden;

box-shadow:
0 15px 35px rgba(0,0,0,.12);

transition:.3s;

}



.car-card:hover{

transform:translateY(-10px);

}




.image-box{

height:230px;

position:relative;

}



.image-box img{

width:100%;

height:100%;

object-fit:cover;

}



.price-tag{

position:absolute;

bottom:15px;

left:15px;

background:#2563eb;

color:white;

padding:10px 18px;

border-radius:20px;

font-weight:800;

font-size:18px;

}




.car-content{

padding:22px;

}



.car-content h2{

font-size:24px;

margin-bottom:10px;

}




.features{

display:flex;

gap:10px;

flex-wrap:wrap;

margin:15px 0;

}



.features span{

background:#f1f5f9;

padding:8px 12px;

border-radius:20px;

font-size:14px;

}




.actions{

display:flex;

gap:10px;

}



.details-btn{

flex:1;

text-align:center;

background:#2563eb;

color:white;

padding:12px;

border-radius:12px;

text-decoration:none;

font-weight:700;

}



.wish-btn{

padding:12px 18px;

background:#fee2e2;

border-radius:12px;

text-decoration:none;

}
</style>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>