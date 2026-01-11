<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Home';

// Fetch featured products from CSV
$all_products = get_all_products();
$featured_products = array_slice($all_products, 0, 15); // Get 15 products for carousel

include 'includes/header.php';
?>

<style>
/* Auto-Scrollable Carousel */
.carousel-section {
    padding: 3rem 0;
    background: var(--white);
    overflow: hidden;
}

.carousel-wrapper {
    position: relative;
    margin: 2rem 0;
}

.carousel-track-container {
    overflow: hidden;
    position: relative;
    padding: 1rem 0;
}

.carousel-track {
    display: flex;
    gap: 1.5rem;
    transition: transform 0.5s ease;
    padding: 0 1rem;
}

.carousel-card {
    min-width: 280px;
    max-width: 280px;
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.carousel-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
    border-color: var(--black);
}

.carousel-card-image {
    width: 100%;
    height: 220px;
    background: var(--white);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    border-bottom: 1px solid var(--gray-200);
    position: relative;
    overflow: hidden;
}

.carousel-card-image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    transition: transform 0.3s ease;
}

.carousel-card:hover .carousel-card-image img {
    transform: scale(1.1);
}

.carousel-card-badge {
    position: absolute;
    top: 0.75rem;
    right: 0.75rem;
    background: var(--black);
    color: var(--white);
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 700;
}

.carousel-card-info {
    padding: 1rem;
}

.carousel-card-name {
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 2.5rem;
    line-height: 1.3;
}

.carousel-card-rating {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.75rem;
    margin-bottom: 0.5rem;
    color: var(--gray-600);
}

.carousel-card-rating .stars {
    color: #ffa500;
}

.carousel-card-price {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--black);
    margin-bottom: 0.75rem;
}

.carousel-card-btn {
    width: 100%;
    padding: 0.625rem;
    background: var(--black);
    color: var(--white);
    text-decoration: none;
    border-radius: 4px;
    font-size: 0.875rem;
    font-weight: 600;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.carousel-card-btn:hover {
    background: var(--gray-800);
}

.carousel-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: var(--white);
    border: 2px solid var(--black);
    color: var(--black);
    font-size: 1.5rem;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.carousel-nav:hover {
    background: var(--black);
    color: var(--white);
    transform: translateY(-50%) scale(1.1);
}

.carousel-nav-prev {
    left: -25px;
}

.carousel-nav-next {
    right: -25px;
}

.carousel-dots {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 1.5rem;
}

.carousel-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--gray-300);
    cursor: pointer;
    transition: var(--transition);
}

.carousel-dot.active {
    background: var(--black);
    width: 24px;
    border-radius: 4px;
}

.auto-scroll-indicator {
    text-align: center;
    margin-top: 1rem;
    font-size: 0.875rem;
    color: var(--gray-600);
}

