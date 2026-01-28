/**
 * High-Performance Scroll Animations
 * Uses IntersectionObserver to trigger premium reveals without listening to scroll events.
 */

document.addEventListener('DOMContentLoaded', () => {
    initScrollAnimations();
});

function initScrollAnimations() {
    // 1. Observer for Section Headers
    const headerObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target); // Play once
            }
        });
    }, { threshold: 0.2, rootMargin: "0px 0px -50px 0px" });

    // Target all section headers (we will add this class to PHP)
    document.querySelectorAll('.section-header-animate').forEach(el => headerObserver.observe(el));

    // 2. Observer for Category Containers to trigger Staggered Card Animation
    // We observe the "track" or "section" parent to trigger the children
    const categoryObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Find all revealable children inside this section
                const cards = entry.target.querySelectorAll('.reveal-on-scroll');

                cards.forEach((card, index) => {
                    // Stagger delay calculation: 50ms per item
                    // Use setTimeout to stagger the class addition for smooth effect
                    // Or set transition-delay dynamically
                    card.style.transitionDelay = `${index * 80}ms`;
                    card.classList.add('is-visible');
                });

                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1, rootMargin: "0px 0px -50px 0px" });

    // Target category sliders/grids
    document.querySelectorAll('.category-scroll-trigger').forEach(el => categoryObserver.observe(el));
}
