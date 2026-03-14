<?php
/**
 * Local Mart — Univault Hyperlocal Shopping
 * Leaflet.js + OpenStreetMap + GeoJSON  (no paid APIs)
 * Page: local-mart.php
 */

$page_title = 'Local Mart – Discover Nearby Shops in Trichy';
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Count real shops from the OSM export file
$geojson_path = __DIR__ . '/data/trichy_shops.geojson';
$shop_count   = 0;
if (file_exists($geojson_path)) {
    $geo = json_decode(file_get_contents($geojson_path), true);
    $shop_count = count(array_filter(
        $geo['features'] ?? [],
        fn($f) => isset($f['geometry']['type']) && $f['geometry']['type'] === 'Point'
    ));
}

include 'includes/header.php';

?>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" crossorigin="" />

<!-- Local Mart CSS -->
<link rel="stylesheet" href="css/local-mart.css">

<div class="lm-page">

<!-- ══════════════════════════════════════════════════════════════
     HERO
     ══════════════════════════════════════════════════════════════ -->
<section class="lm-hero">
    <div class="container position-relative z-2 text-center">
        <div class="lm-hero-badge">
            <i class="bi bi-geo-alt-fill"></i>
            LIVE HYPERLOCAL · TRICHY · <?= $shop_count ?> REAL SHOPS
        </div>
        <h1 class="display-5 fw-bold mb-3">
            Shop from <span style="color:#a5b4fc;">Local Stores</span> Near You
        </h1>
        <p class="lead mb-4">
            Discover nearby shops in Trichy, compare products, and get directions instantly.<br class="d-none d-md-block">
            Powered by Leaflet.js &amp; OpenStreetMap — 100% free &amp; offline-ready.
        </p>
        <div class="d-flex justify-content-center gap-4 flex-wrap">
            <div class="lm-hero-stat">
                <span id="statShopCount"><?= $shop_count ?></span>
                <small>OSM Shops</small>
            </div>
            <div class="lm-hero-stat lm-hero-stat--divider">
                <span id="statOpenCount">—</span>
                <small>Open Now</small>
            </div>
            <div class="lm-hero-stat lm-hero-stat--divider">
                <span id="statProductCount">50</span>
                <small>Products Available</small>
            </div>
            <div class="lm-hero-stat lm-hero-stat--divider">
                <span id="statRadius">5 km</span>
                <small>Search Radius</small>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════════════════════
     CONTROL BAR
     ══════════════════════════════════════════════════════════════ -->
<div class="lm-control-bar" id="lmControlBar">
    <div class="container">
        <div class="d-flex flex-wrap gap-3 align-items-center">

            <!-- Product Search -->
            <div class="lm-search-wrapper">
                <i class="bi bi-search search-icon"></i>
                <input type="text"
                       id="productSearchInput"
                       class="lm-search-input"
                       placeholder="Search product — milk, rice, charger, medicine…"
                       autocomplete="off"
                       aria-label="Search products in nearby shops">
                <div class="lm-search-suggestions" id="searchSuggestions" role="listbox"></div>
            </div>

            <!-- Radius -->
            <div class="d-flex align-items-center gap-2">
                <span class="lm-filter-label"><i class="bi bi-circle me-1"></i>Radius</span>
                <div class="lm-radius-selector">
                    <button class="lm-radius-option" data-km="1">1 km</button>
                    <button class="lm-radius-option" data-km="3">3 km</button>
                    <button class="lm-radius-option active" data-km="5">5 km</button>
                    <button class="lm-radius-option" data-km="10">10 km</button>
                    <button class="lm-radius-option" data-km="20">20 km</button>
                </div>
            </div>

            <!-- Shop Type -->
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="lm-filter-label"><i class="bi bi-shop me-1"></i>Type</span>
                <button class="lm-pill active" data-filter-type="all">All</button>
                <button class="lm-pill" data-filter-type="grocery">🛒 Grocery</button>
                <button class="lm-pill" data-filter-type="vegetables">🥦 Vegetables</button>
                <button class="lm-pill" data-filter-type="electronics">📱 Electronics</button>
                <button class="lm-pill" data-filter-type="supermarket">🏬 Supermarket</button>
            </div>

            <!-- Rating -->
            <div class="d-flex align-items-center gap-2">
                <span class="lm-filter-label"><i class="bi bi-star me-1"></i>Rating</span>
                <button class="lm-pill active" data-filter-rating="0">Any</button>
                <button class="lm-pill" data-filter-rating="4">4★+</button>
            </div>

            <!-- My Location -->
            <button id="redetectLocation" class="lm-pill ms-auto"
                    style="border-color:var(--lm-accent);color:var(--lm-accent);">
                <i class="bi bi-crosshair me-1"></i>My Location
            </button>

        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     MAIN 2-COLUMN LAYOUT
     ══════════════════════════════════════════════════════════════ -->
