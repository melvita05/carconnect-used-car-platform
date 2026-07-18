</main>

<?php
$role = $_SESSION['role'] ?? '';
?>

<?php if($role === 'admin'): ?>

<!-- ✅ ADMIN SIMPLE FOOTER -->
<footer style="
  text-align:center;
  padding:15px;
  color:#666;
  border-top:1px solid #eee;
  margin-top:30px;
">
  ©️ <?php echo date("Y"); ?> CarConnect. All rights reserved.
</footer>

<?php else: ?>

<!-- ✅ NORMAL FULL FOOTER -->
<footer class="footer">

  <div class="container footer-grid">

    <!-- BRAND -->
    <div>
      <h3 class="footer-title">🚗 CarConnect</h3>
      <p class="muted">
        India’s trusted marketplace to buy and sell used cars with ease and transparency.
      </p>
    </div>

    <!-- QUICK LINKS -->
    <div>
      <h4 class="footer-sub">Quick Links</h4>
      <ul class="footer-links">
        <li><a href="/carconnect/index.php">Home</a></li>
        <li><a href="/carconnect/about.php">About</a></li>
        <li><a href="/carconnect/cars.php">Browse Cars</a></li>
      </ul>
    </div>

    <!-- USER LINKS -->
    <div>
      <h4 class="footer-sub">Account</h4>
      <ul class="footer-links">
        <li><a href="/carconnect/auth/login.php">Login</a></li>
        <li><a href="/carconnect/auth/register.php">Register</a></li>
      </ul>
    </div>

    <!-- INFO -->
    <div>
      <h4 class="footer-sub">Contact</h4>
      <p class="muted">📧 support@carconnect.com</p>
      <p class="muted">📍 India</p>
    </div>

  </div>

  <!-- BOTTOM BAR -->
  <div class="footer-bottom">
    ©️ <?php echo date("Y"); ?> CarConnect. All rights reserved.
  </div>

</footer>

<?php endif; ?>

<script src="/carconnect/assets/js/main.js"></script>
<script src="/carconnect/assets/js/validation.js"></script>

</body>
</html>