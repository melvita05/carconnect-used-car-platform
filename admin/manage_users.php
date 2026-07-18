<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("admin");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

/* ===================== */
/* ACTION HANDLER */
/* ===================== */

if(isset($_GET['action']) && isset($_GET['id']) && isset($_GET['type'])){

  $id   = intval($_GET['id']);
  $type = $_GET['type'];
  $action = $_GET['action'];

  $table = ($type === "buyer") ? "buyers" : "sellers";

  if($action === "activate"){
    mysqli_query($conn,"UPDATE $table SET status='active' WHERE id=$id");
  }

  if($action === "deactivate"){
    mysqli_query($conn,"UPDATE $table SET status='inactive' WHERE id=$id");
  }

  if($action === "delete"){
    mysqli_query($conn,"DELETE FROM $table WHERE id=$id");
  }

  echo "<div class='alert success'>Action completed ✅</div>";
}
?>

<h1>👥 Manage Users</h1>

<!-- ===================== -->
<!-- BUYERS SECTION -->
<!-- ===================== -->

<h2 style="margin-top:20px">🧑 Buyers</h2>

<table class="table">

<tr>
<th>Name</th>
<th>Email</th>
<th>Status</th>
<th>Actions</th>
</tr>

<?php

$buyers = mysqli_query($conn,"
SELECT id,name,email,status 
FROM buyers 
ORDER BY created_at DESC
");

if(mysqli_num_rows($buyers)>0){

while($u = mysqli_fetch_assoc($buyers)){

$status = e($u['status']);
$color = ($status === "active") ? "#16a34a" : "#ef4444";

echo "<tr>

<td>".e($u['name'])."</td>

<td>".e($u['email'])."</td>

<td>
<span style='color:$color;font-weight:700'>
$status
</span>
</td>

<td style='display:flex;gap:8px'>

<a class='btn'
href='?action=activate&id=".$u['id']."&type=buyer'>
Activate
</a>

<a class='btn'
href='?action=deactivate&id=".$u['id']."&type=buyer'>
Deactivate
</a>

<a class='btn'
style='background:#ef4444;color:#fff'
onclick=\"return confirm('Delete this user?')\"
href='?action=delete&id=".$u['id']."&type=buyer'>
Delete
</a>

</td>

</tr>";
}

}else{
echo "<tr><td colspan='4'>No buyers found</td></tr>";
}
?>

</table>


<!-- ===================== -->
<!-- SELLERS SECTION -->
<!-- ===================== -->

<h2 style="margin-top:30px">🧑‍💼 Sellers</h2>

<table class="table">

<tr>
<th>Name</th>
<th>Email</th>
<th>Status</th>
<th>Actions</th>
</tr>

<?php

$sellers = mysqli_query($conn,"
SELECT id,name,email,status 
FROM sellers 
ORDER BY created_at DESC
");

if(mysqli_num_rows($sellers)>0){

while($u = mysqli_fetch_assoc($sellers)){

$status = e($u['status']);
$color = ($status === "active") ? "#16a34a" : "#ef4444";

echo "<tr>

<td>".e($u['name'])."</td>

<td>".e($u['email'])."</td>

<td>
<span style='color:$color;font-weight:700'>
$status
</span>
</td>

<td style='display:flex;gap:8px'>

<a class='btn'
href='?action=activate&id=".$u['id']."&type=seller'>
Activate
</a>

<a class='btn'
href='?action=deactivate&id=".$u['id']."&type=seller'>
Deactivate
</a>

<a class='btn'
style='background:#ef4444;color:#fff'
onclick=\"return confirm('Delete this seller?')\"
href='?action=delete&id=".$u['id']."&type=seller'>
Delete
</a>

</td>

</tr>";
}

}else{
echo "<tr><td colspan='4'>No sellers found</td></tr>";
}
?>

</table>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>