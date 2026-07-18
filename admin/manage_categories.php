<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("admin");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$err = "";
$ok = "";

if($_SERVER['REQUEST_METHOD'] === "POST"){
    $name = trim($_POST['name'] ?? '');

    if($name === ""){
        $err = "Category name is required.";
    } else {
        $stmt = mysqli_prepare($conn,"
            INSERT INTO car_categories(name)
            VALUES(?)
        ");
        mysqli_stmt_bind_param($stmt,"s",$name);

        if(mysqli_stmt_execute($stmt)){
            $ok = "Category added successfully ✅";
        } else {
            $err = "Category already exists or failed.";
        }
    }
}

if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];

    $stmt = mysqli_prepare($conn,"
        DELETE FROM car_categories WHERE id=?
    ");
    mysqli_stmt_bind_param($stmt,"i",$id);
    mysqli_stmt_execute($stmt);

    header("Location: manage_categories.php?msg=deleted");
    exit();
}

if(isset($_GET['msg']) && $_GET['msg']=="deleted"){
    $ok = "Category deleted successfully 🗑️";
}
?>

<h1>🏷️ Manage Car Categories</h1>

<?php if($err): ?>
<div class="alert"><?php echo e($err); ?></div>
<?php endif; ?>

<?php if($ok): ?>
<div class="alert success"><?php echo e($ok); ?></div>
<?php endif; ?>

<form method="POST" class="form" style="max-width:500px;margin-bottom:25px">
    <label>Category Name</label>
    <input class="input" name="name" placeholder="Example: SUV, Sedan, Hatchback" required>

    <div style="margin-top:15px">
        <button class="btn primary">Add Category</button>
    </div>
</form>

<table class="table">
<tr>
<th>ID</th>
<th>Category</th>
<th>Created</th>
<th>Action</th>
</tr>

<?php
$res = mysqli_query($conn,"
SELECT * FROM car_categories ORDER BY id DESC
");

if($res && mysqli_num_rows($res)>0){

while($c=mysqli_fetch_assoc($res)){

echo "
<tr>
<td>#".intval($c['id'])."</td>
<td>".e($c['name'])."</td>
<td>".e($c['created_at'])."</td>
<td>
<a class='btn' 
href='manage_categories.php?delete=".intval($c['id'])."'
onclick=\"return confirm('Delete this category?')\">
Delete
</a>
</td>
</tr>
";

}

}else{
echo "<tr><td colspan='4' style='text-align:center'>No categories added yet</td></tr>";
}
?>

</table>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>