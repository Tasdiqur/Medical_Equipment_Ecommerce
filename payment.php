<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Payment - MedRex</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/styles.css" rel="stylesheet">
  <!-- app.js defines API_BASE, token(), user(), updateCartCount(), renderNavAuth() -->
  <script defer src="assets/js/app.js?v=31"></script>
  <style>
    body, .card, p, h5, h2, .text-muted { color:#fff !important; }
  </style>
</head>
<body class="bg-dark text-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-black sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">Med<span class="text-info">REX</span></a>
    <div class="ms-auto d-flex align-items-center gap-2">
      <div id="navAuth"></div>
      <a href="cart.php" class="btn btn-outline-light btn-sm position-relative">
        🛒 <span id="cartCount" class="badge bg-info text-dark position-absolute top-0 start-100 translate-middle rounded-pill">0</span>
      </a>
    </div>
  </div>
</nav>

<main class="container my-5">
  <h2 class="mb-4">Payment Options</h2>

  <div class="card bg-black border border-secondary p-4">
    <h5 class="mb-3">Simulated Payment</h5>

    <div id="items" class="mb-3 small"></div>

    <p id="totalAmount" class="mb-3">Total Amount: <strong>৳0.00</strong></p>
    <p class="text-muted">(For the demo, clicking confirm will create a paid order via the API.)</p>

    <div class="d-flex gap-2">
      <button class="btn btn-outline-secondary" onclick="window.location='cart.php'">Back to Cart</button>
      <button id="payBtn" class="btn btn-info">Confirm Payment</button>
    </div>
    <div id="payMsg" class="small text-warning mt-3"></div>
  </div>
</main>

<script>
/* -----------------------------
   Robust cart reading helpers
------------------------------*/
function getParam(name) {
  const m = new URLSearchParams(window.location.search).get(name);
  return m === null ? null : m;
}

// Try to read the cart in a tolerant way
function readCartFlexible() {
  // try standard key
  let raw = localStorage.getItem('cart');
  if (!raw) {
    // try a few alternate keys if your older code used them
    raw = localStorage.getItem('cartItems') || localStorage.getItem('shopping_cart') || '[]';
  }
  let arr;
  try { arr = JSON.parse(raw || '[]'); } catch { arr = []; }
  if (!Array.isArray(arr)) arr = [];

  // Normalize every item: {pid, qty, price?}
  const norm = arr.map(it => {
    const pid = it.product_id ?? it.id ?? it.productId ?? it.pid;
    const qty = it.quantity ?? it.qty ?? 1;
    const price = (it.price !== undefined) ? Number(it.price) : undefined;
    return { pid: Number(pid), qty: Number(qty), price };
  }).filter(x => x.pid && x.qty > 0);

  return norm;
}

async function fetchProductsMap() {
  // fetch once and map by id
  const r = await fetch(`${API_BASE}/products?limit=1000&offset=0`);
  const j = await r.json();
  const data = j && j.data ? j.data : [];
  const map = new Map();
  for (const p of data) map.set(Number(p.id), p);
  return map;
}

/* -----------------------------
   Render summary and compute total
------------------------------*/
let CART = [];
let TOTAL = 0;

async function loadSummary() {
  // navbar & cart badge
  if (typeof renderNavAuth === 'function') renderNavAuth();
  if (typeof updateCartCount === 'function') updateCartCount();

  const itemsBox = document.getElementById('items');
  const totalBox = document.getElementById('totalAmount');
  const btn = document.getElementById('payBtn');

  CART = readCartFlexible();

  // If cart is empty, but we got ?total= in URL, show that (fallback)
  if (CART.length === 0) {
    const qTotal = Number(getParam('total') || 0);
    if (qTotal > 0) {
      TOTAL = qTotal;
      itemsBox.innerHTML = '<div class="text-warning">No cart found in browser; using total from URL.</div>';
      totalBox.innerHTML = `Total Amount: <strong>৳${TOTAL.toFixed(2)}</strong>`;
      return;
    }
    itemsBox.innerHTML = '<div class="text-warning">Your cart is empty.</div>';
    totalBox.innerHTML  = 'Total Amount: <strong>৳0.00</strong>';
    btn.disabled = true;
    return;
  }

  // If items already carry price, compute directly; otherwise query API
  const needLookup = CART.some(it => typeof it.price !== 'number' || isNaN(it.price));
  let productsMap = null;
  if (needLookup) {
    try {
      productsMap = await fetchProductsMap();
    } catch (e) {
      console.warn('Failed to fetch products for pricing, falling back to 0 totals.', e);
    }
  }

  // Build rows and sum
  let html = '<div class="table-responsive"><table class="table table-sm table-dark align-middle mb-0">';
  html += '<thead><tr><th>Product</th><th class="text-center">Qty</th><th class="text-end">Price</th><th class="text-end">Subtotal</th></tr></thead><tbody>';
  TOTAL = 0;

  for (const it of CART) {
    let price = it.price;
    let name = 'Item';
    if ((price === undefined || isNaN(price)) && productsMap) {
      const p = productsMap.get(Number(it.pid));
      if (p) {
        name = p.name || name;
        price = Number(p.price) || 0;
      }
    }
    if (name === 'Item' && productsMap) {
      const p = productsMap.get(Number(it.pid));
      if (p) name = p.name || name;
    }
    if (price === undefined || isNaN(price)) price = 0;
    const line = price * (Number(it.qty) || 0);
    TOTAL += line;

    html += `<tr>
      <td>${name}</td>
      <td class="text-center">${Number(it.qty) || 0}</td>
      <td class="text-end">৳${price.toFixed(2)}</td>
      <td class="text-end">৳${line.toFixed(2)}</td>
    </tr>`;
  }

  html += `</tbody><tfoot><tr><th colspan="3" class="text-end">Total</th><th class="text-end">৳${TOTAL.toFixed(2)}</th></tr></tfoot></table></div>`;
  itemsBox.innerHTML = html;
  totalBox.innerHTML = `Total Amount: <strong>৳${TOTAL.toFixed(2)}</strong>`;
}

document.addEventListener('DOMContentLoaded', loadSummary);

/* -----------------------------
   Confirm "payment" -> create order via API
------------------------------*/
document.getElementById('payBtn').addEventListener('click', async () => {
  const msg = document.getElementById('payMsg');
  msg.textContent = 'Creating order...';

  // must be customer
  const u = (typeof user === 'function') ? user() : null;
  if (!u || u.role !== 'customer') {
    msg.textContent = 'Please log in as a customer to place an order.';
    return;
  }
  if (!CART || CART.length === 0) {
    msg.textContent = 'Your cart is empty.';
    return;
  }

  // Normalize for API payload
  const items = CART.map(it => ({ product_id: it.pid, quantity: it.qty }));

  try {
    const r = await fetch(`${API_BASE}/orders`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-Auth-Token': token() },
      body: JSON.stringify({ items })
    });
    const data = await r.json();

    if (!r.ok) {
      console.error('Order error:', data);
      msg.textContent = data.error || 'Order creation failed.';
      return;
    }

    // clear cart and redirect
    localStorage.removeItem('cart');
    if (typeof updateCartCount === 'function') updateCartCount();

    const orderId = (data.data && data.data.id) ? data.data.id : 0;
    window.location = `payment_success.php?order_id=${orderId}&total=${encodeURIComponent(TOTAL.toFixed(2))}`;
  } catch (e) {
    console.error(e);
    msg.textContent = 'Network error while creating order.';
  }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
