<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("buyer");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$buyerId = (int)$_SESSION['user_id'];
$carId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

/* ===========================
   FETCH CAR DETAILS
=========================== */

$stmt = mysqli_prepare($conn,"
SELECT id,seller_id,make,model,price,status
FROM car_listings
WHERE id=?
LIMIT 1
");

mysqli_stmt_bind_param($stmt,"i",$carId);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$car = mysqli_fetch_assoc($result);

if(!$car){
    die("<div class='alert'>Car not found.</div>");
}

if($car['status']!="approved"){
    die("<div class='alert'>This car is not available for purchase.</div>");
}


/* ===========================
   PAYMENT PROCESS
=========================== */

if($_SERVER['REQUEST_METHOD']=="POST"){

    $paymentMethod = trim($_POST['payment_method']);

    $upi = trim($_POST['upi'] ?? "");
    $cardNumber = trim($_POST['card_number'] ?? "");
    $expiry = trim($_POST['expiry'] ?? "");
    $cvv = trim($_POST['cvv'] ?? "");

    /* ===========================
       VALIDATION
    =========================== */

    if($paymentMethod==""){
        die("<div class='alert'>Select payment method.</div>");
    }

    if ($paymentMethod == "UPI") {

    if ($upi == "") {
        die("<div class='alert'>Please enter your UPI ID.</div>");
    }

    if (!preg_match('/^[a-zA-Z0-9.\-_]{2,}@[a-zA-Z]{2,}$/', $upi)) {
        die("<div class='alert'>Invalid UPI ID. Example: user@okaxis</div>");
    }

}

    if($paymentMethod=="Credit Card" || $paymentMethod=="Debit Card"){

        if($cardNumber=="" || $expiry=="" || $cvv==""){
            die("<div class='alert'>Complete card details.</div>");
        }

    }

    /* ===========================
       CHECK IF ALREADY SOLD
    =========================== */

    $stmt = mysqli_prepare($conn,"
    SELECT status
    FROM car_listings
    WHERE id=?
    ");

    mysqli_stmt_bind_param($stmt,"i",$carId);
    mysqli_stmt_execute($stmt);

    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if($row['status']=="sold"){

        die("<div class='alert'>Sorry! This car has already been sold.</div>");

    }

    /* ===========================
       CHECK EXISTING ORDER
    =========================== */

    $stmt = mysqli_prepare($conn,"
    SELECT id
    FROM orders
    WHERE car_id=?
    LIMIT 1
    ");

    mysqli_stmt_bind_param($stmt,"i",$carId);
    mysqli_stmt_execute($stmt);

    if(mysqli_num_rows(mysqli_stmt_get_result($stmt))>0){

        die("<div class='alert'>Order already exists.</div>");

    }

    mysqli_begin_transaction($conn);

    try{

        /* ===========================
           CREATE ORDER
        =========================== */

        $sellerId = (int)$car['seller_id'];
        $amount = (float)$car['price'];

        $stmt = mysqli_prepare($conn,"
        INSERT INTO orders
        (
        car_id,
        buyer_id,
        seller_id,
        total_price,
        order_status
        )
        VALUES
        (
        ?,
        ?,
        ?,
        ?,
'pending'        )
        ");

        mysqli_stmt_bind_param(
        $stmt,
        "iiid",
        $carId,
        $buyerId,
        $sellerId,
        $amount
        );

        mysqli_stmt_execute($stmt);

        $orderId = mysqli_insert_id($conn);

        /* ===========================
           SAVE PAYMENT
        =========================== */

        $paymentStatus="paid";

        $stmt = mysqli_prepare($conn,"
        INSERT INTO payments
        (
        order_id,
        buyer_id,
        amount,
        payment_method,
        payment_status
        )
        VALUES
        (
        ?,
        ?,
        ?,
        ?,
        ?
        )
        ");

        mysqli_stmt_bind_param(
        $stmt,
        "iidss",
        $orderId,
        $buyerId,
        $amount,
        $paymentMethod,
        $paymentStatus
        );

        mysqli_stmt_execute($stmt);

        /* ===========================
           MARK CAR AS SOLD
        =========================== */
/*
        $stmt = mysqli_prepare($conn,"
        UPDATE car_listings
        SET status='sold'
        WHERE id=?
        ");

        mysqli_stmt_bind_param($stmt,"i",$carId);
        mysqli_stmt_execute($stmt);
*/
       mysqli_commit($conn);

$_SESSION['order_id'] = $orderId;

header("Location: payment_success.php");
exit();

    }
    catch(Exception $e){

        mysqli_rollback($conn);

        header("Location: payment_failed.php");
        exit();

    }

}
?>    
 <h1 style="text-align:center;margin-bottom:20px;">
    Secure Checkout
</h1>

<div class="checkout-container">

    <!-- Order Summary -->

    <div class="checkout-card">

        <h2>Order Summary</h2>

        <hr>

        <p><strong>Vehicle</strong></p>

        <h3>
            <?php echo e($car['make'])." ".e($car['model']); ?>
        </h3>

        <br>

        <p><strong>Price</strong></p>

        <h2 style="color:#0d6efd;">
            ₹<?php echo number_format($car['price']); ?>
        </h2>

    </div>

    <!-- Payment -->

    <div class="checkout-card">

        <h2>Payment Details</h2>

        <hr>

        <form method="POST" id="paymentForm">

            <!-- Payment Method -->

           <label>Select Payment Method</label>

<select
    name="payment_method"
    id="payment_method"
    class="input"
    onchange="changePayment()"
    required>

    <option value="">Choose Payment Method</option>

    <option value="UPI">UPI</option>
    <option value="Credit Card">Credit Card</option>
    <option value="Debit Card">Debit Card</option>
    <option value="Net Banking">Net Banking</option>

</select>

<!-- ================= UPI ================= -->

<div id="upiSection" style="display:none;">

    <label style="margin-top:15px;">UPI ID</label>

    <input
        type="text"
        class="input"
        name="upi"
        id="upi"
        placeholder="example@okaxis">

</div>

<!-- ================= CARD ================= -->

<div id="cardSection" style="display:none;">

    <label style="margin-top:15px;">Card Holder Name</label>

    <input
        type="text"
        class="input"
        name="card_name"
        id="card_name"
        placeholder="Name">

    <label>Card Number</label>

    <input
        type="text"
        class="input"
        name="card_number"
        id="card_number"
        maxlength="16"
        inputmode="numeric"
        placeholder="1234567812345678">

    <label>Expiry (MM/YY)</label>

    <input
        type="text"
        class="input"
        name="expiry"
        id="expiry"
        maxlength="5"
        placeholder="12/28">

    <label>CVV</label>

    <input
        type="password"
        class="input"
        name="cvv"
        id="cvv"
        maxlength="3"
        inputmode="numeric"
        placeholder="123">

</div>

<!-- ================= NET BANKING ================= -->

<div id="bankSection" style="display:none;">

    <label style="margin-top:15px;">Select Bank</label>

    <select
        class="input"
        name="bank"
        id="bank">

        <option value="">Choose Bank</option>

        <option>SBI</option>
        <option>HDFC</option>
        <option>ICICI</option>
        <option>Axis Bank</option>
        <option>Canara Bank</option>
        <option>Kotak Bank</option>

    </select>

    <label style="margin-top:15px;">Customer ID</label>

    <input
        type="text"
        class="input"
        name="customer_id"
        id="customer_id"
        placeholder="Customer ID">

</div>

<!-- ================= OTP ================= -->



<button
    class="btn primary"
    id="payBtn"
    type="submit"
    style="width:100%;margin-top:25px;">

    Pay ₹<?php echo number_format($car['price']); ?>

</button>

</form>

</div>

</div>
<style>

body{
    background:#f5f7fb;
    font-family:Arial, Helvetica, sans-serif;
}

.checkout-container{

    max-width:1000px;

    margin:40px auto;

    display:grid;

    grid-template-columns:1fr 1fr;

    gap:30px;

}

.checkout-card{

    background:#fff;

    border-radius:12px;

    padding:30px;

    box-shadow:0 5px 20px rgba(0,0,0,.08);

}

.checkout-card h2{

    margin-bottom:15px;

    color:#0d47a1;

}

.checkout-card h3{

    margin:0;

    color:#222;

}

.checkout-card p{

    color:#666;

    margin-bottom:5px;

}

.checkout-card hr{

    border:none;

    border-top:1px solid #eee;

    margin:20px 0;

}

label{

    display:block;

    margin-top:15px;

    margin-bottom:6px;

    font-weight:bold;

}

.input{

    width:100%;

    padding:12px;

    border:1px solid #ccc;

    border-radius:8px;

    font-size:15px;

    outline:none;

    transition:.3s;

    box-sizing:border-box;

}

.input:focus{

    border-color:#0d6efd;

    box-shadow:0 0 6px rgba(13,110,253,.25);

}

.btn{

    padding:14px;

    border:none;

    border-radius:8px;

    cursor:pointer;

    font-size:16px;

    transition:.3s;

}

.btn.primary{

    background:#0d6efd;

    color:white;

}

.btn.primary:hover{

    background:#0b5ed7;

}

.payment-box{

    background:#f8f9fa;

    border:1px solid #ddd;

    border-radius:8px;

    padding:15px;

    margin-top:15px;

}

.secure-text{

    margin-top:20px;

    text-align:center;

    color:#28a745;

    font-size:14px;

}

@media(max-width:768px){

.checkout-container{

grid-template-columns:1fr;

}

}

</style> 
 <script>

function changePayment(){

    let method = document.getElementById("payment_method").value;

    document.getElementById("upiSection").style.display = "none";
    document.getElementById("cardSection").style.display = "none";
    document.getElementById("bankSection").style.display = "none";
  
    if(method=="UPI"){

        document.getElementById("upiSection").style.display="block";
        

    }

    if(method=="Credit Card" || method=="Debit Card"){

        document.getElementById("cardSection").style.display="block";

    }

    if(method=="Net Banking"){

        document.getElementById("bankSection").style.display="block";

    }

}

document.getElementById("paymentForm").addEventListener("submit", function(e){

    let method = document.getElementById("payment_method").value;

    if(method==""){

        alert("Please select a payment method.");
        e.preventDefault();
        return;

    }

    /* ================= UPI ================= */

    if(method=="UPI"){

        let upi=document.getElementById("upi").value.trim();

        let pattern=/^[a-zA-Z0-9._-]{2,}@[a-zA-Z]{2,}$/;

        if(!pattern.test(upi)){

            alert("Enter a valid UPI ID.\nExample: user@okaxis");
            e.preventDefault();
            return;

        }

    }

    /* ================= CREDIT / DEBIT CARD ================= */

    if(method=="Credit Card" || method=="Debit Card"){

        let name=document.getElementById("card_name").value.trim();

        let card=document.getElementById("card_number").value.trim();

        let expiry=document.getElementById("expiry").value.trim();

        let cvv=document.getElementById("cvv").value.trim();

        if(!/^[A-Za-z ]{3,}$/.test(name)){

            alert("Enter a valid Card Holder Name.");
            e.preventDefault();
            return;

        }

        if(!/^\d{16}$/.test(card)){

            alert("Card Number must be exactly 16 digits.");
            e.preventDefault();
            return;

        }

        if(!/^(0[1-9]|1[0-2])\/\d{2}$/.test(expiry)){

            alert("Expiry must be in MM/YY format.");
            e.preventDefault();
            return;

        }

        if(!/^\d{3}$/.test(cvv)){

            alert("CVV must be exactly 3 digits.");
            e.preventDefault();
            return;

        }

    }

    /* ================= NET BANKING ================= */

    if(method=="Net Banking"){

        let bank=document.getElementById("bank").value;

        let customer=document.getElementById("customer_id").value.trim();

        if(bank==""){

            alert("Please select your bank.");
            e.preventDefault();
            return;

        }

        if(customer.length < 5){

            alert("Customer ID must contain at least 5 characters.");
            e.preventDefault();
            return;

        }

    }

    /* ================= OTP ================= */


    document.getElementById("payBtn").disabled=true;

    document.getElementById("payBtn").innerHTML="Processing Payment...";

});

</script>         
<?php require_once __DIR__ . "/../includes/footer.php"; ?>                                                                                               