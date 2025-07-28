/* ==========================================================
   MedREX — Frontend app.js (FULL)
   ========================================================== */

// ---- API base (works without Apache rewrites) ----
const API_BASE = 'api/index.php?r=';

// --------------------------------------------------
// Helpers: auth state in localStorage
// --------------------------------------------------
function token() { return localStorage.getItem('token'); }
function user()  { try { return JSON.parse(localStorage.getItem('user')); } catch { return null; } }
function setAuth(t, u) { localStorage.setItem('token', t); localStorage.setItem('user', JSON.stringify(u)); }
function clearAuth() { localStorage.removeItem('token'); localStorage.removeItem('user'); }
function ensureRole(role) { const u = user(); if (!u || u.role !== role) window.location = 'login.php'; }

// --------------------------------------------------
// Navbar auth display
// --------------------------------------------------
function renderNavAuth() {
  const u = user();
  const wrap = document.getElementById('navAuth');
  if (!wrap) return;

  if (u) {
    wrap.innerHTML = `
      <span class="me-2">👤 ${u.full_name}</span>
      <button class="btn btn-sm btn-outline-light" onclick="logout()">Logout</button>
    `;
  } else {
    wrap.innerHTML = `
      <a class="btn btn-outline-light btn-sm" href="login.php">Login</a>
      <a class="btn btn-info btn-sm" href="register.php">Sign Up</a>
    `;
  }
}

// --------------------------------------------------
// Auth actions
// --------------------------------------------------
async function doLogin() {
  try {
    const email = document.getElementById('loginEmail').value.trim();
    const password = document.getElementById('loginPassword').value.trim();
    const msg = document.getElementById('loginMsg');
    if (msg) msg.textContent = 'Signing in...';

    const r = await fetch(`${API_BASE}/auth/login`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email, password })
    });
    const data = await r.json();

    if (r.ok) {
      setAuth(data.token, data.user);
      if (msg) msg.textContent = 'Success! Redirecting...';
      setTimeout(() => {
        window.location = data.user.role === 'shopowner' ? 'shop.php' : 'index.php';
      }, 500);
    } else {
      if (msg) msg.textContent = data.error || 'Invalid credentials';
    }
  } catch (err) {
    console.error(err);
    const msg = document.getElementById('loginMsg');
    if (msg) msg.textContent = 'Unexpected error (see console).';
  }
}

async function doRegister() {
  try {
    const full_name = document.getElementById('regName').value.trim();
    const email = document.getElementById('regEmail').value.trim();
    const password = document.getElementById('regPassword').value.trim();
    const role = document.getElementById('regRole').value;
    const msg = document.getElementById('regMsg');
    if (msg) msg.textContent = 'Creating account...';

    const r = await fetch(`${API_BASE}/auth/register`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ full_name, email, password, role })
    });
    const data = await r.json();

    if (r.ok) {
      setAuth(data.token, data.user);
      if (msg) msg.textContent = 'Registered! Redirecting...';
      setTimeout(() => (window.location = 'index.php'), 600);
    } else {
      if (msg) msg.textContent = data.error || 'Registration failed';
    }
  } catch (err) {
    console.error(err);
    const msg = document.getElementById('regMsg');
    if (msg) msg.textContent = 'Unexpected error (see console).';
  }
}

async function logout() {
  try { await fetch(`${API_BASE}/auth/logout`, { method: 'POST', headers: { 'X-Auth-Token': token() } }); } catch {}
  clearAuth();
  window.location = 'index.php';
}

// --------------------------------------------------
// Safe image helpers (avoid hotlink/CORS + graceful fallback)
// --------------------------------------------------

/**
 * Wrap external URLs to avoid hotlink blocks. Strategy:
 *  - Unsplash: use direct URL with no-referrer (proxy often blocked).
 *  - Others: use images.weserv.nl proxy (server-side fetch).
 *  - If anything fails, <img onerror> falls back to picsum.
 */
