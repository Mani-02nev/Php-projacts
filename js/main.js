// Univaut JavaScript

// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function () {
    // Add smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });

    // Product card animations
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });

    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = 'red';
                } else {
                    field.style.borderColor = '';
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields');
            }
        });
    });

    // Wishlist Toggle with AJAX
    document.querySelectorAll('a[href*="wishlist_toggle"]').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const url = this.href;
            const icon = this.querySelector('i');
            const productId = new URL(url).searchParams.get('wishlist_toggle');

            // Optimistic UI update
            const isCurrentlyInWishlist = icon.classList.contains('bi-heart-fill');

            if (isCurrentlyInWishlist) {
                icon.classList.remove('bi-heart-fill', 'text-danger');
                icon.classList.add('bi-heart');
            } else {
                icon.classList.remove('bi-heart');
                icon.classList.add('bi-heart-fill', 'text-danger');
                // Add bounce animation
                icon.style.animation = 'heartBounce 0.5s ease';
                setTimeout(() => icon.style.animation = '', 500);
            }

            // Send AJAX request to API
            fetch(`api/wishlist_toggle.php?product_id=${productId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show toast notification
                        if (typeof showToast === 'function') {
                            showToast(data.message, data.in_wishlist ? 'success' : 'info');
                        }
                    } else {
                        // Revert UI on failure
                        if (isCurrentlyInWishlist) {
                            icon.classList.remove('bi-heart');
                            icon.classList.add('bi-heart-fill', 'text-danger');
                        } else {
                            icon.classList.remove('bi-heart-fill', 'text-danger');
                            icon.classList.add('bi-heart');
                        }
                        if (typeof showToast === 'function') {
                            showToast(data.message || 'Error updating wishlist', 'danger');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Revert UI on error
                    if (isCurrentlyInWishlist) {
                        icon.classList.remove('bi-heart');
                        icon.classList.add('bi-heart-fill', 'text-danger');
                    } else {
                        icon.classList.remove('bi-heart-fill', 'text-danger');
                        icon.classList.add('bi-heart');
                    }
                    if (typeof showToast === 'function') {
                        showToast('Error updating wishlist', 'danger');
                    }
                });
        });
    });
});

// Heart bounce animation
const style = document.createElement('style');
style.textContent = `
    @keyframes heartBounce {
        0%, 100% { transform: scale(1); }
        25% { transform: scale(1.3); }
        50% { transform: scale(0.9); }
        75% { transform: scale(1.1); }
    }
`;
document.head.appendChild(style);
