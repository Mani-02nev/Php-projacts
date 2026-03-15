/**
 * AJAX Interactions for No-Reload Experience
 * Handles Add to Cart and Wishlist toggles seamlessly.
 */

document.addEventListener('DOMContentLoaded', () => {
    setupAjaxAddToCart();
    setupAjaxWishlist();
});

function setupAjaxAddToCart() {
    // 1. Product Detail Page "Add to Cart" Button
    const detailBtn = document.getElementById('btn-ajax-add-cart');
    if (detailBtn) {
        detailBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

            const btn = e.target.closest('button'); // Ensure we have the button even if clicked on icon/text inside
            const form = btn.closest('form');
            const productId = new URLSearchParams(window.location.search).get('id');
            const qtyInput = form.querySelector('input[name="quantity"]');
            const quantity = qtyInput ? qtyInput.value : 1;

            // Image for animation
            const img = document.querySelector('.product-image-container img');

            handleAddToCart(productId, quantity, btn, img);
        });
    }

    // 2. Product Listing Page "ADD" Buttons (Delegation)
    document.body.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-add');
        if (btn) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

            // Extract ID
            let id = btn.getAttribute('data-id');
            // Flashback to parsing if data-id missing
            if (!id && btn.getAttribute('onclick')) {
                const match = btn.getAttribute('onclick').match(/add_to_cart=(\d+)/);
                if (match) id = match[1];
            }

            if (id) {
                // Find Image
                // We assume btn is inside .card-content which is inside .global-product-card
                const card = btn.closest('.global-product-card');
                const img = card ? card.querySelector('.img-wrapper img') : null;

                handleAddToCart(id, 1, btn, img);
            }
        }
    });
}

function handleAddToCart(productId, quantity, btnElement, imgElement) {
    // 1. Play 3D Animation
    if (window.addToCartAnim && imgElement) {
        window.addToCartAnim.fly(imgElement, '.bi-bag-fill', () => {
            // Animation complete callback (optional UI updates)
        });
    }

    // 2. Send AJAX Request
    fetch('ajax_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: 'add_to_cart',
            product_id: productId,
            quantity: quantity
        })
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Update UI (e.g., Cart Count Badge)
                // Assuming there's a cart count badge somewhere, if not we might not update visuals other than animation
                console.log('Added to cart, new count:', data.cart_count);

                // Show Toast
                if (window.showToast) window.showToast("Product added to cart", "success");
            }
        })
        .catch(err => console.error(err));
}

function setupAjaxWishlist() {
    // Use capture phase so we handle the click before it bubbles to the parent <a> (prevents navigation)
    document.body.addEventListener('click', (e) => {
        // Target 1: Class .wishlist-btn (Home/Shop cards) — only toggle wishlist, never open product page
        let btn = e.target.closest('.wishlist-btn');

        // Target 2: Anchor links (Detail page main heart)
        if (!btn) {
            btn = e.target.closest('a[href*="wishlist_toggle"]');
        }

        if (btn) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

            let productId = btn.getAttribute('data-id');

            // Fallback: Parse from href or onclick
            if (!productId && btn.href) {
                const match = btn.href.match(/wishlist_toggle=(\d+)/);
                if (match) productId = match[1];
            }
            if (!productId && btn.getAttribute('onclick')) {
                const match = btn.getAttribute('onclick').match(/wishlist_toggle=(\d+)/);
                if (match) productId = match[1];
            }

            if (productId) {
                handleWishlistToggle(productId, btn);
            }
        }
    }, true); // capture: true so parent <a> never receives the click
}

function handleWishlistToggle(productId, btnElement) {
    const icon = btnElement.querySelector('i');
    if (!icon) return;

    // 1. Play heart pop animation (scale 1 → 1.2 → 1)
    btnElement.classList.add('heart-pop');
    btnElement.addEventListener('animationend', function removePop() {
        btnElement.classList.remove('heart-pop');
        btnElement.removeEventListener('animationend', removePop);
    }, { once: true });

    // 2. Optional 3D animation if available
    if (window.wishlistAnim) {
        window.wishlistAnim.play(btnElement);
    }

    // 3. Optimistic UI: toggle icon and .active immediately for snappy feedback
    const isActive = btnElement.classList.toggle('active');
    if (isActive) {
        icon.classList.remove('bi-heart');
        icon.classList.add('bi-heart-fill');
        icon.classList.remove('text-body');
        icon.classList.add('text-danger');
    } else {
        icon.classList.remove('bi-heart-fill', 'text-danger');
        icon.classList.add('bi-heart');
        icon.classList.add('text-body');
    }

    // 4. Persist via AJAX and revert UI if request fails
    fetch('ajax_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: 'toggle_wishlist',
            product_id: productId
        })
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Sync UI with server (in case of race or different state)
                if (data.status === 'added') {
                    btnElement.classList.add('active');
                    icon.classList.remove('bi-heart');
                    icon.classList.add('bi-heart-fill', 'text-danger');
                    icon.classList.remove('text-body');
                } else {
                    btnElement.classList.remove('active');
                    icon.classList.remove('bi-heart-fill', 'text-danger');
                    icon.classList.add('bi-heart');
                    icon.classList.add('text-body');
                    // On wishlist page, remove the card from DOM when item is removed
                    if (window.location.pathname.indexOf('wishlist') !== -1) {
                        const card = btnElement.closest('a.global-product-card');
                        if (card) card.remove();
                    }
                }
            } else {
                // Revert optimistic update
                btnElement.classList.toggle('active');
                if (icon.classList.contains('bi-heart-fill')) {
                    icon.classList.remove('bi-heart-fill', 'text-danger');
                    icon.classList.add('bi-heart', 'text-body');
                } else {
                    icon.classList.remove('bi-heart', 'text-body');
                    icon.classList.add('bi-heart-fill', 'text-danger');
                }
            }
        })
        .catch(() => {
            // Revert optimistic update on network error
            btnElement.classList.toggle('active');
            if (icon.classList.contains('bi-heart-fill')) {
                icon.classList.remove('bi-heart-fill', 'text-danger');
                icon.classList.add('bi-heart', 'text-body');
            } else {
                icon.classList.remove('bi-heart', 'text-body');
                icon.classList.add('bi-heart-fill', 'text-danger');
            }
        });
}