function proxied(url) {
  if (!url) return '';
  try {
    const u = new URL(url);
    const host = u.hostname.toLowerCase();

    // Unsplash can block some proxies; try direct first.
    if (host.includes('unsplash.com')) {
      return url; // will be used with referrerpolicy="no-referrer"
    }

    // Generic proxy (reliable for many hosts)
    if (u.protocol === 'http:' || u.protocol === 'https:') {
      // strip protocol for weserv
      const path = u.hostname + u.pathname + (u.search || '');
      return `https://images.weserv.nl/?url=${encodeURIComponent(path)}&w=600&h=450&fit=cover`;
    }
    return url;
  } catch {
    return url;
  }
}

/** Build an IMG tag with fallback and no-referrer. */
function imgTag(p, w = 600, h = 450) {
  const fallback = `https://picsum.photos/seed/${p.id}/${w}/${h}`;
  const src = proxied(p.image_url) || fallback;
  return `
    <img
      src="${src}"
      alt=""
      width="100%"
      height="${Math.round(h * 0.36)}"
      style="width:100%;height:160px;object-fit:cover;display:block;border-top-left-radius:1rem;border-top-right-radius:1rem"
      loading="lazy"
      decoding="async"
      referrerpolicy="no-referrer"
      onerror="this.onerror=null;this.src='${fallback}'"
    >
  `;
}

// --------------------------------------------------
// Products grid (index.php)
// --------------------------------------------------
let offset = 0;
const pageSize = 24; // show more per page

async function loadProducts(applySearch = false) {
  const grid = document.getElementById('productGrid');
  if (!grid) return;

  const q = (document.getElementById('searchBox')?.value || '').trim().toLowerCase();

  try {
    const url = `${API_BASE}/products?limit=${pageSize}&offset=${offset}`;
    const r = await fetch(url);
    const payload = await r.json();

    if (!r.ok) {
      grid.innerHTML = `<div class="text-warning small">Failed to load products (${r.status}).</div>`;
      return;
    }

    const list = (payload.data || []).filter(p => !q || (p.name || '').toLowerCase().includes(q));

    if (list.length === 0 && offset === 0) {
      grid.innerHTML = `<div class="text-muted">No products found.</div>`;
    }

    for (const p of list) {
      const col = document.createElement('div');
      col.className = 'col-6 col-md-4 col-lg-3';
      col.innerHTML = `
        <div class="card product-card bg-black h-100 border-secondary position-relative">
          <a href="product.php?id=${p.id}" class="stretched-link" aria-label="${p.name}"></a>
          ${imgTag(p)}
          <div class="p-3 d-flex flex-column">
            <div class="fw-semibold">${p.name}</div>
            <small class="text-muted">${p.category || ''}</small>
            <div class="mt-auto d-flex justify-content-between align-items-end">
              <span class="fw-bold">$${Number(p.price).toFixed(2)}</span>
              <div class="d-flex gap-2">
                <a href="product.php?id=${p.id}" class="btn btn-outline-secondary btn-sm">View</a>
                <button class="btn btn-outline-info btn-sm" data-add="${p.id}">Add</button>
              </div>
            </div>
          </div>
        </div>`;
      grid.appendChild(col);
    }

    // Bind Add buttons once
    grid.querySelectorAll('[data-add]').forEach(btn => {
      if (!btn.dataset.bound) {
        btn.dataset.bound = '1';
        btn.addEventListener('click', (e) => {
          e.preventDefault(); e.stopPropagation();
          addToCart(Number(btn.getAttribute('data-add')), 1);
        });
      }
    });

    offset += pageSize;
  } catch (e) {
    console.error('loadProducts error:', e);
    grid.innerHTML = `<div class="text-warning small">Error loading products (see console).</div>`;
  }
}

// Re-init on DOM load for pages that have these elements
document.addEventListener('DOMContentLoaded', () => {
  renderNavAuth();
  updateCartCount();

  if (document.getElementById('productGrid')) {
    offset = 0;
    document.getElementById('productGrid').innerHTML = '';
    loadProducts();
  }
});

