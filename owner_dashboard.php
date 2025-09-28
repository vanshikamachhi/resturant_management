<?php require 'db.php'; ?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Owner Dashboard</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
  <h1>Owner: Orders & Calls</h1>
  <div id="orders"></div>
  <h2>Service Calls</h2>
  <div id="calls"></div>
</div>

<script>
async function fetchOrders(){
  const res = await fetch('api/owner_get_orders.php');
  const data = await res.json();
  const wrap = document.getElementById('orders');
  wrap.innerHTML = '';
  data.forEach(o=>{
    const div = document.createElement('div');
    div.style.border='1px solid #ddd'; div.style.padding='8px'; div.style.margin='8px 0';
    let itemsHtml = o.items.map(it=>`${it.name} x ${it.qty}`).join(', ');
    div.innerHTML = `Order #${o.id} (Table: ${o.table_id}) - ${o.status}<br/>Items: ${itemsHtml}
      <div style="margin-top:6px;">
        <button onclick="update(${o.id},'preparing')">Mark Preparing</button>
        <button onclick="update(${o.id},'served')">Mark Served</button>
        <button onclick="update(${o.id},'paid')">Mark Paid</button>
      </div>`;
    wrap.appendChild(div);
  });
}

async function fetchCalls(){
  const res = await fetch('api/owner_get_calls.php');
  const data = await res.json();
  const wrap = document.getElementById('calls');
  wrap.innerHTML = '';
  data.forEach(c=>{
    const div = document.createElement('div');
    div.style.border='1px solid #eee'; div.style.padding='6px'; div.style.margin='6px 0';
    div.innerHTML = `Table ${c.table_id}: ${c.note} - ${c.handled==0 ? 'NEW' : 'Handled'}
      <button onclick="handleCall(${c.id})">Handle</button>`;
    wrap.appendChild(div);
  });
}

async function update(id, status){
  await fetch('api/update_order.php', {
    method:'POST', headers:{'Content-Type':'application/json'},
    body: JSON.stringify({order_id:id, status})
  });
  fetchOrders();
}

async function handleCall(id){
  await fetch('api/handle_call.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({id})});
  fetchCalls();
}

setInterval(()=>{ fetchOrders(); fetchCalls(); }, 4000);
fetchOrders(); fetchCalls();
</script>
</body>
</html>
