<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MedRex</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/styles.css" rel="stylesheet">
  <!-- cache-bust to ensure latest JS loads -->
  <script defer src="assets/js/app.js?v=22"></script>
</head>
<body class="bg-dark text-light">

  <!-- Top nav -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-black sticky-top shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold" href="index.php">Med<span class="text-info">REX</span></a>

      <form class="d-flex ms-auto" role="search" onsubmit="event.preventDefault();">
        <input class="form-control me-2" id="searchBox" type="search" placeholder="Search equipment">
      </form>

      <div class="d-flex align-items-center ms-3 gap-2">
        <a href="shop.php" class="btn btn-outline-light btn-sm">Become a Seller</a>

        

        <a href="cart.php" class="btn btn-outline-light btn-sm position-relative">
          🛒
          <span id="cartCount"
                class="badge bg-info text-dark position-absolute top-0 start-100 translate-middle rounded-pill">0</span>
        </a>
        <!-- Dynamic auth area (populated by app.js → renderNavAuth) -->
        <div id="navAuth" class="d-flex align-items-center gap-2">
          <!-- If logged out: Login + Sign Up buttons appear -->
          <!-- If logged in: "👤 Full Name" + Logout button appear -->
        </div>
        <a href="#" class="btn btn-outline-light btn-sm position-relative" onclick="openNotif()">
  🔔    <span id="notifCount" class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill">0</span>
        </a>

      </div>
    </div>
  </nav>

  <!-- Hero -->
  <header class="hero container my-4">
    <div class="p-5 rounded-4 hero-banner text-center">
      <h1 class="display-5 fw-semibold">Medical equipment for clinics & labs</h1>
      <p class="lead opacity-75">Diagnostic • Biomedical • Durable • Defibrillators</p>
    </div>
  </header>

  <!-- Products -->
  <main class="container pb-5">
    <h5 class="mb-3">Products</h5>
    <div id="productGrid" class="row g-3"></div>
    <div class="text-center mt-4">
      <button class="btn btn-outline-info" id="loadMoreBtn">Load More</button>
    </div>
  </main>

  <!-- Footer -->
  <footer class="py-4 bg-black text-center text-muted small">
    © <span id="year"></span> MedREX
  </footer>

  <script>
    document.getElementById('year').textContent = new Date().getFullYear();
    // If your JS wasn't updated yet, this will prevent a blank nav
    document.addEventListener('DOMContentLoaded', () => {
      if (typeof renderNavAuth === 'function') renderNavAuth();
      if (typeof updateCartCount === 'function') updateCartCount();
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  
<script defer src="assets/js/app.js"></script>
<script defer src="assets/js/notify.js"></script>
</body>
</html>

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