document.getElementById('loadMoreBtn')?.addEventListener('click', () => loadProducts());
document.getElementById('searchBox')?.addEventListener('input', () => {
  const grid = document.getElementById('productGrid');
  if (grid) grid.innerHTML = '';
  offset = 0;
  loadProducts(true);
});

// --------------------------------------------------
// Cart
// --------------------------------------------------
function addToCart(id, qty = 1) {
  const cart = JSON.parse(localStorage.getItem('cart') || '[]');
  const idx = cart.findIndex((i) => i.product_id === id);
  if (idx >= 0) cart[idx].quantity += qty;
  else cart.push({ product_id: id, quantity: qty });
  localStorage.setItem('cart', JSON.stringify(cart));
  updateCartCount();
}

function updateCartCount() {
  const cart = JSON.parse(localStorage.getItem('cart') || '[]');
  const el = document.getElementById('cartCount');
  if (el) el.innerText = cart.reduce((a, b) => a + b.quantity, 0);
}

async function renderCart() {
  const wrap = document.getElementById('cartList'); if (!wrap) return;
  const cart = JSON.parse(localStorage.getItem('cart') || '[]');
  if (cart.length === 0) { wrap.innerHTML = '<p>Your cart is empty.</p>'; return; }

  const r = await fetch(`${API_BASE}/products?limit=200&offset=0`);
  const all = (await r.json()).data || [];
  let total = 0;
  wrap.innerHTML = '';
  for (const item of cart) {
    const p = all.find(pp => pp.id === item.product_id);
    if (!p) continue;
    const line = Number(p.price) * item.quantity;
    total += line;
    const col = document.createElement('div'); col.className = 'col-12';
    col.innerHTML = `
      <div class="d-flex align-items-center border rounded-4 p-2">
        <img src="${proxied(p.image_url) || ('https://picsum.photos/seed/' + p.id + '/120/80')}"
             onerror="this.onerror=null;this.src='https://picsum.photos/seed/${p.id}/120/80';"
             referrerpolicy="no-referrer"
             class="rounded me-3" width="120" height="80">
        <div class="flex-grow-1"><div class="fw-semibold">${p.name}</div><small>${item.quantity} × $${Number(p.price).toFixed(2)}</small></div>
        <div class="fw-bold">$${line.toFixed(2)}</div>
      </div>`;
    wrap.appendChild(col);
  }
  const totalDiv = document.createElement('div'); totalDiv.className = 'col-12 mt-2';
  totalDiv.innerHTML = `<div class="d-flex justify-content-end"><div class="fs-5 fw-bold">Total: $${total.toFixed(2)}</div></div>`;
  wrap.appendChild(totalDiv);
}

async function checkout() {
  const u = user();
  const msg = document.getElementById('cartMsg');
  if (!u || u.role !== 'customer') { if (msg) msg.textContent = 'Please log in as a customer to checkout.'; return; }
  const items = JSON.parse(localStorage.getItem('cart') || '[]');
  if (items.length === 0) { if (msg) msg.textContent = 'Cart is empty.'; return; }
  const r = await fetch(`${API_BASE}/orders`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-Auth-Token': token() },
    body: JSON.stringify({ items })
  });
  const data = await r.json();
  if (r.ok) {
    localStorage.removeItem('cart'); updateCartCount();
    if (msg) msg.textContent = 'Order placed! #' + data.data.id;
    renderCart();
  } else {
    if (msg) msg.textContent = data.error || 'Checkout failed';
  }
}

// --------------------------------------------------
// Seller dashboard (shop.php)
// --------------------------------------------------
function clearForm() {
  ['prodId','prodName','prodDesc','prodPrice','prodStock','prodCategory','prodImage']
    .forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
}

