async function loadNotif(unreadOnly=true) {
  const r = await fetch(`${API_BASE}/notifications?unread=${unreadOnly?1:0}`, {
    headers:{'X-Auth-Token': token()}
  });
  const j = await r.json();
  const list = document.getElementById('notifList');
  const count = document.getElementById('notifCount');
  if (!r.ok) { list.innerHTML = `<div class="text-warning">${j.error||'Error'}</div>`; return; }
  const rows = j.data || [];
  count.textContent = rows.filter(n=>!n.is_read).length;
  if (!rows.length) { list.innerHTML = '<div class="text-muted">No notifications</div>'; return; }
  let html = '<div class="list-group list-group-flush">';
  for (const n of rows) {
    html += `<div class="list-group-item bg-transparent text-white">
      <div class="d-flex justify-content-between">
        <strong>${n.title}</strong>
        ${n.is_read?'<span class="badge bg-secondary">read</span>':'<span class="badge bg-danger">new</span>'}
      </div>
      <div class="small">${n.message}</div>
      <div class="small text-muted">${n.created_at}</div>
      ${!n.is_read?`<button class="btn btn-sm btn-outline-light mt-2" onclick="markRead(${n.id})">Mark read</button>`:''}
    </div>`;
  }
  html += '</div>';
  list.innerHTML = html;
}

async function markRead(id) {
  await fetch(`${API_BASE}/notifications/${id}/read`, {
    method:'PATCH',
    headers:{'X-Auth-Token': token()}
  });
  loadNotif();
}

function openNotif() {
  loadNotif();
  const el = document.getElementById('notifPanel');
  const off = new bootstrap.Offcanvas(el);
  off.show();
}
