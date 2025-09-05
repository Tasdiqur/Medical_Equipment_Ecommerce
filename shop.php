<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>MedREX — Seller</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/styles.css" rel="stylesheet">
  <script defer src="assets/js/app.js?v=32"></script>
  <script defer src="assets/js/seller.js?v=4"></script>
  <style> body, .card, label, h5, h4 { color:#fff !important; } </style>
</head>
<body class="bg-dark text-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-black sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">Med<span class="text-info">REX</span></a>
    <div class="ms-auto d-flex gap-2">
      <a href="index.php" class="btn btn-info btn-sm">Storefront</a>
      <button class="btn btn-outline-light btn-sm" onclick="logout()">Logout</button>
    </div>
  </div>
</nav>

<main class="container my-4">
  <h4 class="mb-3">Seller Console</h4>

  <ul class="nav nav-tabs" id="sellerTabs">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabProducts">Products</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabOrders">Orders</button></li>
  </ul>

  <div class="tab-content pt-3">
    <!-- Products tab (existing form + grid) -->
    <div class="tab-pane fade show active" id="tabProducts">
      <div class="row g-4">
        <div class="col-lg-5">
          <div class="card bg-black border border-secondary p-4">
            <h5>New / Edit Product</h5>
            <div class="mb-2">
              <label class="form-label">Name</label>
              <input id="pname" class="form-control" placeholder="e.g., Digital Stethoscope">
            </div>
            <div class="mb-2">
              <label class="form-label">Description</label>
              <textarea id="pdesc" class="form-control" rows="5" placeholder="Short description..."></textarea>
            </div>
            <div class="row g-2">
              <div class="col">
                <label class="form-label">Price</label>
                <input id="pprice" class="form-control" type="number" step="0.01" value="0.00">
              </div>
              <div class="col">
                <label class="form-label">Stock</label>
                <input id="pstock" class="form-control" type="number" value="0">
              </div>
            </div>
            <div class="row g-2 mt-2">
              <div class="col">
                <label class="form-label">Category</label>
                <input id="pcat" class="form-control" value="Diagnostic">
              </div>
              <div class="col">
                <label class="form-label">Image URL</label>
                <input id="pimg" class="form-control" placeholder="https://...">
              </div>
            </div>
            <div class="mt-3 d-flex gap-2">
              <button class="btn btn-outline-secondary" onclick="sellerClear()">Clear</button>
              <button class="btn btn-info" onclick="sellerSaveProduct()">Save</button>
            </div>
            <div id="sellerMsg" class="text-warning small mt-2"></div>
          </div>
        </div>

        <div class="col-lg-7">
          <div class="card bg-black border border-secondary p-3">
            <h5>My Products</h5>
            <div id="myProducts" class="row g-3"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Orders tab -->
    <div class="tab-pane fade" id="tabOrders">
      <div class="card bg-black border border-secondary p-3">
        <h5>Orders to Fulfil</h5>
        <div id="ordersBox" class="table-responsive">
          <div class="text-muted">Loading...</div>
        </div>
      </div>
    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
