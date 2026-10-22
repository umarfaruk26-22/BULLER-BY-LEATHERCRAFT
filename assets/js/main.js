/**
 * BULLER LEATHER — CLIENT INTERACTIONS & COMMERCE ENGINE
 */

document.addEventListener('DOMContentLoaded', () => {
    initMarketSwitcher();
    initCartEvents();
    initWishlistEvents();
    initNavbarScroll();
    initHeaderActionButtons();
    initReviewsCarousel();
});

// Patron Reviews Carousel Auto-play (5000ms delay)
function initReviewsCarousel() {
    const carouselEl = document.getElementById('patronReviewsCarousel');
    if (!carouselEl || typeof bootstrap === 'undefined' || !bootstrap.Carousel) return;
    new bootstrap.Carousel(carouselEl, {
        interval: 5000,
        ride: 'carousel',
        pause: 'hover',
        touch: true
    });
}

// Navbar scroll shadow
function initNavbarScroll() {
    const navbar = document.querySelector('.buller-navbar');
    if (!navbar) return;
    window.addEventListener('scroll', () => {
        if (window.scrollY > 40) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
}

// Market & Country Switcher
function initMarketSwitcher() {
    const marketOptions = document.querySelectorAll('.market-card-option');
    marketOptions.forEach(option => {
        option.addEventListener('click', async () => {
            const countryCode = option.dataset.countryCode;
            if (!countryCode) return;

            try {
                const res = await fetch(window.BULLER_CONFIG.baseUrl + '/api/market.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ country_code: countryCode })
                });
                const data = await res.json();
                if (data.success) {
                    showToast('Market Updated', `Shopping experience localized to ${data.country_name}`);
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    showToast('Market Error', data.message || 'Could not switch market', 'error');
                }
            } catch (err) {
                console.error('Market switch error:', err);
            }
        });
    });
}

// Cart Drawer & AJAX Actions
function initCartEvents() {
    // Quick Add buttons
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.btn-quick-add, .btn-add-to-cart');
        if (!btn) return;
        e.preventDefault();

        const productId = btn.dataset.productId;
        const qty = parseInt(btn.dataset.quantity || '1', 10);
        if (!productId) return;

        const originalText = btn.innerHTML;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status"></span> Adding...`;
        btn.disabled = true;

        try {
            const res = await fetch(window.BULLER_CONFIG.baseUrl + '/api/cart.php?action=add', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId, quantity: qty })
            });
            const data = await res.json();
            if (data.success) {
                updateCartCount(data.cart_count);
                showToast('Added to Bag', data.message || 'Item added successfully');
                refreshCartDrawer();
                openCartDrawer();
            } else {
                showToast('Notice', data.message, 'warning');
            }
        } catch (err) {
            console.error('Add to cart failed:', err);
            showToast('Error', 'Unable to add item to bag', 'error');
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    });

    // Cart Drawer Quantity Buttons
    document.addEventListener('click', async (e) => {
        const qtyBtn = e.target.closest('.cart-qty-btn');
        if (!qtyBtn) return;
        const productId = qtyBtn.dataset.productId;
        const action = qtyBtn.dataset.action; // 'inc' or 'dec'
        const currentQty = parseInt(qtyBtn.dataset.currentQty, 10);
        let newQty = action === 'inc' ? currentQty + 1 : currentQty - 1;

        await updateCartItem(productId, newQty);
    });

    // Cart Drawer Remove Button
    document.addEventListener('click', async (e) => {
        const removeBtn = e.target.closest('.cart-item-remove');
        if (!removeBtn) return;
        const productId = removeBtn.dataset.productId;
        await updateCartItem(productId, 0);
    });
}

async function updateCartItem(productId, quantity) {
    try {
        const res = await fetch(window.BULLER_CONFIG.baseUrl + '/api/cart.php?action=update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId, quantity: quantity })
        });
        const data = await res.json();
        if (data.success) {
            updateCartCount(data.cart_count);
            refreshCartDrawer();
        }
    } catch (err) {
        console.error('Update cart item error:', err);
    }
}

