// === Helper: nhúng component header/footer ===
(function includePartials(){
  const nodes = document.querySelectorAll("[data-include]");
  nodes.forEach(async (el) => {
    const url = el.getAttribute("data-include");
    const res = await fetch(url);
    el.outerHTML = await res.text(); // thay chính nó bằng nội dung file
    // sau khi header vào DOM, cập nhật số lượng giỏ
    if (url.includes("header.html")) updateCartBadge();
  });
})();

// === Mock dữ liệu (tạm thời chưa có backend) ===
const PRODUCTS_NEW = [
  {id:1, name:"White cheese & caramel cake", price:335000, image:"../assets/img/sp1.jpg"},
  {id:2, name:"Floss Pork Bread", price:13000, image:"../assets/img/sp2.jpg"},
  {id:3, name:"Bánh Mỳ Que", price:14000, image:"../assets/img/sp3.jpg"},
  {id:4, name:"Banana Cheese Cake", price:37000, image:"../assets/img/sp4.jpg"},
  {id:5, name:"Coconut Mochi", price:16000, image:"../assets/img/sp5.jpg"},
  {id:6, name:"Flan Pudding Chocolate", price:16000, image:"../assets/img/sp6.jpg"}
];
const PRODUCTS_BEST = [
  {id:7, name:"Choux Cream", price:32000, image:"../assets/img/sp7.jpg"},
  {id:8, name:"Hawaii mousse", price:335000, image:"../assets/img/sp8.jpg"},
  {id:9, name:"Bánh Croissant", price:17000, image:"../assets/img/sp9.jpg"}
];

function vnd(n){ return n.toLocaleString('vi-VN') + 'đ'; }

function renderProducts(list, targetId){
  const el = document.getElementById(targetId);
  el.innerHTML = list.map(p => `
    <article class="card">
      <img src="${p.image}" alt="${p.name}"/>
      <div class="p-12">
        <h3>${p.name}</h3>
        <p class="price">${vnd(p.price)}</p>
        <button class="btn" onclick='addToCart(${JSON.stringify(p)})'>Thêm vào giỏ</button>
      </div>
    </article>
  `).join('');
}

function getCart(){ return JSON.parse(localStorage.getItem('cart') || '[]'); }
function setCart(c){ localStorage.setItem('cart', JSON.stringify(c)); updateCartBadge(); }
function updateCartBadge(){
  const badge = document.querySelector('#cart-count');
  if (!badge) return;
  const total = getCart().reduce((s,i)=>s+i.qty,0);
  badge.textContent = total;
}
function addToCart(p){
  const cart = getCart();
  const i = cart.findIndex(x => x.id === p.id);
  if (i >= 0) cart[i].qty += 1; else cart.push({...p, qty:1});
  setCart(cart);
  alert("Đã thêm vào giỏ!");
}

document.addEventListener('DOMContentLoaded', () => {
  renderProducts(PRODUCTS_NEW, 'new-products');
  renderProducts(PRODUCTS_BEST, 'best-products');
});
