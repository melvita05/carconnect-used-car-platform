<?php 
require_once __DIR__ . "/includes/header.php"; 
require_once __DIR__ . "/includes/db_connect.php"; 
require_once __DIR__ . "/includes/functions.php";

// Check if user is logged in and fetch the role
$isLoggedIn = !empty($_SESSION['user_id']);
$role = $_SESSION['role'] ?? ''; // Check the role of the logged-in user
?>

<!-- HERO Section -->
<section class="hero">
  <div>
    <span class="badge">🚗 India’s Trusted Used Car Marketplace</span>
    <h1>Buy & Sell Cars Easily</h1>
    <p>
      Discover verified listings from trusted sellers. 
      Find your dream car or sell your vehicle quickly with zero middlemen.
    </p>

    <div class="hero-actions">
      <?php if($isLoggedIn): ?>
        <!-- Update link based on role -->
        <?php if($role == 'admin'): ?>
          <a class="btn primary" href="/carconnect/cars.php?role=admin">Browse Cars</a>
        <?php elseif($role == 'seller'): ?>
          <a class="btn primary" href="/carconnect/cars.php?role=seller">Browse Cars</a>
        <?php else: ?>
          <a class="btn primary" href="/carconnect/cars.php?role=buyer">Browse Cars</a>
        <?php endif; ?>
      <?php else: ?>
        <a class="btn primary" href="/carconnect/auth/login.php">Browse Cars</a>
      <?php endif; ?>

      <a class="btn" href="/carconnect/auth/register.php">Sell Your Car</a>
    </div>
  </div>

  <div class="card">
    <img src="https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=900&q=80">  <!-- Hero image -->
  </div>
</section>

<!-- BRANDS Section -->
<section class="section">
  <h2 class="section-title">Browse Cars by Brand</h2>

  <div class="grid brand-grid">
    <?php
    $brands = ["Maruti", "Tata", "Mahindra", "Hyundai", "Honda", "Toyota"];

    foreach($brands as $name):
        // Update link with the role parameter
        $link = $isLoggedIn 
            ? "/carconnect/cars.php?brand=" . $name . "&role=" . $role  // Add role to the URL dynamically
            : "/carconnect/auth/login.php";
        
        echo "
        <a href='$link' class='card brand-card'>
          <div class='brand-name'>$name</div>
        </a>";
    endforeach;
    ?>
  </div>
</section>

<!-- FEATURED CARS Section -->
<section class="section">
  <h2 class="section-title">Latest Cars</h2>

  <?php if($isLoggedIn): ?>
    <div class="grid">
      <?php
      // Fetch the role dynamically from the session and adjust URL accordingly
      $stmt = mysqli_prepare($conn, "
        SELECT id, make, model, year, price, image_path
        FROM car_listings
        WHERE status = 'approved'
        ORDER BY created_at DESC
        LIMIT 8
      ");

      mysqli_stmt_execute($stmt);
      $res = mysqli_stmt_get_result($stmt);

      while($c = mysqli_fetch_assoc($res)): 
        $img = $c['image_path'] ?: "/carconnect/assets/images/default_car.jpg";  // Fallback image
      ?>

      <div class="card car-card">
        <img src="<?php echo e($img); ?>" alt="Car image">
        <div class="p">
          <div class="car-title">
            <?php echo e($c['make'] . " " . $c['model']); ?>
          </div>
          <div class="muted"><?php echo e($c['year']); ?></div>
          <div class="price">
            ₹<?php echo number_format($c['price']); ?>
          </div>
          <a class="btn primary mt-10" href="/carconnect/<?php echo $role; ?>/car_details.php?id=<?php echo $c['id']; ?>">View Details</a>  <!-- Dynamic link -->
        </div>
      </div>

      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <div class="locked-box">
      <h3>🔒 Login Required</h3>
      <p class="muted">Login to view available cars and details.</p><br/>
      <a class="btn primary" href="/carconnect/auth/login.php">Login Now</a>
    </div>
  <?php endif; ?>
</section>

<!-- WHY US Section -->
<section class="section">
  <h2 class="section-title">Why Choose Online Car Connect</h2>
  <div class="grid three">
    <div class="card">
      <div class="p">
        <h3>🔒 Secure Transactions</h3>
        <p class="muted">Safe login and protected payments.</p>
      </div>
    </div>
    <div class="card">
      <div class="p">
        <h3>📊 Transparent Pricing</h3>
        <p class="muted">No hidden fees or middlemen.</p>
      </div>
    </div>
    <div class="card">
      <div class="p">
        <h3>⚡ Fast Selling</h3>
        <p class="muted">Sell your car quickly across India.</p>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . "/includes/footer.php"; ?>