async function refreshCartDrawer() {
    const container = document.getElementById('cart-drawer-items-container');
    const subtotalEl = document.getElementById('cart-drawer-subtotal');
    if (!container) return;

    try {
        const res = await fetch(window.BULLER_CONFIG.baseUrl + '/api/cart.php?action=get');
        const data = await res.json();

        if (data.items.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5">
                    <i class="bi bi-bag text-muted" style="font-size: 3rem;"></i>
                    <p class="mt-3 text-muted">Your luxury shopping bag is currently empty.</p>
                    <a href="${window.BULLER_CONFIG.baseUrl}/shop" class="btn btn-buller-primary btn-sm mt-2">Explore Collections</a>
                </div>
            `;
            if (subtotalEl) subtotalEl.textContent = data.formatted_subtotal;
            return;
        }

        let html = '';
        data.items.forEach(item => {
            html += `
                <div class="cart-item-row">
                    <img src="${window.BULLER_CONFIG.baseUrl}/${item.primary_image}" alt="${item.title}" class="cart-item-img">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <h6 class="mb-1" style="font-size: 0.95rem; font-family: var(--font-heading);">
                                <a href="${window.BULLER_CONFIG.baseUrl}/product?slug=${item.slug}" class="text-dark">${item.title}</a>
                            </h6>
                            <button class="btn btn-link text-muted p-0 cart-item-remove" data-product-id="${item.product_id}">
                                <i class="bi bi-trash3" style="font-size: 0.85rem;"></i>
                            </button>
                        </div>
                        <div class="text-muted" style="font-size: 0.75rem;">${item.leather_type || ''}</div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <div class="d-flex align-items-center gap-1">
                                <button class="cart-qty-btn" data-action="dec" data-product-id="${item.product_id}" data-current-qty="${item.quantity}">-</button>
                                <span class="px-2" style="font-size: 0.85rem; font-weight: 600;">${item.quantity}</span>
                                <button class="cart-qty-btn" data-action="inc" data-product-id="${item.product_id}" data-current-qty="${item.quantity}">+</button>
                            </div>
                            <div class="fw-bold" style="font-size: 0.95rem; color: var(--color-espresso-deep);">
                                ${item.formatted_total}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
        if (subtotalEl) subtotalEl.textContent = data.formatted_subtotal;
    } catch (err) {
        console.error('Error refreshing cart drawer:', err);
    }
}

// Header Action Buttons (Search, Account, Wishlist, Cart Drawer)
function initHeaderActionButtons() {
    // 1. Search Action Trigger
    document.addEventListener('click', (e) => {
        const searchBtn = e.target.closest('#bullerSearchBtn, .nav-action-search, [data-bs-target="#searchModal"]');
        if (!searchBtn) return;
        e.preventDefault();
        e.stopPropagation();
        openSearchModal();
    });

    // 2. Shopping Bag / Cart Action Trigger
    document.addEventListener('click', (e) => {
        const cartBtn = e.target.closest('#bullerCartBtn, .nav-action-cart, [data-bs-target="#cartDrawer"]');
        if (!cartBtn) return;
        e.preventDefault();
        e.stopPropagation();
        refreshCartDrawer();
        openCartDrawer();
    });

    // 3. Client Account Dropdown Trigger (Rock-solid for both Mobile & Desktop)
    const initAccountDropdown = () => {
        document.addEventListener('click', (e) => {
            const accountBtn = e.target.closest('#bullerAccountBtn, .nav-action-account');
            if (accountBtn) {
                e.preventDefault();
                e.stopPropagation();
                const parentDropdown = accountBtn.closest('.nav-action-account-dropdown');
                if (!parentDropdown) return;
                const menu = parentDropdown.querySelector('.dropdown-menu');
                if (!menu) return;

                const isShown = menu.classList.contains('show');
                // Close other open menus
                document.querySelectorAll('.dropdown-menu.show').forEach(m => {
                    if (m !== menu) m.classList.remove('show');
                });

                if (isShown) {
                    menu.classList.remove('show');
                    accountBtn.setAttribute('aria-expanded', 'false');
                } else {
                    menu.classList.add('show');
                    accountBtn.setAttribute('aria-expanded', 'true');
                }
            } else {
                // Click outside closes any open account dropdown
                if (!e.target.closest('.nav-action-account-dropdown')) {
                    const openMenus = document.querySelectorAll('.nav-action-account-dropdown .dropdown-menu.show');
                    openMenus.forEach(m => m.classList.remove('show'));
                    const accBtn = document.getElementById('bullerAccountBtn');
                    if (accBtn) accBtn.setAttribute('aria-expanded', 'false');
                }
            }
        });
    };
    initAccountDropdown();

    // 4. Keyboard accessibility: Escape closes search or cart
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const searchModal = document.getElementById('searchModal');
            if (searchModal && searchModal.classList.contains('show') && typeof bootstrap !== 'undefined') {
                bootstrap.Modal.getOrCreateInstance(searchModal).hide();
            }
            const cartDrawer = document.getElementById('cartDrawer');
            if (cartDrawer && cartDrawer.classList.contains('show') && typeof bootstrap !== 'undefined') {
                bootstrap.Offcanvas.getOrCreateInstance(cartDrawer).hide();
            }
        }
    });
}

