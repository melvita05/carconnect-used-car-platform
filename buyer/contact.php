<?php
require_once "../core/middleware.php";
requireRole("buyer");

require_once "../includes/db_connect.php";
require_once "../includes/functions.php";
require_once "../includes/header.php";

$user = (int)$_SESSION['user_id'];
$error = "";
$success = "";

/* ===================== */
/* HANDLE FORM */
/* ===================== */

if($_SERVER['REQUEST_METHOD'] === "POST"){

  $text = trim($_POST['message'] ?? '');

  if($text === ""){
    $error = "⚠️ Please enter a message";
  }
  elseif(strlen($text) < 5){
    $error = "⚠️ Message too short";
  }
  else{

    $stmt = mysqli_prepare($conn,"
      INSERT INTO contact_messages (user_id,message)
      VALUES (?,?)
    ");

    mysqli_stmt_bind_param($stmt,"is",$user,$text);

    if(mysqli_stmt_execute($stmt)){
      $success = "✅ Message sent to admin successfully";
    }else{
      $error = "❌ Something went wrong. Try again";
    }

    mysqli_stmt_close($stmt);
  }
}
?>

<h2>📞 Contact Admin</h2>

<p class="muted">
Have any issue or question? Send a message to admin.
</p>

<!-- ALERTS -->
<?php if($error): ?>
<div class="alert"><?php echo e($error); ?></div>
<?php endif; ?>

<?php if($success): ?>
<div class="alert success"><?php echo e($success); ?></div>
<?php endif; ?>

<!-- FORM -->
<form method="POST" class="form" style="max-width:600px">

<label>Your Message</label>

<textarea 
name="message" 
class="input" 
rows="5" 
placeholder="Type your message..."
required
><?php echo isset($_POST['message']) ? e($_POST['message']) : ''; ?></textarea>

<button class="btn primary mt-10">
📨 Send Message
</button>

</form>

<?php require_once "../includes/footer.php"; ?>