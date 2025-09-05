/* Seller helper functions. Requires app.js (API_BASE, token(), user()) */

async function sellerLoadProducts() {
  // existing function in app.js may do this; if not, implement here
  if (typeof loadMyProducts === 'function') { loadMyProducts(); return; }
}

function sellerClear() {
  document.getElementById('pname').value = '';
  document.getElementById('pdesc').value = '';
  document.getElementById('pprice').value = '0.00';
  document.getElementById('pstock').value = '0';
  document.getElementById('pcat').value = 'Diagnostic';
  document.getElementById('pimg').value = '';
}

async function sellerSaveProduct() {
  const msg = document.getElementById('sellerMsg');
  const body = {
    name:        document.getElementById('pname').value.trim(),
    description: document.getElementById('pdesc').value.trim(),
    price:       parseFloat(document.getElementById('pprice').value),
    stock:       parseInt(document.getElementById('pstock').value,10),
    category:    document.getElementById('pcat').value.trim(),
    image_url:   document.getElementById('pimg').value.trim()
  };
  if (!body.name || !body.description || !body.image_url || !body.price || body.price<=0) {
    msg.textContent = 'Please fill name, description, image and a valid price.'; return;
  }
  try {
    const r = await fetch(`${API_BASE}/products`, {
      method:'POST',
      headers:{'Content-Type':'application/json', 'X-Auth-Token': token()},
      body: JSON.stringify(body)
    });
    const j = await r.json();
    if (!r.ok) { msg.textContent = j.error || 'Save failed'; return; }
    msg.textContent = 'Saved';
    sellerClear();
    if (typeof loadMyProducts === 'function') loadMyProducts();
  } catch(e){ msg.textContent = 'Network error'; }
}

/* --------- Orders tab --------- */

async function loadSellerOrders() {
  const box = document.getElementById('ordersBox');
  box.innerHTML = '<div class="text-muted">Loading...</div>';
  try {
    const r = await fetch(`${API_BASE}/seller/orders`, {
      headers: {'X-Auth-Token': token()}
    });
    const j = await r.json();
    if (!r.ok) { box.innerHTML = `<div class="text-warning">${j.error||'Error'}</div>`; return; }
    const rows = j.data || [];
    if (!rows.length) { box.innerHTML = '<div class="text-muted">No items to fulfil.</div>'; return; }

    let html = `<table class="table table-dark table-sm align-middle">
      <thead><tr>
        <th>Order #</th><th>Product</th><th class="text-center">Qty</th>
        <th class="text-end">Price</th><th>Status</th><th>Action</th>
      </tr></thead><tbody>`;

    for (const it of rows) {
      const canPack = it.status==='pending';
      const canDispatch = it.status==='packed';
      const btn = canPack
        ? `<button class="btn btn-sm btn-outline-info" onclick="markItemStatus(${it.item_id},'packed')">Mark Packed</button>`
        : (canDispatch
          ? `<button class="btn btn-sm btn-info" onclick="markItemStatus(${it.item_id},'dispatched')">Mark Dispatched</button>`
          : `<span class="text-muted">—</span>`);

      html += `<tr>
        <td>#${it.order_id}</td>
        <td>${it.product_name}</td>
        <td class="text-center">${it.quantity}</td>
        <td class="text-end">৳${Number(it.price).toFixed(2)}</td>
        <td><span class="badge bg-secondary">${it.status}</span></td>
        <td>${btn}</td>
      </tr>`;
    }
    html += '</tbody></table>';
    box.innerHTML = html;
  } catch(e) {
    box.innerHTML = '<div class="text-warning">Network error</div>';
  }
}

async function markItemStatus(itemId, status) {
  const box = document.getElementById('ordersBox');
  try {
    const r = await fetch(`${API_BASE}/seller/order-items/${itemId}/status`, {
      method:'POST',
      headers:{'Content-Type':'application/json', 'X-Auth-Token': token()},
      body: JSON.stringify({status})
    });
    const j = await r.json();
    if (!r.ok) { alert(j.error || 'Failed'); return; }
    await loadSellerOrders();
  } catch(e){ alert('Network error'); }
}

document.addEventListener('DOMContentLoaded', () => {
  // default tab renders products; when orders tab is clicked, load orders
  const tabs = document.getElementById('sellerTabs');
  if (tabs) {
    tabs.addEventListener('click', (ev) => {
      const tgt = ev.target;
      if (tgt.getAttribute('data-bs-target') === '#tabOrders') {
        loadSellerOrders();
      }
    });
  }
  // initial load of products grid if app.js has it
  sellerLoadProducts();
});