async function loadMyProducts() {
  const wrap = document.getElementById('myProducts');
  if (!wrap) return;

  const r = await fetch(`${API_BASE}/products/mine`, { headers: { 'X-Auth-Token': token() } });
  const data = await r.json();
  wrap.innerHTML = '';
  if (!r.ok) {
    const msg = document.getElementById('sellerMsg');
    if (msg) msg.textContent = data.error || 'Failed to load';
    return;
  }

  for (const p of data.data || []) {
    const col = document.createElement('div'); col.className = 'col-12';
    col.innerHTML = `
      <div class="d-flex align-items-center border rounded-4 p-2">
        <img src="${proxied(p.image_url) || ('https://picsum.photos/seed/' + p.id + '/120/80')}"
             onerror="this.onerror=null;this.src='https://picsum.photos/seed/${p.id}/120/80';"
             referrerpolicy="no-referrer"
             class="rounded me-3" width="120" height="80">
        <div class="flex-grow-1">
          <div class="fw-semibold">${p.name}</div>
          <small class="text-muted">Stock: ${p.stock} • ${p.category || ''} • $${Number(p.price).toFixed(2)}</small>
        </div>
        <div class="ms-2">
          <button class="btn btn-sm btn-outline-info me-2" data-edit='${p.id}'>Edit</button>
          <button class="btn btn-sm btn-outline-danger" data-del='${p.id}'>Delete</button>
        </div>
      </div>`;
    wrap.appendChild(col);
  }

  // Bind edit/delete
  wrap.querySelectorAll('[data-edit]').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = Number(btn.getAttribute('data-edit'));
      const item = (data.data || []).find(x => x.id === id);
      if (!item) return;
      document.getElementById('prodId').value = item.id;
      document.getElementById('prodName').value = item.name || '';
      document.getElementById('prodDesc').value = item.description || '';
      document.getElementById('prodPrice').value = item.price ?? 0;
      document.getElementById('prodStock').value = item.stock ?? 0;
      document.getElementById('prodCategory').value = item.category || '';
      document.getElementById('prodImage').value = item.image_url || '';
      const msg = document.getElementById('sellerMsg');
      if (msg) msg.textContent = 'Editing product #' + item.id;
    });
  });

  wrap.querySelectorAll('[data-del]').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = Number(btn.getAttribute('data-del'));
      if (!confirm('Delete this product?')) return;
      const r = await fetch(`${API_BASE}/products/${id}`, { method: 'DELETE', headers: { 'X-Auth-Token': token() } });
      const data2 = await r.json();
      const msg = document.getElementById('sellerMsg');
      if (msg) msg.textContent = r.ok ? 'Deleted.' : (data2.error || 'Failed');
      if (r.ok) loadMyProducts();
    });
  });
}

async function saveProduct() {
  const id = document.getElementById('prodId').value;
  const body = {
    name: document.getElementById('prodName').value,
    description: document.getElementById('prodDesc').value,
    price: Number(document.getElementById('prodPrice').value || 0),
    stock: Number(document.getElementById('prodStock').value || 0),
    category: document.getElementById('prodCategory').value,
    image_url: document.getElementById('prodImage').value
  };
  const url = id ? `${API_BASE}/products/${id}` : `${API_BASE}/products`;
  const method = id ? 'PUT' : 'POST';
  const r = await fetch(url, {
    method,
    headers: { 'Content-Type': 'application/json', 'X-Auth-Token': token() },
    body: JSON.stringify(body)
  });
  const data = await r.json();
  const msg = document.getElementById('sellerMsg');
  if (msg) msg.textContent = r.ok ? 'Saved!' : (data.error || 'Failed');
  if (r.ok) { clearForm(); loadMyProducts(); }
}

// --------------------------------------------------
// Expose globals for inline handlers/pages
// --------------------------------------------------
window.doLogin = doLogin;
window.doRegister = doRegister;
window.logout = logout;
window.renderNavAuth = renderNavAuth;

window.loadProducts = loadProducts;
window.addToCart = addToCart;
window.updateCartCount = updateCartCount;
window.renderCart = renderCart;
window.checkout = checkout;

window.ensureRole = ensureRole;
window.clearForm = clearForm;
window.loadMyProducts = loadMyProducts;
window.saveProduct = saveProduct;
