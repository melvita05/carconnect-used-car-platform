<?php require_once __DIR__ . "/includes/header.php"; require_once __DIR__ . "/includes/db_connect.php"; require_once __DIR__ . "/includes/functions.php"; ?>

<h1>Contact Us</h1>
<form class="form" method="POST" action="/carconnect/contact.php" data-validate>
  <label>Name</label>
  <input class="input" name="name" required>

  <label>Email</label>
  <input class="input" type="email" name="email" required>

  <label>Message</label>
  <textarea class="input" name="message" rows="5" required></textarea>

  <div style="margin-top:12px">
    <button class="btn primary" type="submit">Send</button>
  </div>
</form>

<?php
if($_SERVER['REQUEST_METHOD']==='POST'){
  $name = post('name');
  $email = post('email');
  $message = post('message');

  if(!$name || !$message || !is_valid_email($email)){
    echo '<div class="alert">Please valid details daalo.</div>';
  } else {
    $stmt = mysqli_prepare($conn, "INSERT INTO contact_messages(name,email,message) VALUES (?,?,?)");
    mysqli_stmt_bind_param($stmt, "sss", $name, $email, $message);
    mysqli_stmt_execute($stmt);
    echo '<div class="alert success">Message sent ✅</div>';
  }
}
?>

<?php require_once __DIR__ . "/includes/footer.php"; ?>
