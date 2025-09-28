// simple front-end script using fetch
let menu = [];
let cart = {};
let selectedTable = null;

async function init() {
  await loadTables();
  await loadMenu();
  document.getElementById('placeOrderBtn').addEventListener('click', placeOrder);
  document.getElementById('payOnlineBtn').addEventListener('click', payNow);
  document.getElementById('callServiceBtn').addEventListener('click', callService);
}

async function loadTables(){
  const res = await fetch('api/get_tables.php');
  const data = await res.json();
  const sel = document.getElementById('tableSelect');
  data.forEach(t=>{
    const opt = document.createElement('option');
    opt.value = t.id; opt.textContent = t.name;
    sel.appendChild(opt);
  });
  sel.addEventListener('change', ()=> { selectedTable = sel.value; });
  if(data[0]) { sel.value = data[0].id; selectedTable = data[0].id; }
}

async function loadMenu(){
  const res = await fetch('api/get_menu.php');
  menu = await res.json();
  const wrap = document.getElementById('menuList');
  wrap.innerHTML = '';
  menu.forEach(m=>{
    const div = document.createElement('div'); div.className='menuItem';
    div.innerHTML = `<h3>${m.name}</h3><p>${m.description||''}</p><div>₹${m.price}</div>
      <div style="margin-top:8px;"><button class="small" onclick="addToCart(${m.id})">Add</button></div>`;
    wrap.appendChild(div);
  });
}

function addToCart(id){
  const item = menu.find(m=>m.id==id);
  if(!cart[id]) cart[id] = { ...item, qty:0 };
  cart[id].qty++;
  renderCart();
}

function renderCart(){
  const el = document.getElementById('cartList');
  if(Object.keys(cart).length===0) { el.textContent='No items'; document.getElementById('cartTotal').textContent='0.00'; return; }
  el.innerHTML = '';
  let total=0;
  Object.values(cart).forEach(ci=>{
    total += ci.qty * parseFloat(ci.price);
    const div = document.createElement('div');
    div.innerHTML = `${ci.name} x ${ci.qty} = ₹${(ci.qty*ci.price).toFixed(2)} <button class="small" onclick="dec(${ci.id})">-</button> <button class="small" onclick="inc(${ci.id})">+</button>`;
    el.appendChild(div);
  });
  document.getElementById('cartTotal').textContent = total.toFixed(2);
}

function inc(id){ cart[id].qty++; renderCart(); }
function dec(id){ cart[id].qty--; if(cart[id].qty<=0) delete cart[id]; renderCart(); }

async function placeOrder(){
  if(!selectedTable) return showMsg('Select a table first', true);
  if(Object.keys(cart).length===0) return showMsg('Cart empty', true);
  const items = Object.values(cart).map(c=>({menu_id:c.id, qty:c.qty, price:c.price}));
  const res = await fetch('api/place_order.php', {
    method:'POST', headers:{'Content-Type':'application/json'},
    body: JSON.stringify({table_id:selectedTable, items})
  });
  const data = await res.json();
  if(data.success){ showMsg('Order placed (id:'+data.order_id+')'); cart = {}; renderCart(); } else showMsg('Failed to place order', true);
}

async function payNow(){
  // This is a mock payment flow — in production integrate Razorpay/Stripe
  if(Object.keys(cart).length===0) return showMsg('Cart empty', true);
  const total = parseFloat(document.getElementById('cartTotal').textContent);
  const res = await fetch('api/create_payment.php', {
    method:'POST', headers:{'Content-Type':'application/json'},
    body: JSON.stringify({table_id:selectedTable, amount: total})
  });
  const data = await res.json();
  if(data.success){
    // simulate redirect to payment; we'll immediately mark completed (demo)
    const confirmRes = await fetch('api/complete_payment.php', {
      method:'POST', headers:{'Content-Type':'application/json'},
      body: JSON.stringify({payment_id: data.payment_id})
    });
    const r2 = await confirmRes.json();
    if(r2.success) { showMsg('Payment successful. Order marked paid.'); cart={}; renderCart(); }
  } else showMsg('Payment failed', true);
}

async function callService(){
  if(!selectedTable) return showMsg('Select a table first', true);
  const note = prompt('Anything to tell service? (optional)');
  const res = await fetch('api/call_service.php', {
    method:'POST', headers:{'Content-Type':'application/json'},
    body: JSON.stringify({table_id:selectedTable, note:note||''})
  });
  const r = await res.json();
  if(r.success) showMsg('Service requested');
}

function showMsg(txt, isError=false){ const m = document.getElementById('messages'); m.style.color = isError ? 'red' : 'green'; m.textContent = txt; setTimeout(()=> m.textContent='',4000); }

// start
init();