<div class="container">
    <div class="lm-quick-search-row">
        <div class="lm-chip" onclick="window.LM.quickSearch('Milk')"><i class="bi bi-cup-hot"></i> Milk</div>
        <div class="lm-chip" onclick="window.LM.quickSearch('Bread')"><i class="bi bi-egg-fill"></i> Bread</div>
        <div class="lm-chip" onclick="window.LM.quickSearch('Rice')"><i class="bi bi-box-seam"></i> Rice</div>
        <div class="lm-chip" onclick="window.LM.quickSearch('Vegetables')"><i class="bi bi-leaf"></i> Vegetables</div>
        <div class="lm-chip" onclick="window.LM.quickSearch('Fruits')"><i class="bi bi-apple"></i> Fruits</div>
        <div class="lm-chip" onclick="window.LM.quickSearch('Medicine')"><i class="bi bi-capsule"></i> Medicine</div>
        <div class="lm-chip" onclick="window.LM.quickSearch('Electronics')"><i class="bi bi-phone"></i> Electronics</div>
        <div class="lm-chip" onclick="window.LM.quickSearch('Hardware')"><i class="bi bi-tools"></i> Hardware</div>
    </div>
</div>

<div class="container">
    <div class="lm-content-area">

        <!-- ── LEFT: LEAFLET MAP ─────────────────────────── -->
        <div class="lm-map-panel">
            <div class="lm-map-container" aria-label="OpenStreetMap — Local Mart">

                <!-- Leaflet renders into this div -->
                <div id="localMartMap"></div>

                <!-- Location status badge -->
                <div class="lm-location-badge" id="locationBadge">
                    <div class="pulse-dot"></div>
                    <span>Loading map…</span>
                </div>

                <!-- Map action buttons -->
                <div class="lm-map-controls">
                    <button class="lm-map-btn" id="btnResetView" title="Reset to Trichy centre">
                        <i class="bi bi-arrows-fullscreen"></i>
                    </button>
                    <button class="lm-map-btn" id="btnMyLocation" title="Go to my location">
                        <i class="bi bi-bullseye"></i>
                    </button>
                    <button class="lm-map-btn" id="btnCompass" title="Reset Rotation">
                        <i class="bi bi-compass"></i>
                    </button>
                    <button class="lm-map-btn" id="btnToggleCats" title="Toggle Categories">
                        <i class="bi bi-layers"></i>
                    </button>
                </div>

                <!-- Legend -->
                <div class="lm-map-legend">
                    <div class="lm-legend-item">
                        <span class="lm-legend-dot" style="background:#10B981;"></span> Open
                    </div>
                    <div class="lm-legend-item">
                        <span class="lm-legend-dot" style="background:#9CA3AF;"></span> Closed
                    </div>
                    <div class="lm-legend-item">
                        <span class="lm-legend-dot" style="background:#3B82F6;box-shadow:0 0 0 3px rgba(59,130,246,.25);"></span> You
                    </div>
                </div>

                <!-- OSM credit (supplementary) -->
                <div style="position:absolute;bottom:28px;left:8px;z-index:500;
                             background:rgba(255,255,255,.85);border-radius:6px;
                             padding:2px 8px;font-size:.68rem;color:#6B7280;">
                    Map data: <a href="https://www.openstreetmap.org" target="_blank" rel="noopener"
                                 style="color:#6C63FF;">OpenStreetMap</a>
                </div>

            </div>
        </div>

        <!-- ── RIGHT: SHOPS PANEL ───────────────────────── -->
        <div class="lm-shops-panel" id="shopsPanel">
            <div class="lm-panel-header d-flex align-items-center justify-content-between">
                <div>
                    <p class="lm-panel-title">Nearby Shops</p>
                    <p class="lm-panel-subtitle">Sorted by distance · Top 5 shown</p>
                </div>
                <span class="lm-count-badge" id="shopCountBadge">Loading…</span>
            </div>
        </div>

    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     QUICK CATEGORY SEARCH
     ══════════════════════════════════════════════════════════════ -->
