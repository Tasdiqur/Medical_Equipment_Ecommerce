<?php /* cart.php */ ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MedREX — Cart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/styles.css" rel="stylesheet">
  <!-- cache-bust to ensure latest JS is loaded -->
  <script defer src="assets/js/app.js?v=20"></script>
</head>

<body class="bg-dark text-light">

  <!-- Top bar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-black py-2">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold" href="index.php"><span class="text-info">Med</span>REX</a>
      <div class="d-flex align-items-center gap-2">
        <a class="btn btn-outline-light btn-sm" href="login.php">Login</a>
        <a class="btn btn-info btn-sm" href="register.php">Sign Up</a>
        <a class="btn btn-outline-info btn-sm position-relative" href="cart.php">
          🛒 <span id="cartCount" class="badge bg-info text-dark position-absolute top-0 start-100 translate-middle rounded-pill">0</span>
        </a>
        <a href="#" class="btn btn-outline-light btn-sm position-relative" onclick="openNotif()">
  🔔 <span id="notifCount" class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill">0</span>
  </a>

      </div>
    </div>
  </nav>

  <main class="container py-4">
    <h2 class="mb-3">Your Cart</h2>

    <div class="mb-3">
      <a href="index.php" class="btn btn-outline-secondary">← Continue Shopping</a>
    </div>

    <!-- cart items will be rendered here -->
    <div id="cartList" class="row g-3"></div>

    <div class="d-flex justify-content-end align-items-center gap-3 mt-3">
      <div id="cartMsg" class="small text-warning"></div>
      <a href="payment.php" class="btn btn-info">Proceed to Payment</a>

    </div>
  </main>

  <script>
    //git commit test
    // Render the cart and bind checkout when the page is ready
    document.addEventListener('DOMContentLoaded', () => {
      // this function comes from assets/js/app.js
      if (typeof renderCart === 'function') renderCart();
      if (typeof updateCartCount === 'function') updateCartCount();

      document.getElementById('checkoutBtn').addEventListener('click', (e) => {
        e.preventDefault();
        if (typeof checkout === 'function') checkout();
      });
    });
  </script>
  <div id="notifPanel" class="offcanvas offcanvas-end text-bg-dark" tabindex="-1">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">Notifications</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body" id="notifList">
    <div class="text-muted">Loading...</div>
  </div>
</div>

</body>
</html>
