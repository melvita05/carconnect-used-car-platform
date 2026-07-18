<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("admin");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

/* ===================== */
/* HANDLE ACTIONS */
/* ===================== */

if($_SERVER['REQUEST_METHOD']=="POST"){

  $id = intval($_POST['id']);

  if($_POST['action']=="approve"){
    mysqli_query($conn,"UPDATE car_listings SET status='approved' WHERE id=$id");
    header("Location: manage_cars.php?msg=approved");
    exit();
  }

  if($_POST['action']=="reject"){
    mysqli_query($conn,"UPDATE car_listings SET status='rejected' WHERE id=$id");
    header("Location: manage_cars.php?msg=rejected");
    exit();
  }

  if($_POST['action']=="delete"){
    mysqli_query($conn,"DELETE FROM car_listings WHERE id=$id");
    header("Location: manage_cars.php?msg=deleted");
    exit();
  }
}

/* ===================== */
/* MESSAGE */
/* ===================== */

if(isset($_GET['msg'])){
  if($_GET['msg']=="approved") echo "<div class='alert success'>Car Approved ✅</div>";
  if($_GET['msg']=="rejected") echo "<div class='alert'>Car Rejected ❌</div>";
  if($_GET['msg']=="deleted") echo "<div class='alert success'>Car Deleted 🗑️</div>";
}
?>

<div class="manage-header">
    <h1>🚗 Manage Car Listings</h1>
    <p>Approve, reject and manage all car listings submitted by sellers.</p>
</div>

<!-- ===================== -->
<!-- PENDING CARS -->
<!-- ===================== -->
<?php

$pending=mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM car_listings WHERE status='pending'"))[0];

$approved=mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM car_listings WHERE status='approved'"))[0];

$rejected=mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM car_listings WHERE status='rejected'"))[0];

$sold=mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM car_listings WHERE status='sold'"))[0];

?>

<div class="stats-grid">

<div class="stat-card">
<h2><?php echo $pending; ?></h2>
<p>Pending</p>
</div>

<div class="stat-card">
<h2><?php echo $approved; ?></h2>
<p>Approved</p>
</div>

<div class="stat-card">
<h2><?php echo $rejected; ?></h2>
<p>Rejected</p>
</div>

<div class="stat-card">
<h2><?php echo $sold; ?></h2>
<p>Sold</p>
</div>

</div>
<h2>⏳ Pending Approval</h2>

<div class="grid">

<?php

