<?php /* shop.php — Seller dashboard */ ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MedREX — Seller</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/styles.css" rel="stylesheet">
  <!-- cache-bust to ensure latest JS is loaded -->
  <script defer src="assets/js/app.js?v=23"></script>
</head>
<body class="bg-dark text-light">

  <!-- Top Bar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-black sticky-top shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold" href="index.php">Med<span class="text-info">REX</span> — Seller</a>

      <div class="ms-auto d-flex align-items-center gap-2">
        <!-- Dynamic auth area -->
        <div id="navAuth" class="d-flex align-items-center gap-2"></div>
        <a href="index.php" class="btn btn-info btn-sm">Storefront</a>
      </div>
    </div>
  </nav>

  <main class="container py-4">

    <div class="row g-4">
      <!-- Left: Product form -->
      <div class="col-lg-5">
        <div class="card bg-black p-3">
          <h5 class="mb-3">New / Edit Product</h5>

          <!-- hidden id when editing -->
          <input type="hidden" id="prodId">

          <div class="mb-2">
            <label class="form-label">Name</label>
            <input id="prodName" class="form-control" placeholder="e.g., Digital Stethoscope">
          </div>

          <div class="mb-2">
            <label class="form-label">Description</label>
            <textarea id="prodDesc" class="form-control" rows="5" placeholder="Short description..."></textarea>
          </div>

          <div class="row g-2">
            <div class="col-6">
              <label class="form-label">Price</label>
              <input id="prodPrice" type="number" step="0.01" min="0" class="form-control" placeholder="0.00">
            </div>
            <div class="col-6">
              <label class="form-label">Stock</label>
              <input id="prodStock" type="number" min="0" class="form-control" placeholder="0">
            </div>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-6">
              <label class="form-label">Category</label>
              <input id="prodCategory" class="form-control" placeholder="Diagnostic">
            </div>
            <div class="col-6">
              <label class="form-label">Image URL</label>
              <input id="prodImage" class="form-control" placeholder="https://...">
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mt-3">
            <div id="sellerMsg" class="small text-warning"></div>
            <div class="d-flex gap-2">
              <button class="btn btn-outline-secondary" type="button" onclick="clearForm()">Clear</button>
              <button class="btn btn-info" type="button" onclick="saveProduct()">Save</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Right: My products -->
      <div class="col-lg-7">
        <div class="card bg-black p-3">
          <h5 class="mb-3">My Products</h5>
          <div id="myProducts" class="row g-2">
            <!-- loadMyProducts() fills this -->
          </div>
        </div>
      </div>
    </div>
  </main>

  <script>
    // Require shopowner role, render navbar, and load seller products
    document.addEventListener('DOMContentLoaded', () => {
      if (typeof ensureRole === 'function') ensureRole('shopowner');
      if (typeof renderNavAuth === 'function') renderNavAuth();
      if (typeof updateCartCount === 'function') updateCartCount();

      // very important: populate the right panel
      if (typeof loadMyProducts === 'function') loadMyProducts();
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
