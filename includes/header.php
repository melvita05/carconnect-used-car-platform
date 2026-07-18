<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$current = basename($_SERVER['PHP_SELF']);
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Online Car Connect</title>

  <link rel="stylesheet" href="/carconnect/assets/css/style.css">
  <link rel="stylesheet" href="/carconnect/assets/css/responsive.css">
</head>

<body>

<header class="nav">
  <div class="nav__inner">

    <!-- LOGO -->
    <a class="brand" href="/carconnect/index.php">
      OnlineCarConnect
    </a>

    <!-- NAV LINKS -->
    <nav class="nav__links">

      <?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>

        <!-- ✅ ADMIN VIEW (ONLY DASHBOARD) -->

        <a href="/carconnect/admin/admin_dashboard.php">Dashboard</a>

        <span class="user-badge">
          👤 Admin
        </span>

        <a class="btn" href="/carconnect/auth/logout.php">Logout</a>

      <?php else: ?>

        <!-- ✅ NORMAL USERS -->

        <a class="<?php echo ($current=='index.php')?'active':''; ?>" 
           href="/carconnect/index.php">Home</a>

        <a class="<?php echo ($current=='about.php')?'active':''; ?>" 
           href="/carconnect/about.php">About</a>

        <a href="/carconnect/cars.php">Browse Cars</a>

        <?php if (!empty($_SESSION['role'])): ?>

          <!-- DASHBOARD -->

          <?php if ($_SESSION['role'] === 'buyer'): ?>
            <a href="/carconnect/buyer/home.php">Dashboard</a>

          <?php elseif ($_SESSION['role'] === 'seller'): ?>
            <a href="/carconnect/seller/seller_dashboard.php">Seller Panel</a>

          <?php endif; ?>

          <!-- USER BADGE -->
          <span class="user-badge">
            👤 <?php echo ucfirst($_SESSION['role']); ?>
          </span>

          <!-- LOGOUT -->
          <a class="btn" href="/carconnect/auth/logout.php">Logout</a>

        <?php else: ?>

          <!-- LOGIN / REGISTER -->

          <a class="btn" href="/carconnect/auth/login.php">
            Login
          </a>

          <a class="btn primary" href="/carconnect/auth/register.php">
            Register
          </a>

        <?php endif; ?>

      <?php endif; ?>

    </nav>

  </div>
</header>

<main class="container">