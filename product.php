<?php /* product.php — Product detail */ ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MedREX — Product</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/styles.css" rel="stylesheet">
  <!-- cache-bust to ensure latest JS is loaded -->
  <script defer src="assets/js/app.js?v=24"></script>
</head>
<body class="bg-dark text-light">

  <!-- Top nav -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-black sticky-top shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold" href="index.php">Med<span class="text-info">REX</span></a>

      <form class="d-flex ms-auto" role="search" action="index.php">
        <input class="form-control me-2" name="q" placeholder="Search equipment">
      </form>

      <div class="d-flex align-items-center ms-3 gap-2">
        <a href="shop.php" class="btn btn-outline-light btn-sm">Become a Seller</a>
        <div id="navAuth" class="d-flex align-items-center gap-2"></div>
        <a href="cart.php" class="btn btn-outline-light btn-sm position-relative">
          🛒
          <span id="cartCount"
            class="badge bg-info text-dark position-absolute top-0 start-100 translate-middle rounded-pill">0</span>
        </a>
      </div>
    </div>
  </nav>

  <main class="container py-4" id="page">
    <a href="index.php" class="btn btn-outline-secondary btn-sm mb-3">← Back to Products</a>

    <div id="detail" class="row g-4">
      <div class="col-12 text-muted">Loading...</div>
    </div>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', async () => {
      if (typeof renderNavAuth === 'function') renderNavAuth();
      if (typeof updateCartCount === 'function') updateCartCount();

      const params = new URLSearchParams(location.search);
      const id = Number(params.get('id') || 0);
      const wrap = document.getElementById('detail');

      if (!id) {
        wrap.innerHTML = `<div class="col-12 text-warning">Invalid product ID.</div>`;
        return;
      }

      try {
        const r = await fetch(`${API_BASE}/products/${id}`);
        const payload = await r.json();

        if (!r.ok) {
          wrap.innerHTML = `<div class="col-12 text-warning">${payload.error || 'Could not load product.'}</div>`;
          return;
        }

        const p = payload.data;

        // Build a safe image tag using app.js helper if present
        const img = (typeof proxied === 'function')
          ? `<img src="${proxied(p.image_url) || ('https://picsum.photos/seed/' + p.id + '/900/600')}"
                   onerror="this.onerror=null;this.src='https://picsum.photos/seed/${p.id}/900/600';"
                   referrerpolicy="no-referrer"
                   class="w-100 rounded-4 shadow-sm"
                   style="object-fit:cover;max-height:520px;" alt="">`
          : `<img src="${p.image_url || ('https://picsum.photos/seed/' + p.id + '/900/600')}"
                   onerror="this.onerror=null;this.src='https://picsum.photos/seed/${p.id}/900/600';"
                   class="w-100 rounded-4 shadow-sm"
                   style="object-fit:cover;max-height:520px;" alt="">`;

        wrap.innerHTML = `
          <div class="col-lg-6">${img}</div>
          <div class="col-lg-6">
            <h2 class="fw-semibold mb-2">${p.name}</h2>
            <div class="text-muted mb-3">${p.category ? p.category + ' • ' : ''}Sold by ${p.owner_name || 'Seller'}</div>

            <div class="display-6 fw-bold mb-3">$${Number(p.price).toFixed(2)}</div>

            <div class="mb-3">
              <span class="badge bg-secondary me-2">Stock: ${p.stock}</span>
              <span class="badge bg-dark border">${new Date(p.created_at).toLocaleDateString()}</span>
            </div>

            <p class="mb-4">${(p.description || 'No description provided.').replace(/\n/g, '<br>')}</p>

            <div class="d-flex align-items-center gap-2">
              <label for="qty" class="form-label m-0">Qty</label>
              <input id="qty" type="number" class="form-control" value="1" min="1" max="${Math.max(1, p.stock)}" style="width:100px">
              <button id="btnAdd" class="btn btn-info">Add to Cart</button>
            </div>

            <div id="msg" class="small text-warning mt-3"></div>
          </div>
        `;

        // Bind add-to-cart
        document.getElementById('btnAdd').addEventListener('click', () => {
          const qty = Math.max(1, Number(document.getElementById('qty').value || 1));
          if (typeof addToCart === 'function') {
            for (let i = 0; i < qty; i++) addToCart(p.id, 1);
            if (typeof updateCartCount === 'function') updateCartCount();
            document.getElementById('msg').textContent = 'Added to cart.';
          } else {
            document.getElementById('msg').textContent = 'Cart not ready.';
          }
        });

      } catch (e) {
        console.error(e);
        wrap.innerHTML = `<div class="col-12 text-warning">Error loading product (see console).</div>`;
      }
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