$res = mysqli_query($conn,"
SELECT c.*, s.name seller
FROM car_listings c
JOIN sellers s ON s.id=c.seller_id
WHERE c.status='pending'
ORDER BY c.id DESC
");

if($res && mysqli_num_rows($res)>0){

while($c=mysqli_fetch_assoc($res)){

$img = $c['image_path'] ?: "/carconnect/assets/images/default_car.jpg";

$statusColor = "#888";

switch($c['status']){

    case "approved":
        $statusColor="#16a34a";
        break;

    case "pending":
        $statusColor="#f59e0b";
        break;

    case "rejected":
        $statusColor="#ef4444";
        break;

    case "sold":
        $statusColor="#7c3aed";
        break;
}

echo "
<div class='card'>

<img src='".e($img)."' style='height:200px;object-fit:cover'>

<div class='p'>

<div style='font-weight:800;font-size:18px'>
".e($c['make'])." ".e($c['model'])."
</div>

<div class='muted'>
Year: ".e($c['year'])."
</div>

<div style='margin-top:6px;color:#00d2ff;font-weight:700'>
₹".number_format((float)$c['price'])."
</div>
<div class='muted' style='margin-top:6px'>
Seller: ".e($c['seller'])."
</div>

<div style='margin-top:10px'>
<span style='background:$statusColor;color:white;padding:6px 14px;border-radius:20px;font-size:13px;font-weight:bold'>
".ucfirst($c['status'])."
</span>
</div>

<div class='muted' style='margin-top:10px;font-size:13px'>
Mileage: ".e($c['mileage'])." km •
Fuel: ".e($c['fuel_type'])." •
Transmission: ".e($c['transmission'])."
</div>
<div class='muted' style='margin-top:6px;font-size:13px'>
".e(substr($c['description'],0,100))."...
</div>

<div style='margin-top:12px;display:flex;gap:8px;flex-wrap:wrap'>

<form method='POST'>
<input type='hidden' name='id' value='{$c['id']}'>
<button name='action' value='approve' class='btn primary'>Approve</button>
</form>

<form method='POST'>
<input type='hidden' name='id' value='{$c['id']}'>
<button name='action' value='reject' class='btn'>Reject</button>
</form>

<form method='POST' onsubmit=\"return confirm('Delete this car?')\">
<input type='hidden' name='id' value='{$c['id']}'>
<button name='action' value='delete' class='btn' style='background:#ef4444;color:white'>Delete</button>
</form>

</div>

</div>

</div>

";

}

}else{
echo "<p class='muted'>No pending cars</p>";
}

?>

</div>

<!-- ===================== -->
<!-- APPROVED CARS -->
<!-- ===================== -->

<h2 style="margin-top:40px">✅ Approved Cars</h2>

<div class="grid">

<?php

$res = mysqli_query($conn,"
SELECT c.*, s.name seller
FROM car_listings c
JOIN sellers s ON s.id=c.seller_id
WHERE c.status='approved'
ORDER BY c.id DESC
");

if($res && mysqli_num_rows($res)>0){

while($c=mysqli_fetch_assoc($res)){

    $img = $c['image_path'] ?: "/carconnect/assets/images/default_car.jpg";

    $statusColor = "#888";

    switch($c['status']){

        case "approved":
            $statusColor = "#16a34a";
            break;

        case "pending":
            $statusColor = "#f59e0b";
            break;

        case "rejected":
            $statusColor = "#ef4444";
            break;

        case "sold":
            $statusColor = "#7c3aed";
            break;
    }

    echo "
<div class='card' style='border-radius:18px;overflow:hidden;box-shadow:0 10px 25px rgba(0,0,0,.12);background:#fff;'>

<img src='".e($img)."' style='width:100%;height:220px;object-fit:cover;'>

<div class='p' style='padding:18px;'>

<div style='display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;'>

<h3 style='margin:0;font-size:22px;font-weight:700;'>
".e($c['make'])." ".e($c['model'])."
</h3>

<span style='background:$statusColor;color:#fff;padding:6px 14px;border-radius:20px;font-size:13px;font-weight:700;'>
".ucfirst($c['status'])."
</span>

</div>

<div style='font-size:30px;font-weight:800;color:#2563eb;margin-bottom:15px;'>
₹".number_format((float)$c['price'])."
</div>

<div style='display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:15px;font-size:14px;'>

<div>
📅 <b>Year</b><br>
".e($c['year'])."
</div>

<div>
👤 <b>Seller</b><br>
".e($c['seller'])."
</div>

<div>
⛽ <b>Fuel</b><br>
".e($c['fuel_type'])."
</div>

<div>
⚙️ <b>Transmission</b><br>
".e($c['transmission'])."
</div>

<div>
🚗 <b>Mileage</b><br>
".number_format($c['mileage'])." km
</div>

<div>
📍 <b>Location</b><br>
".e($c['location'])."
</div>

</div>

<div style='color:#666;line-height:1.6;margin-bottom:18px;'>

".e(substr($c['description'],0,100))."...

</div>

<div style='display:flex;gap:10px;flex-wrap:wrap;'>

<a href='car_details.php?id=".$c['id']."' class='btn primary'>
👁 View
</a>

<form method='POST' onsubmit=\"return confirm('Delete this car?')\">

<input type='hidden' name='id' value='".$c['id']."'>

<button name='action' value='delete'
class='btn'
style='background:#ef4444;color:white'>
🗑 Delete
</button>

</form>

</div>

</div>

</div>

";

}

}else{
echo "<p class='muted'>No approved cars</p>";
}

?>

</div>

<style>

.stats-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:20px;
    margin:25px 0;
}

.stat-card{
    background:#fff;
    border-radius:15px;
    padding:25px;
    text-align:center;
    box-shadow:0 8px 20px rgba(0,0,0,.08);
}

.stat-card h2{
    font-size:36px;
    color:#2563eb;
    margin:0;
}

.stat-card p{
    margin-top:8px;
    color:#666;
    font-weight:600;
}

.grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(320px,1fr));
    gap:25px;
    margin-top:20px;
}

.card{
    background:#fff;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
}

.card img{
    width:100%;
    display:block;
}

@media(max-width:900px){
    .stats-grid{
        grid-template-columns:repeat(2,1fr);
    }
}

@media(max-width:600px){
    .stats-grid{
        grid-template-columns:1fr;
    }
}

</style>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>