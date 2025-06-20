// Simple cart logic
const products = [
  {id:1, name:'Handmade Basket', price:200},
  {id:2, name:'Beaded Necklace', price:150},
  {id:3, name:'Woven Scarf', price:300}
];
function renderCart() {
  let cart = JSON.parse(localStorage.getItem('cart')||'[]');
  let items = cart.map(id => products.find(p=>p.id===id)).filter(Boolean);
  const list = document.getElementById('cartItems');
  const total = document.getElementById('cartTotal');
  list.innerHTML = items.length ? items.map(p=>`<li><span class='cart-item-name'>${p.name}</span> <span class='cart-item-price'>${p.price} ETB</span></li>`).join('') : '<li><em>Cart is empty.</em></li>';
  total.innerHTML = items.length ? `Total: ${items.reduce((a,b)=>a+b.price,0)} ETB` : '';
  // Disable checkout if cart is empty
  const form = document.getElementById('checkoutForm');
  if (form) form.style.display = items.length ? '' : 'none';
  // Attach cart data to form on submit
  if (form) {
    form.onsubmit = function(e) {
      document.getElementById('orderStatus').innerHTML = '<em>Processing payment...</em>';
      // Add cart data as hidden input
      let input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'cart';
      input.value = JSON.stringify(items);
      form.appendChild(input);
    };
  }
}
window.onload = renderCart;
