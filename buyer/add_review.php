<?php
require_once "../core/middleware.php";
requireRole("buyer");

require_once "../includes/db_connect.php";
require_once "../includes/functions.php";
require_once "../includes/header.php";

$buyerId = (int)$_SESSION['user_id'];
$carId   = (int)($_GET['car_id'] ?? 0);

$err = "";
$ok  = "";

/* ===================== */
/* VALIDATE CAR */
/* ===================== */

$checkCar = mysqli_prepare($conn,"SELECT id FROM car_listings WHERE id=?");
mysqli_stmt_bind_param($checkCar,"i",$carId);
mysqli_stmt_execute($checkCar);
$resCar = mysqli_stmt_get_result($checkCar);

if(mysqli_num_rows($resCar) == 0){
  echo "<div class='alert'>Invalid car</div>";
  require_once "../includes/footer.php";
  exit();
}

/* ===================== */
/* CHECK ALREADY REVIEWED */
/* ===================== */

$check = mysqli_prepare($conn,"
SELECT id FROM reviews WHERE car_id=? AND user_id=?
");
mysqli_stmt_bind_param($check,"ii",$carId,$buyerId);
mysqli_stmt_execute($check);
$resCheck = mysqli_stmt_get_result($check);

if(mysqli_num_rows($resCheck) > 0){
  echo "<div class='alert'>You already reviewed this car ⭐</div>";
  require_once "../includes/footer.php";
  exit();
}

/* ===================== */
/* HANDLE POST */
/* ===================== */

if($_SERVER['REQUEST_METHOD']=="POST"){

  $rating  = (int)($_POST['rating'] ?? 0);
  $comment = trim($_POST['comment'] ?? '');

  if($rating < 1 || $rating > 5){
    $err = "Rating must be between 1 and 5";
  }
  elseif($comment === ""){
    $err = "Comment cannot be empty";
  }
  else{

    $stmt = mysqli_prepare($conn,"
    INSERT INTO reviews(car_id,user_id,rating,comment)
    VALUES(?,?,?,?)
    ");

    mysqli_stmt_bind_param($stmt,"iiis",$carId,$buyerId,$rating,$comment);

   if(mysqli_stmt_execute($stmt)){

    echo "<pre>";
    echo "Review inserted successfully";
    echo "</pre>";

} else {

    die(mysqli_error($conn));

}
    mysqli_stmt_close($stmt);
  }
}
?>

<h2>⭐ Add Review</h2>

<?php if($err): ?>
<div class="alert"><?php echo e($err); ?></div>
<?php endif; ?>

<?php if($ok): ?>
<div class="alert success"><?php echo e($ok); ?></div>
<?php endif; ?>


<form method="POST" class="form">

<!-- RATING -->
<label>Rating</label>
<select class="input" name="rating" required>
<option value="">Select Rating</option>
<option value="5">⭐⭐⭐⭐⭐ (5)</option>
<option value="4">⭐⭐⭐⭐ (4)</option>
<option value="3">⭐⭐⭐ (3)</option>
<option value="2">⭐⭐ (2)</option>
<option value="1">⭐ (1)</option>
</select>

<!-- COMMENT -->
<label>Comment</label>
<textarea 
  class="input" 
  name="comment" 
  rows="4" 
  placeholder="Write your experience..."
  required
></textarea>

<button class="btn primary mt-10">Submit Review</button>

</form>

<?php require_once "../includes/footer.php"; ?>