.auto-scroll-indicator i {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
</style>


<!-- Auto-Scrollable Carousel -->
<section class="carousel-section">
    <div class="container">
        <h2 class="section-title"><i class="bi bi-stars"></i> Featured Products</h2>
        
        <div class="carousel-wrapper">
            <button class="carousel-nav carousel-nav-prev" onclick="moveCarousel(-1)">
                <i class="bi bi-chevron-left"></i>
            </button>
            
            <div class="carousel-track-container">
                <div class="carousel-track" id="carouselTrack">
                    <?php foreach ($featured_products as $index => $product): ?>
                        <div class="carousel-card">
                            <div class="carousel-card-image">
                                <?php if (!empty($product['image'])): ?>
                                    <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                                         onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'200\'%3E%3Crect fill=\'%23f5f5f5\' width=\'200\' height=\'200\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\' font-size=\'14\'%3E📦%3C/text%3E%3C/svg%3E'">
                                <?php endif; ?>
                                <?php if ($product['stock'] < 20): ?>
                                    <span class="carousel-card-badge">🔥 Hot</span>
                                <?php endif; ?>
                            </div>
                            <div class="carousel-card-info">
                                <h3 class="carousel-card-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                                <div class="carousel-card-rating">
                                    <span class="stars">⭐⭐⭐⭐☆</span>
                                    <span>(<?php echo rand(100, 9999); ?>)</span>
                                </div>
                                <div class="carousel-card-price">₹<?php echo number_format($product['price'], 0); ?></div>
                                <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="carousel-card-btn">
                                    <i class="bi bi-eye"></i> View Details
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <button class="carousel-nav carousel-nav-next" onclick="moveCarousel(1)">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>
        
        <div class="carousel-dots" id="carouselDots"></div>
        
        <div class="auto-scroll-indicator">
            <i class="bi bi-arrow-left-right"></i> Auto-scrolling • Hover to pause
        </div>
    </div>
</section>

<!-- Regular Products Grid -->
<div class="container">
    <h2 class="section-title"><i class="bi bi-grid"></i> More Products</h2>
    
    <div class="products-grid">
        <?php 
        $more_products = array_slice($all_products, 15, 6);
        if (!empty($more_products)): 
        ?>
            <?php foreach ($more_products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php if (!empty($product['image'])): ?>
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'150\' height=\'150\'%3E%3Crect fill=\'%23f5f5f5\' width=\'150\' height=\'150\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\' font-size=\'12\'%3E📦%3C/text%3E%3C/svg%3E'">
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <div class="product-rating">
                            <span class="stars">⭐⭐⭐⭐☆</span>
                            <span>(<?php echo rand(100, 9999); ?>)</span>
                        </div>
                        <div class="product-price">₹<?php echo number_format($product['price'], 0); ?></div>
                        <div class="product-meta">
                            <span>📦 <?php echo $product['stock']; ?></span>
                            <span>🆔 #<?php echo $product['id']; ?></span>
                        </div>
                        <div class="product-actions">
                            <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="btn btn-black">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <div style="text-align: center; margin: 3rem 0;">
        <a href="products.php" class="btn btn-black" style="font-size: 1.1rem; padding: 1rem 3rem;">
            <i class="bi bi-grid"></i> View All Products
        </a>
    </div>
</div>

<script>
const track = document.getElementById('carouselTrack');
const cards = track.querySelectorAll('.carousel-card');
const dotsContainer = document.getElementById('carouselDots');

let currentIndex = 0;
let autoScrollInterval;
const cardWidth = 280 + 24; // card width + gap
const visibleCards = Math.floor(window.innerWidth / cardWidth);
const maxIndex = Math.max(0, cards.length - visibleCards);

// Create dots
const dotsCount = Math.ceil(cards.length / visibleCards);
for (let i = 0; i < dotsCount; i++) {
    const dot = document.createElement('div');
    dot.className = 'carousel-dot' + (i === 0 ? ' active' : '');
    dot.onclick = () => goToPage(i);
    dotsContainer.appendChild(dot);
}

function updateCarousel() {
    const offset = -currentIndex * cardWidth;
    track.style.transform = `translateX(${offset}px)`;
    
    // Update dots
    const dots = dotsContainer.querySelectorAll('.carousel-dot');
    const activeDot = Math.floor(currentIndex / visibleCards);
    dots.forEach((dot, index) => {
        dot.classList.toggle('active', index === activeDot);
    });
}

function moveCarousel(direction) {
    currentIndex += direction * visibleCards;
    
    // Loop around
    if (currentIndex < 0) {
        currentIndex = maxIndex;
    } else if (currentIndex > maxIndex) {
        currentIndex = 0;
    }
    
    updateCarousel();
}

function goToPage(page) {
    currentIndex = page * visibleCards;
    if (currentIndex > maxIndex) currentIndex = maxIndex;
    updateCarousel();
}

function startAutoScroll() {
    autoScrollInterval = setInterval(() => {
        currentIndex++;
        if (currentIndex > maxIndex) {
            currentIndex = 0;
        }
        updateCarousel();
    }, 3000);
}

function stopAutoScroll() {
    clearInterval(autoScrollInterval);
}

// Auto-scroll
startAutoScroll();

// Pause on hover
track.addEventListener('mouseenter', stopAutoScroll);
track.addEventListener('mouseleave', startAutoScroll);

// Handle window resize
window.addEventListener('resize', () => {
    const newVisibleCards = Math.floor(window.innerWidth / cardWidth);
    if (newVisibleCards !== visibleCards) {
        location.reload();
    }
});

// Initialize
updateCarousel();
</script>

<?php include 'includes/footer.php'; ?>