<div class="container pb-4">
    <div class="lm-quick-search-bar">
        <h3 class="lm-qs-title">
            <i class="bi bi-lightning-charge-fill" style="color:#6C63FF;"></i>
            Quick Search by Category
        </h3>
        <div class="d-flex flex-wrap gap-2 mt-3">
            <?php
            $quick = [
                ['🥛','Milk','milk'],        ['🍞','Bread','bread'],
                ['🍚','Rice','rice'],         ['🥦','Vegetables','spinach'],
                ['🍎','Fruits','apple'],      ['💊','Medicine','paracetamol'],
                ['📱','Charger','charger'],   ['🥚','Eggs','eggs'],
                ['🧴','Handwash','handwash'], ['🧀','Dairy','paneer'],
                ['🔧','Hardware','screwdriver'],['🎧','Electronics','earbuds'],
            ];
            foreach ($quick as [$e, $l, $q]): ?>
            <button class="lm-pill lm-qs-pill"
                    onclick="window.LM.quickSearch('<?= htmlspecialchars($q) ?>')"
                    aria-label="Search <?= htmlspecialchars($l) ?>">
                <?= $e ?> <?= htmlspecialchars($l) ?>
            </button>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     HOW IT WORKS
     ══════════════════════════════════════════════════════════════ -->
<div class="container pb-5">
    <div class="lm-how-strip">
        <div class="lm-how-item">
            <div class="lm-how-icon"><i class="bi bi-geo-alt-fill"></i></div>
            <h6>Share Location</h6>
            <p>Allow location access or use Trichy centre</p>
        </div>
        <div class="lm-how-divider"></div>
        <div class="lm-how-item">
            <div class="lm-how-icon lm-how-icon--green"><i class="bi bi-map-fill"></i></div>
            <h6>View on Map</h6>
            <p>Shops appear as colour-coded pins on OSM</p>
        </div>
        <div class="lm-how-divider"></div>
        <div class="lm-how-item">
            <div class="lm-how-icon lm-how-icon--amber"><i class="bi bi-search"></i></div>
            <h6>Search Products</h6>
            <p>Find which nearby shop sells what you need</p>
        </div>
        <div class="lm-how-divider"></div>
        <div class="lm-how-item">
            <div class="lm-how-icon lm-how-icon--red"><i class="bi bi-navigation-fill"></i></div>
            <h6>Navigate &amp; Buy</h6>
            <p>Get OSM directions or add to cart instantly</p>
        </div>
    </div>
</div>

</div><!-- /.lm-page -->

<!-- ══════════════════════════════════════════════════════════════
     LEAFLET JS & ACTIVE PLUGINS
     ══════════════════════════════════════════════════════════════ -->
<!-- Core Leaflet -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

<!-- Leaflet MarkerCluster -->
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" crossorigin=""/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" crossorigin=""/>
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js" crossorigin=""></script>

<!-- Leaflet Routing Machine -->
<script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js" crossorigin=""></script>

<!-- Local Mart Engine -->
<script src="js/local-mart.js"></script>

<?php include 'includes/footer.php'; ?>
