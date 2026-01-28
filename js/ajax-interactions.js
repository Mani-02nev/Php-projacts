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
    // Delegation for all Heart logic (Home, Shop, Detail)
    document.body.addEventListener('click', (e) => {
        // Target 1: Class .wishlist-btn (Home/Shop cards)
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
    }); // Capture if needed, but standard bubble is fine for delegation
}

function handleWishlistToggle(productId, btnElement) {
    // 1. Determine current state BEFORE animation
    const icon = btnElement.querySelector('i');
    const isCurrentlyInWishlist = icon && (icon.classList.contains('bi-heart-fill') || btnElement.classList.contains('active'));

    // 2. Play 3D Animation
    if (window.wishlistAnim) {
        window.wishlistAnim.play(btnElement);
    }

    // 3. Send AJAX Request
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
                // Update UI based on backend response
                if (icon) {
                    if (data.status === 'added') {
                        // Item was added to wishlist - show filled heart
                        icon.classList.remove('bi-heart');
                        icon.classList.add('bi-heart-fill');
                        if (icon.classList.contains('text-white')) {
                            icon.classList.remove('text-white');
                            icon.classList.add('text-danger');
                        }
                        btnElement.classList.add('active');
                    } else {
                        // Item was removed from wishlist - show outline heart
                        icon.classList.remove('bi-heart-fill');
                        icon.classList.add('bi-heart');
                        if (icon.classList.contains('text-danger')) {
                            icon.classList.remove('text-danger');
                            icon.classList.add('text-white');
                        }
                        btnElement.classList.remove('active');
                    }
                }
            } else {
                console.error('Wishlist toggle failed', data);
            }
        })
        .catch(err => {
            console.error('Wishlist error:', err);
        });
}
