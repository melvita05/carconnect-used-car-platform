<?php
session_start();

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/header.php";

$err = "";
$ok = "";

if($_SERVER['REQUEST_METHOD'] === "POST"){

    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if(!$name || !$email || !$password || !$role){
        $err = "All fields are required";
    } else {

        /* ===================== */
        /* 🔍 CHECK DUPLICATE EMAIL */
        /* ===================== */

        $exists = false;

        // CHECK BUYERS
        $check = mysqli_prepare($conn,"SELECT id FROM buyers WHERE email=?");
        mysqli_stmt_bind_param($check,"s",$email);
        mysqli_stmt_execute($check);
        $res = mysqli_stmt_get_result($check);
        if(mysqli_num_rows($res)>0) $exists = true;

        // CHECK SELLERS
        $check = mysqli_prepare($conn,"SELECT id FROM sellers WHERE email=?");
        mysqli_stmt_bind_param($check,"s",$email);
        mysqli_stmt_execute($check);
        $res = mysqli_stmt_get_result($check);
        if(mysqli_num_rows($res)>0) $exists = true;

        /* ===================== */
        /* 🚫 IF EMAIL EXISTS */
        /* ===================== */

        if($exists){
            $err = "Email already registered. Please login.";
        }

        /* ===================== */
        /* ✅ INSERT USER */
        /* ===================== */

        else{

            $hashed = password_hash($password, PASSWORD_DEFAULT);

            if($role == "buyer"){
                $stmt = mysqli_prepare($conn,"
                INSERT INTO buyers(name,email,password)
                VALUES(?,?,?)
                ");
                mysqli_stmt_bind_param($stmt,"sss",$name,$email,$hashed);
            }

            elseif($role == "seller"){
                $stmt = mysqli_prepare($conn,"
                INSERT INTO sellers(name,email,password)
                VALUES(?,?,?)
                ");
                mysqli_stmt_bind_param($stmt,"sss",$name,$email,$hashed);
            }

            if(mysqli_stmt_execute($stmt)){

                // redirect immediately
                header("Location: /carconnect/auth/login.php");
                exit();

            } else {
                $err = "Registration failed. Try again.";
            }
        }
    }
}
?>

<h2 class="center mt-20">Create Account</h2>

<?php if($err) echo "<div class='alert'>$err</div>"; ?>

<form method="POST" class="form">

    <label>Select Role</label>
    <select name="role" class="input" required>
        <option value="">-- Select --</option>
        <option value="buyer">Buyer</option>
        <option value="seller">Seller</option>
    </select>

    <label>Name</label>
    <input class="input" name="name" required>

    <label>Email</label>
    <input class="input" type="email" name="email" required>

    <label>Password</label>
    <input class="input" type="password" name="password" required>

    <div class="center mt-20">
        <button class="btn primary">Register</button>
    </div>

    <p class="center mt-20 muted">
        Already have an account? 
        <a href="/carconnect/auth/login.php" style="color:var(--primary);font-weight:700">
            Login here
        </a>
    </p>

</form>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>