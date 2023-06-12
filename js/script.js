let navbar = document.querySelector('.navbar');

document.querySelector('#menu-btn').onclick = () =>{
    navbar.classList.toggle('active');
    searchForm.classList.remove('active');
    cartItem.classList.remove('active');
}
let searchForm = document.querySelector('.search-form');

document.querySelector('#search-btn').onclick = () =>{
    searchForm.classList.toggle('active');
    navbar.classList.remove('active');
    cartItem.classList.remove('active');
}

let cartItem = document.querySelector('.cart-items-container');

document.querySelector('#cart-btn').onclick = showCart;

window.onscroll = (e) =>{
    navbar.classList.remove('active');
    searchForm.classList.remove('active');
    if (!$(e.target).is('.cart-items-container, .cart-items-container *'))
        cartItem.classList.remove('active');
}

let prices = JSON.parse($('#prices').text());
prices.forEach(p => {
    var $menu = $('#menu' + p.menu_id);
    if (!$menu.length) {
        $menu = $(document.createElement('div')).
        attr('class', 'box').
        attr('id', 'menu' + p.menu_id).
        append(`<img src="images/menu${p.menu_id}.jpg" alt="">`).
        append(`<h3>${p.nama}</h3>`);
        $('#menus').append($menu);
    }
    $menu.append(`<a href="javascript:addToCart(${p.id})" class="btn">${(p.label ? p.label + ': ' : '')}Rp${p.harga}</a>`);
});
renderCart();

function addToCart(price_id) {
    const cart = JSON.parse(localStorage.getItem('cart'));
    cart.push(price_id);
    localStorage.setItem('cart', JSON.stringify(cart));
    renderCart();
}

function renderCart() {
    var cart = localStorage.getItem('cart');
    if (!cart) localStorage.setItem('cart', '[]');
    cart = cart && JSON.parse(cart);
    cart = cart || [];
    $('.cart-items-container').children().remove();
    if (cart.length) {
        let $cart = $('.cart-items-container');
        cart.forEach((price_id, i) => {
            const m = prices.find(p => p.id == price_id);
            $cart.append(
                $('<div class="cart-item">').
                append(
                    $('<span class="fas fa-times"></span>').
                    on('click', () => removeICart(i))
                ).append(`<img src="images/menu${m.menu_id}.jpg" alt="">`).
                append(
                    $('<div class="content">').
                    append(`<h3>${m.nama}</h3>`).
                    append(`<div class="price">${(m.label ? m.label + ': ' : '')}Rp${m.harga}</div>`)
                )
            )
        });
        $cart.append('<a href="javascript:checkout()" class="btn">Checkout</a>');
        showCart();
    } else cartItem.classList.remove('active');
}

function removeICart(index) {
    const cart = JSON.parse(localStorage.getItem('cart'));
    cart.splice(index, 1);
    localStorage.setItem('cart', JSON.stringify(cart));
    renderCart();
}

function showCart() {
    cartItem.classList.add('active');
    navbar.classList.remove('active');
    searchForm.classList.remove('active');
}

async function checkout() {
    await $.ajax({
        method: 'post',
        url: 'pesanan.php',
        headers: {
            'Content-Type': 'text/json',
        },
        data: localStorage.getItem('cart'),
    });
    localStorage.setItem('cart', '[]');
    renderCart();
}