function openSearchModal() {
    const modalEl = document.getElementById('searchModal');
    if (!modalEl) return;
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
        bsModal.show();
    } else {
        modalEl.classList.add('show');
        modalEl.style.display = 'block';
    }
    setTimeout(() => {
        const input = modalEl.querySelector('input[name="q"]');
        if (input) {
            input.focus();
            input.select();
        }
    }, 250);
}

function openCartDrawer() {
    const drawerEl = document.getElementById('cartDrawer');
    if (!drawerEl) return;
    if (typeof bootstrap !== 'undefined' && bootstrap.Offcanvas) {
        const bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(drawerEl);
        bsOffcanvas.show();
    } else {
        drawerEl.classList.add('show');
        drawerEl.style.visibility = 'visible';
    }
}

function updateCartCount(count) {
    const badges = document.querySelectorAll('.cart-count-badge');
    badges.forEach(b => {
        b.textContent = count;
        b.style.display = count > 0 ? 'flex' : 'none';
    });
}

// Wishlist
function initWishlistEvents() {
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.wishlist-toggle-btn');
        if (!btn) return;
        e.preventDefault();

        const productId = btn.dataset.productId;
        if (!productId) return;

        try {
            const res = await fetch(window.BULLER_CONFIG.baseUrl + '/api/wishlist.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId })
            });
            const data = await res.json();
            if (data.success) {
                if (data.added) {
                    btn.classList.add('active');
                    btn.innerHTML = '<i class="bi bi-heart-fill"></i>';
                    showToast('Saved to Wishlist', 'Product added to your wishlist');
                } else {
                    btn.classList.remove('active');
                    btn.innerHTML = '<i class="bi bi-heart"></i>';
                    showToast('Wishlist Updated', 'Product removed from your wishlist');
                }
                const badges = document.querySelectorAll('.wishlist-count-badge');
                badges.forEach(b => {
                    b.textContent = data.count;
                    b.style.display = data.count > 0 ? 'flex' : 'none';
                });
            }
        } catch (err) {
            console.error('Wishlist error:', err);
        }
    });
}

// Toast Notifications
function showToast(title, message, type = 'success') {
    let container = document.getElementById('buller-toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'buller-toast-container';
        container.className = 'position-fixed bottom-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
    }

    const toastEl = document.createElement('div');
    toastEl.className = 'toast align-items-center border-0 shadow-lg mb-2';
    toastEl.style.backgroundColor = type === 'error' ? '#842029' : (type === 'warning' ? '#664d03' : '#1f140c');
    toastEl.style.color = '#fdfbf7';
    toastEl.style.borderLeft = '4px solid #c5a880';
    toastEl.setAttribute('role', 'alert');

    toastEl.innerHTML = `
        <div class="d-flex">
            <div class="toast-body py-3">
                <strong class="d-block mb-1" style="font-family: var(--font-heading); color: #c5a880;">${title}</strong>
                <span style="font-size: 0.88rem;">${message}</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

    container.appendChild(toastEl);
    if (typeof bootstrap !== 'undefined') {
        const bsToast = new bootstrap.Toast(toastEl, { delay: 4000 });
        bsToast.show();
        toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
    }
}

// Global Product Card Click Delegation (Clicking anywhere on card opens product page)
document.addEventListener('click', (e) => {
    const card = e.target.closest('.product-card[data-product-url]');
    if (!card) return;
    if (e.target.closest('button, .btn-quick-add, .wishlist-toggle-btn, .btn-add-to-cart, a')) {
        return;
    }
    const targetUrl = card.dataset.productUrl;
    if (targetUrl) {
        window.location.href = targetUrl;
    }
});

