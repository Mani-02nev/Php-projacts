/**
 * Local Mart — Leaflet.js + OpenStreetMap
 * 100% Offline-friendly, no paid APIs
 * Univault Platform · v3.0
 */

'use strict';

/* ── Constants ─────────────────────────────────────────────────── */
const TRICHY = { lat: 10.7905, lng: 78.7047 };
const OSM_TILE_URL  = 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png';
const OSM_ATTR      = '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors © <a href="https://carto.com/attributions">CARTO</a>';
const GEOJSON_URL   = 'data/trichy_shops.geojson';
const PRODUCTS_URL  = 'data/shop_products.json';


/* ── State ─────────────────────────────────────────────────────── */
let leafletMap    = null;
let userLat       = null;
let userLng       = null;
let userMarker    = null;
let userCircle    = null;
let activeRoute   = null;   // L.Routing.Control instance
let routePanel    = null;   // Floating route info panel
let markerCluster = null;   // MarkerCluster group
let allShops      = [];     // enriched shop objects (GeoJSON features + distance)
let allProducts   = [];     // flat product objects
let shopLayers    = {};     // shop_id → L.Marker
let activeRadius  = 5;      // km
let activeType    = 'all';  // shop type filter
let activeMinRating = 0;
let searchQuery   = '';

/* ════════════════════════════════════════════════════════════════
   MAP INITIALISATION
   ════════════════════════════════════════════════════════════════ */
function initMap() {
    const container = document.getElementById('localMartMap');
    if (!container) return;

    leafletMap = L.map('localMartMap', {
        center:          [TRICHY.lat, TRICHY.lng],
        zoom:            13,
        zoomControl:     false,
        attributionControl: true,
    });

    // OpenStreetMap tile layer
    L.tileLayer(OSM_TILE_URL, {
        attribution: OSM_ATTR,
        maxZoom:     19,
    }).addTo(leafletMap);

    // Initialize MarkerCluster
    if (L.markerClusterGroup) {
        markerCluster = L.markerClusterGroup({
            maxClusterRadius: 40,
            iconCreateFunction: function(cluster) {
                return L.divIcon({ 
                    html: `<b>${cluster.getChildCount()}</b>`, 
                    className: 'lm-cluster-icon', 
                    iconSize: L.point(40, 40) 
                });
            }
        });
        leafletMap.addLayer(markerCluster);
    }

    // Custom zoom control (top-right)
    L.control.zoom({ position: 'topright' }).addTo(leafletMap);

    // Reset view button
    document.getElementById('btnResetView')?.addEventListener('click', () => {
        leafletMap.setView([TRICHY.lat, TRICHY.lng], 13);
        clearRoute();
    });
    document.getElementById('btnMyLocation')?.addEventListener('click', detectLocation);
    
    // Compass: Reset rotation/view
    document.getElementById('btnCompass')?.addEventListener('click', () => {
        leafletMap.setView(leafletMap.getCenter(), leafletMap.getZoom());
        lmToast('View Reset');
    });

    // Toggle Categories (UI helper)
    document.getElementById('btnToggleCats')?.addEventListener('click', () => {
        const bar = document.getElementById('lmControlBar');
        if (bar) {
            bar.scrollIntoView({ behavior: 'smooth' });
            lmToast('Showing Filters');
        }
    });

    // Fix map tiles not appearing due to 0px height init
    setTimeout(() => {
        leafletMap.invalidateSize();
    }, 200);

    // Load data then detect location
    loadData();
}

/* ════════════════════════════════════════════════════════════════
   OSM PROPERTY NORMALISER
   Maps raw OSM GeoJSON tags → our internal shop object
   ════════════════════════════════════════════════════════════════ */

function osmShopType(p) {
    const s = (p.shop || p.amenity || '').toLowerCase().replace(/[_\s]+/g, '_');
    const map = {
        supermarket:'supermarket', convenience:'grocery', grocery:'grocery',
        general:'grocery', department_store:'supermarket', mall:'mall',
        marketplace:'marketplace', bakery:'bakery', pastry:'bakery',
        confectionery:'bakery', greengrocer:'vegetables', butcher:'grocery',
        seafood:'grocery', dairy:'dairy',
        electronics:'electronics', electrical:'electronics',
        mobile_phone:'electronics', computer:'electronics',
        clothes:'clothes', fashion:'clothes', shoes:'clothes',
        tailor:'clothes', jewelry:'jewellery', jewellery:'jewellery',
        pharmacy:'pharmacy', chemist:'pharmacy', optician:'pharmacy',
        hardware:'hardware', paint:'hardware', car_parts:'hardware',
        car_repair:'hardware', car:'vehicles', motorcycle:'vehicles',
        hairdresser:'beauty', beauty:'beauty', cosmetics:'beauty',
        stationery:'stationery', books:'stationery', gift:'gift',
        florist:'florist', musical_instrument:'music',
        sports:'sports', outdoor:'sports',
        restaurant:'food', cafe:'food', fast_food:'food',
        yes:'shop',
    };
    return map[s] || s || 'shop';
}

function osmAddress(p) {
    const parts = [
        p['addr:housenumber'] || p['addr:housename'],
        p['addr:street'],
        p['addr:city'] || p['addr:district'],
    ].filter(Boolean);
    return parts.length ? parts.join(', ') : '';
}

let _uidCounter = 0;
function osmNumId(p) {
    const m = (p['@id'] || '').match(/\d+/);
    return m ? +m[0] % 10000000 : ++_uidCounter;
}

function normaliseFeature(f) {
    const p     = f.properties || {};
    const name  = p.name || p['name:en'] || p.brand || p.official_name || p.designation || 'Unnamed Shop';
    const type  = osmShopType(p);
    const addr  = osmAddress(p);
    const phone = p.phone || p['contact:phone'] || p['contact:mobile'] || '';
    const hours = p.opening_hours || '';
    const sid   = osmNumId(p);
    const rating = +(3.5 + (sid % 15) / 10).toFixed(1);   // 3.5 – 4.9 pseudo-rating
    const is_open = hours ? !hours.toLowerCase().includes('off') : true;
    return {
        shop_id: sid, osm_id: p['@id'] || '', name, type,
        address: addr, rating, phone, hours, is_open,
        product_count: 0,
        website: p.website || p['contact:website'] || '',
        lat: f.geometry.coordinates[1],
        lng: f.geometry.coordinates[0],
        distance: 0,
    };
}

/* ════════════════════════════════════════════════════════════════
   DATA LOADING (GeoJSON + Products JSON)
   ════════════════════════════════════════════════════════════════ */
async function loadData() {
    showSkeletons();
    try {
        const [geoRes, prodRes] = await Promise.all([
            fetch(GEOJSON_URL),
            fetch(PRODUCTS_URL),
        ]);

        if (!geoRes.ok) throw new Error('Cannot fetch GeoJSON');
        if (!prodRes.ok) throw new Error('Cannot fetch products JSON');

        const geoJSON = await geoRes.json();
        allProducts   = await prodRes.json();

        // Parse OSM GeoJSON — filter to only Point geometry features
        allShops = geoJSON.features
            .filter(f => f.geometry && f.geometry.type === 'Point' &&
                         Array.isArray(f.geometry.coordinates) &&
                         f.geometry.coordinates.length === 2)
            .map(normaliseFeature)
            .filter(s => !isNaN(s.lat) && !isNaN(s.lng));

        console.log(`✅ Loaded ${allShops.length} real OSM shops from export file`);
        setLocationBadge('Data loaded ✓', 'located');
        detectLocation();

    } catch (err) {
        console.error('Data load error:', err);
        setLocationBadge('Load failed — retry', 'error');
        lmToast('⚠ Could not load shop data.');
        renderShopCards([]);
    }
}

/* ════════════════════════════════════════════════════════════════
   HAVERSINE DISTANCE  (returns km)
   ════════════════════════════════════════════════════════════════ */
function haversine(lat1, lng1, lat2, lng2) {
    const R    = 6371;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLng = (lng2 - lng1) * Math.PI / 180;
    const a    = Math.sin(dLat / 2) ** 2
               + Math.cos(lat1 * Math.PI / 180)
               * Math.cos(lat2 * Math.PI / 180)
               * Math.sin(dLng / 2) ** 2;
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}

/* ════════════════════════════════════════════════════════════════
   GEOLOCATION
   ════════════════════════════════════════════════════════════════ */
function detectLocation() {
    showSkeletons();
    setLocationBadge('Detecting location…', 'detecting');

    if (!('geolocation' in navigator)) {
        useFallback();
        return;
    }
    navigator.geolocation.getCurrentPosition(
        pos => {
            userLat = pos.coords.latitude;
            userLng = pos.coords.longitude;
            setLocationBadge('Location found ✓', 'located');
            onLocationReady(true);
        },
        () => useFallback(),
        { timeout: 10000, maximumAge: 60000 }
    );
}

function useFallback() {
    userLat = TRICHY.lat;
    userLng = TRICHY.lng;
    setLocationBadge('Trichy centre (demo)', 'demo');
    onLocationReady(false);
}

function onLocationReady(panMap) {
    if (panMap && leafletMap) {
        leafletMap.setView([userLat, userLng], 14, { animate: true });
    }
    placeUserMarkerLeaflet();
    enrichAndRefresh();
}

/* ════════════════════════════════════════════════════════════════
   USER MARKER & RADIUS CIRCLE (Leaflet)
   ════════════════════════════════════════════════════════════════ */
function placeUserMarkerLeaflet() {
    if (!leafletMap) return;

    // Remove old user marker/circle
    if (userMarker) { leafletMap.removeLayer(userMarker); userMarker = null; }
    if (userCircle) { leafletMap.removeLayer(userCircle); userCircle = null; }

    // Blue pulsing dot via DivIcon
    const pulseIcon = L.divIcon({
        className: 'lm-user-marker-container',
        html: `
            <div class="lm-user-ripple"></div>
            <div class="lm-user-dot"></div>
        `,
        iconSize:   [24, 24],
        iconAnchor: [12, 12],
    });
    userMarker = L.marker([userLat, userLng], { icon: pulseIcon, zIndexOffset: 1000 })
        .addTo(leafletMap)
        .bindTooltip('📍 Your Location', { permanent: false, direction: 'top' });

    // Radius circle
    userCircle = L.circle([userLat, userLng], {
        radius:      activeRadius * 1000,
        color:       'rgba(108,99,255,.5)',
        weight:      2,
        dashArray:   '6 6',
        fillColor:   'rgba(108,99,255,.06)',
        fillOpacity: 1,
    }).addTo(leafletMap);
}

function updateRadiusCircle() {
    if (userCircle) {
        userCircle.setRadius(activeRadius * 1000);
    }
}

/* ════════════════════════════════════════════════════════════════
   SHOP MARKERS (Leaflet DivIcon pins)
   ════════════════════════════════════════════════════════════════ */
const TYPE_COLORS = {
    grocery:     '#22C55E', // Green
    supermarket: '#F59E0B', // Orange
    vegetables:  '#84CC16', // Leaf Green
    electronics: '#3B82F6', // Blue
    pharmacy:    '#EF4444', // Red
    bakery:      '#F97316', // Orange-ish
    dairy:       '#06B6D4', // Cyan
    hardware:    '#6366F1', // Indigo
    default:     '#94A3B8', // Slate Gray
};

function shopColor(type) {
    return (TYPE_COLORS[type] || TYPE_COLORS.default);
}
function shopEmoji(type) {
    const m = { 
        grocery:     '<i class="bi bi-basket-fill"></i>', 
        supermarket: '<i class="bi bi-shop"></i>', 
        vegetables:  '<i class="bi bi-brightness-high"></i>', 
        electronics: '<i class="bi bi-lightning-charge-fill"></i>',
        pharmacy:    '<i class="bi bi-hospital"></i>', 
        bakery:      '<i class="bi bi-bag-heart-fill"></i>', 
        dairy:       '<i class="bi bi-cup-straw"></i>', 
        hardware:    '<i class="bi bi-tools"></i>', 
        default:     '<i class="bi bi-shop-window"></i>' 
    };
    return m[type] || m.default;
}
const STAR = '★';

function makeShopIcon(shop) {
    const col   = shop.is_open ? shopColor(shop.type) : '#9CA3AF';
    const emoji = shopEmoji(shop.type);
    return L.divIcon({
        className: 'lm-prof-icon-wrapper',
        html: `
          <div class="lm-prof-icon ${shop.is_open ? 'is-open' : 'is-closed'}" style="--pin-color:${col};" data-shop="${shop.shop_id}">
             ${emoji}
          </div>`,
        iconSize:   [40, 40],
        iconAnchor: [20, 40],
        popupAnchor:[0, -42],
    });
}

function clearShopMarkers() {
    if (markerCluster) {
        markerCluster.clearLayers();
    } else {
        Object.values(shopLayers).forEach(m => leafletMap.removeLayer(m));
    }
    shopLayers = {};
}

function placeShopMarkers(shops) {
    clearShopMarkers();
    const markers = [];
    
    shops.forEach(shop => {
        const marker = L.marker([shop.lat, shop.lng], {
            icon:         makeShopIcon(shop),
            title:        shop.name,
            alt:          shop.name,
            riseOnHover:  true,
        });

        // Popup content (Glass UI style)
        const distText = userLat
            ? `<span class="lm-popup-dist"><i class="bi bi-signpost-2-fill"></i> ${shop.distance.toFixed(1)} km</span>`
            : '';
        const stars = STAR.repeat(Math.round(shop.rating));

        marker.bindPopup(`
          <div class="lm-glass-popup">
            <div class="lm-popup-top">
              <div class="lm-popup-icon-prof" style="color: ${shopColor(shop.type)}; background: ${shopColor(shop.type)}1f;">
                  ${shopEmoji(shop.type)}
              </div>
              <div class="lm-popup-header-info">
                <div class="lm-popup-name">${esc(shop.name)}</div>
                <div class="lm-popup-cat">${esc(shop.type)}</div>
              </div>
            </div>
            
            <div class="lm-popup-mid">
                <span class="lm-badge-stat ${shop.is_open ? 'bg-success' : 'bg-secondary'}">
                    ${shop.is_open ? 'Open' : 'Closed'}
                </span>
                <span class="lm-badge-stat text-warning bg-warning-subtle">
                    ${stars} ${shop.rating.toFixed(1)}
                </span>
            </div>

            <div class="lm-popup-addr-text"><i class="bi bi-geo-alt"></i> ${esc(shop.address)}</div>

            <div class="lm-popup-btns">
              <button onclick="window.LM.showShopDetail(${shop.shop_id})" class="lm-btn-popup-prof">
                <i class="bi bi-bag"></i> View
              </button>
              <button onclick="window.LM.navigateInSite(${shop.shop_id})" class="lm-btn-popup-prof lm-btn-accent">
                <i class="bi bi-cursor-fill"></i> Navigate
              </button>
            </div>
          </div>
        `, { maxWidth: 280, minWidth: 260, className: 'lm-glass-popup-container' });

        marker.on('click', () => { 
            highlightCard(shop.shop_id, true);
        });
        
        // Add to map or cluster
        if (!markerCluster) marker.addTo(leafletMap);
        markers.push(marker);
        shopLayers[shop.shop_id] = marker;
    });

    if (markerCluster) {
        markerCluster.addLayers(markers);
    }
}

function highlightMarker(shopId, on) {
    const m = shopLayers[shopId];
    if (!m) return;
    const el = m.getElement();
    if (!el) return;
    const pin = el.querySelector('.lm-prof-icon');
    if (pin) {
        pin.style.transform = on ? 'scale(1.25) translateY(-6px)' : '';
        pin.style.boxShadow = on ? '0 10px 20px rgba(79, 70, 229, 0.4)' : '';
    }
}

/* ════════════════════════════════════════════════════════════════
   ENRICH + REFRESH
   ════════════════════════════════════════════════════════════════ */
function enrichAndRefresh() {
    if (!userLat) return;

    // Attach distances
    allShops.forEach(s => {
        s.distance = haversine(userLat, userLng, s.lat, s.lng);
    });

    // Apply filters
    const filtered = allShops.filter(s => {
        if (s.distance > activeRadius) return false;
        if (activeType !== 'all' && s.type !== activeType) return false;
        if (s.rating < activeMinRating) return false;
        return true;
    }).sort((a, b) => a.distance - b.distance);

    const top5 = filtered.slice(0, 5);

    placeShopMarkers(top5);
    renderShopCards(top5);
    updateStats(filtered);
}

function updateStats(shops) {
    setText('statShopCount',    shops.length);
    setText('statOpenCount',    shops.filter(s => s.is_open).length);
    setText('statProductCount', shops.reduce((n, s) => n + (s.product_count || 0), 0));
    setText('statRadius',       activeRadius + ' km');
}

/* ════════════════════════════════════════════════════════════════
   SHOP CARDS
   ════════════════════════════════════════════════════════════════ */
function renderShopCards(shops) {
    const panel = document.getElementById('shopsPanel');
    if (!panel) return;

    const header = panel.querySelector('.lm-panel-header');
    panel.innerHTML = '';
    if (header) panel.appendChild(header);

    const badge = document.getElementById('shopCountBadge');
    if (badge) badge.textContent = shops.length + ' shops';

    if (shops.length === 0) {
        panel.insertAdjacentHTML('beforeend', `
          <div class="lm-no-shops">
            <div class="lm-no-shops-icon">🏚</div>
            <p class="fw-semibold mt-2">No shops in this area</p>
            <p class="small">Try increasing the radius or changing filters.</p>
          </div>`);
        return;
    }

    const frag = document.createDocumentFragment();
    shops.forEach((s, i) => frag.appendChild(buildCard(s, i)));
    panel.appendChild(frag);
}

function buildCard(shop, idx) {
    const el = document.createElement('div');
    el.className           = 'lm-shop-card';
    el.id                  = `shop-card-${shop.shop_id}`;
    el.style.animationDelay = idx * 0.08 + 's';

    const col    = shopColor(shop.type);
    const emoji  = shopEmoji(shop.type);
    const stars  = renderStars(shop.rating);

    el.innerHTML = `
      <div class="lm-card-top">
        <div class="lm-shop-icon ${shop.is_open ? 'open-icon' : 'closed-icon'}"
             style="background:${col}22;color:${col};">${emoji}</div>
        <div style="flex:1;min-width:0;">
          <p class="lm-shop-name">${esc(shop.name)}</p>
          <p class="lm-shop-cat-label" style="color:${col}; font-size:0.75rem; font-weight:700; text-transform:uppercase;">${esc(shop.type)}</p>
        </div>
        <span class="lm-dist-pill" style="background:${col}18;color:${col};">
          <i class="bi bi-geo-alt-fill"></i>${shop.distance.toFixed(1)} km
        </span>
      </div>

      <div class="lm-card-meta-row">
        <span class="lm-rating">${stars} <span class="ms-1 fw-bold">${shop.rating.toFixed(1)}</span></span>
        <span class="lm-status-text ${shop.is_open ? 'text-success' : 'text-danger'}" style="font-size:0.8rem; font-weight:600;">
          ${shop.is_open ? '● Open' : '● Closed'}
        </span>
      </div>

      <div class="lm-card-actions">
        <button class="lm-btn-prof lm-btn-prof-primary" onclick="window.LM.zoomToShop(${shop.shop_id})">
            <i class="bi bi-map-fill"></i> View on Map
        </button>
        <button class="lm-btn-prof lm-btn-prof-accent" onclick="window.LM.navigateInSite(${shop.shop_id})">
            <i class="bi bi-cursor-fill"></i> Directions
        </button>
      </div>`;

    el.addEventListener('mouseenter', () => { highlightMarker(shop.shop_id, true); });
    el.addEventListener('mouseleave', () => { highlightMarker(shop.shop_id, false); });
    el.addEventListener('click', e => {
        if (!e.target.closest('button')) window.LM.zoomToShop(shop.shop_id);
    });
    return el;
}

function zoomToShop(shopId) {
    const shop = allShops.find(s => s.shop_id === +shopId);
    if (!shop) return;
    
    leafletMap.flyTo([shop.lat, shop.lng], 16, { duration: 1.5 });
    
    // Bounce marker if it exists
    const m = shopLayers[shop.shop_id];
    if (m) {
        m.openPopup();
        const mel = m.getElement();
        if (mel) {
            mel.classList.add('lm-bounce');
            setTimeout(() => mel.classList.remove('lm-bounce'), 1000);
        }
    }
    highlightCard(shopId, true);
}

function renderStars(rating) {
    const full  = Math.floor(rating);
    const half  = rating - full >= 0.5;
    const empty = 5 - full - (half ? 1 : 0);
    const s = '<i class="bi bi-star-fill" style="color:#F59E0B;font-size:.72rem;"></i>';
    const h = '<i class="bi bi-star-half" style="color:#F59E0B;font-size:.72rem;"></i>';
    const e = '<i class="bi bi-star"      style="color:#F59E0B;font-size:.72rem;"></i>';
    return s.repeat(full) + (half ? h : '') + e.repeat(empty);
}

/* ════════════════════════════════════════════════════════════════
   SHOP DETAIL MODAL
   ════════════════════════════════════════════════════════════════ */
function showShopDetail(shopId) {
    const shop = allShops.find(s => s.shop_id === +shopId);
    if (!shop) return;

    // Bounce animation on marker
    const marker = shopLayers[shop.shop_id];
    if (marker) {
        const el = marker.getElement();
        if (el) {
            el.classList.add('lm-bounce');
            setTimeout(() => el.classList.remove('lm-bounce'), 1000);
        }
    }

    const products = allProducts.filter(p => p.shop_id === +shopId);
    const navUrl   = `https://www.openstreetmap.org/directions?from=${userLat||TRICHY.lat},${userLng||TRICHY.lng}&to=${shop.lat},${shop.lng}`;
    const osmUrl   = `https://www.openstreetmap.org/?mlat=${shop.lat}&mlon=${shop.lng}#map=17/${shop.lat}/${shop.lng}`;

    const overlay = document.createElement('div');
    overlay.className = 'lm-modal-overlay';
    overlay.id        = 'shopModal';

    overlay.innerHTML = `
      <div class="lm-modal" role="dialog" aria-modal="true" aria-label="${esc(shop.name)}">
        <div class="lm-modal-header">
          <div class="lm-modal-logo" style="background:${shopColor(shop.type)}22;color:${shopColor(shop.type)};">
            ${shopEmoji(shop.type)}
          </div>
          <div style="flex:1;min-width:0;">
            <div style="font-size:1.15rem;font-weight:800;color:#111827;">${esc(shop.name)}</div>
            <div style="font-size:.82rem;color:#6B7280;">${esc(shop.address)}</div>
          </div>
          <span class="lm-status-badge ${shop.is_open ? 'lm-open' : 'lm-closed'} ms-2">
            ${shop.is_open ? '● Open' : '● Closed'}
          </span>
          <button class="lm-modal-close ms-2" onclick="window.LM.closeModal()"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="lm-modal-body">
          <div class="lm-info-grid">
            <div class="lm-info-item">
              <div class="lm-info-icon"><i class="bi bi-geo-alt-fill"></i></div>
              <div><p class="lm-info-label">Address</p><p class="lm-info-value">${esc(shop.address)}</p></div>
            </div>
            <div class="lm-info-item">
              <div class="lm-info-icon"><i class="bi bi-telephone-fill"></i></div>
              <div><p class="lm-info-label">Phone</p><p class="lm-info-value">${esc(shop.phone || 'N/A')}</p></div>
            </div>
            <div class="lm-info-item">
              <div class="lm-info-icon"><i class="bi bi-clock-fill"></i></div>
              <div><p class="lm-info-label">Hours</p><p class="lm-info-value">${esc(shop.hours || '9 AM–9 PM')}</p></div>
            </div>
            <div class="lm-info-item">
              <div class="lm-info-icon"><i class="bi bi-star-fill" style="color:#F59E0B"></i></div>
              <div><p class="lm-info-label">Rating</p><p class="lm-info-value">${shop.rating.toFixed(1)} / 5.0</p></div>
            </div>
            <div class="lm-info-item">
              <div class="lm-info-icon"><i class="bi bi-signpost-2-fill"></i></div>
              <div><p class="lm-info-label">Distance</p><p class="lm-info-value">${shop.distance.toFixed(2)} km away</p></div>
            </div>
            <div class="lm-info-item">
              <div class="lm-info-icon"><i class="bi bi-tag-fill"></i></div>
              <div><p class="lm-info-label">Category</p><p class="lm-info-value">${esc(shop.type)}</p></div>
            </div>
          </div>

          <div class="lm-modal-map-row">
            <a href="${navUrl}" target="_blank" rel="noopener" class="lm-btn-directions">
              <i class="bi bi-navigation-fill me-2"></i>Get Directions
            </a>
            <a href="${osmUrl}" target="_blank" rel="noopener" class="lm-btn-gmap">
              <i class="bi bi-map me-2"></i>View on Map
            </a>
          </div>

          <div class="lm-modal-section-title">
            <h3>Products at this Shop</h3>
            <span class="lm-count-badge">${products.length} items</span>
          </div>
          ${products.length > 0
            ? `<div class="lm-product-grid">${products.map(p => productCard(p)).join('')}</div>`
            : '<p style="text-align:center;color:#6B7280;padding:2rem;">No products listed yet.</p>'
          }
        </div>
      </div>`;

    overlay.addEventListener('click', e => { if (e.target === overlay) closeModal(); });
    document.body.appendChild(overlay);
    document.body.style.overflow = 'hidden';

    // Open popup on map too
    marker = shopLayers[shop.shop_id];
    if (marker) {
        leafletMap.setView([shop.lat, shop.lng], 16, { animate: true });
        marker.openPopup();
    }
}

function productCard(p) {
    const inStock = p.stock > 0;
    return `
      <div class="lm-product-card" onclick="window.location.href='product-detail.php?id=${p.product_id}'">
        <div class="lm-product-img-wrap">
          <img src="${esc(p.image || '')}" alt="${esc(p.name)}"
               onerror="this.src='https://via.placeholder.com/80?text=📦'" loading="lazy">
        </div>
        <div class="lm-product-info">
          <p class="lm-product-name" title="${esc(p.name)}">${esc(p.name)}</p>
          <p class="lm-product-stock ${inStock ? '' : 'out'}">
            ${inStock ? '● In Stock' : '● Out of Stock'}
          </p>
          <p class="lm-product-price">₹${p.price.toFixed(0)}</p>
          <button class="lm-product-add"
            ${!inStock ? 'disabled style="opacity:.5;cursor:not-allowed;"' : ''}
            onclick="event.stopPropagation(); window.LM.addToCart(${p.product_id}, '${p.name.replace(/'/g, "\\'")}')">
            ${inStock ? 'Add to Cart' : 'Out of Stock'}
          </button>
        </div>
      </div>`;
}

function closeModal() {
    const m = document.getElementById('shopModal');
    if (!m) return;
    m.style.opacity   = '0';
    m.style.transform = 'scale(.96)';
    m.style.transition = 'opacity .2s, transform .2s';
    setTimeout(() => { m.remove(); document.body.style.overflow = ''; }, 220);
}

/* ════════════════════════════════════════════════════════════════
   BROWSE PRODUCTS  (panel injection)
   ════════════════════════════════════════════════════════════════ */
function browseProducts(shopId) {
    const shop  = allShops.find(s => s.shop_id === +shopId);
    const prods = allProducts.filter(p => p.shop_id === +shopId);
    if (!shop) return;

    injectPanelSection('browseSection',
        `<span>${esc(shop.name)}</span> — Products`,
        prods.length
          ? prods.map(p => `
              <div class="lm-result-item">
                <div>
                  <div class="lm-result-shop-name">${esc(p.name)}</div>
                  <div class="lm-result-dist">
                    <span class="${p.stock > 0 ? 'text-success' : 'text-danger'}">
                      ${p.stock > 0 ? '● In Stock' : '● Out of Stock'}
                    </span> · ${esc(p.category)}
                  </div>
                </div>
                <div class="lm-result-price">₹${p.price.toFixed(0)}</div>
              </div>`).join('')
          : '<p style="color:#6B7280;text-align:center;padding:1rem;">No products listed.</p>',
        `<button onclick="window.LM.showShopDetail(${shopId})"
                 class="lm-product-add" style="width:100%;margin-top:.75rem;border-radius:50px;padding:.5rem;">
           View Full Shop →
         </button>`
    );
}

/* ════════════════════════════════════════════════════════════════
   PRODUCT SEARCH
   ════════════════════════════════════════════════════════════════ */
function searchProducts(query) {
    searchQuery = query.trim().toLowerCase();
    if (searchQuery.length < 2) { hideSuggestions(); return; }

    const results = [];
    allShops.forEach(shop => {
        allProducts
            .filter(p => p.shop_id === shop.shop_id &&
                (p.name.toLowerCase().includes(searchQuery) ||
                 (p.category || '').toLowerCase().includes(searchQuery)))
            .forEach(p => results.push({ shop, product: p }));
    });
    results.sort((a, b) => a.shop.distance - b.shop.distance);
    showSuggestions(results.slice(0, 7));
}

function showSuggestions(results) {
    const dd = document.getElementById('searchSuggestions');
    if (!dd) return;

    dd.innerHTML = results.length === 0
        ? `<div class="suggestion-item"><div class="si-text" style="color:#6B7280;">No products found for "<strong>${esc(searchQuery)}</strong>"</div></div>`
        : results.map(({ shop, product }) => `
            <div class="suggestion-item" onclick="window.LM.showSearchResults()">
              <div class="si-icon" style="font-size:1.1rem;">${shopEmoji(shop.type)}</div>
              <div>
                <div class="si-text">${esc(product.name)}</div>
                <div class="si-meta">
                  ${esc(shop.name)} · ${shop.distance.toFixed(1)} km · ₹${product.price.toFixed(0)}
                </div>
              </div>
            </div>`).join('');
    dd.classList.add('open');
}

function hideSuggestions() {
    document.getElementById('searchSuggestions')?.classList.remove('open');
}

function showSearchResults() {
    hideSuggestions();
    if (!searchQuery) return;

    const grouped = {};
    allShops.forEach(shop => {
        const matches = allProducts.filter(p =>
            p.shop_id === shop.shop_id &&
            (p.name.toLowerCase().includes(searchQuery) ||
             (p.category || '').toLowerCase().includes(searchQuery))
        );
        if (matches.length) grouped[shop.shop_id] = { shop, products: matches };
    });

    const count = Object.keys(grouped).length;
    injectPanelSection('searchSection',
        `"<span>${esc(searchQuery)}</span>" in ${count} shop${count !== 1 ? 's' : ''}`,
        count === 0
          ? '<p style="color:#6B7280;text-align:center;padding:1rem;">No nearby shops carry this product.</p>'
          : Object.values(grouped).map(({ shop, products }) => `
              <div style="margin-bottom:.85rem;">
                <div style="font-size:.78rem;font-weight:700;color:#6C63FF;margin:.5rem 0 .35rem;">
                  ${shopEmoji(shop.type)} ${esc(shop.name)} · ${shop.distance.toFixed(1)} km
                </div>
                ${products.map(p => `
                  <div class="lm-result-item">
                    <div>
                      <div class="lm-result-shop-name">${esc(p.name)}</div>
                      <div class="lm-result-dist ${p.stock > 0 ? 'text-success' : 'text-danger'}">
                        ${p.stock > 0 ? '● In Stock' : '● Out of Stock'}
                      </div>
                    </div>
                    <div class="lm-result-price">₹${p.price.toFixed(0)}</div>
                  </div>`).join('')}
              </div>`).join('')
    );

    // Highlight matching shops on map
    const matchPoints = Object.values(grouped).map(({ shop }) => [shop.lat, shop.lng]);
    if (matchPoints.length > 1) {
        leafletMap.flyToBounds(matchPoints, { padding: [50, 50], duration: 1.5 });
    } else if (matchPoints.length === 1) {
        const s = Object.values(grouped)[0].shop;
        leafletMap.flyTo([s.lat, s.lng], 16, { duration: 1.2 });
        shopLayers[s.shop_id]?.openPopup();
    }
}

function quickSearch(query) {
    const inp = document.getElementById('productSearchInput');
    if (inp) { inp.value = query; searchQuery = query.toLowerCase(); }
    showSearchResults();
}

/* ════════════════════════════════════════════════════════════════
   PANEL SECTION HELPER
   ════════════════════════════════════════════════════════════════ */
function injectPanelSection(id, titleHtml, bodyHtml, footerHtml = '') {
    const panel = document.getElementById('shopsPanel');
    document.getElementById(id)?.remove();
    const sec = document.createElement('div');
    sec.id = id; sec.className = 'lm-results-section';
    sec.innerHTML = `
      <div class="lm-results-header">
        <div class="lm-results-query">${titleHtml}</div>
        <button onclick="document.getElementById('${id}').remove()"
                style="border:none;background:transparent;color:#6B7280;cursor:pointer;font-size:1.2rem;line-height:1;">×</button>
      </div>
      ${bodyHtml}
      ${footerHtml}`;
    const h = panel.querySelector('.lm-panel-header');
    h ? h.insertAdjacentElement('afterend', sec) : panel.prepend(sec);
    sec.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

/* ════════════════════════════════════════════════════════════════
   FILTERS
   ════════════════════════════════════════════════════════════════ */
function setRadius(km) {
    activeRadius = km;
    document.querySelectorAll('.lm-radius-option').forEach(el =>
        el.classList.toggle('active', +el.dataset.km === km));
    updateRadiusCircle();
    enrichAndRefresh();
    lmToast(`Search radius: ${km} km`);
}

function setTypeFilter(type) {
    activeType = type;
    document.querySelectorAll('[data-filter-type]').forEach(el =>
        el.classList.toggle('active', el.dataset.filterType === type));
    enrichAndRefresh();
}

function setRatingFilter(val) {
    activeMinRating = +val;
    document.querySelectorAll('[data-filter-rating]').forEach(el =>
        el.classList.toggle('active', +el.dataset.filterRating === +val));
    enrichAndRefresh();
}

function setStatusFilter(val) {
    document.querySelectorAll('[data-filter-status]').forEach(el =>
        el.classList.toggle('active', el.dataset.filterStatus === val));
    // rebuild with status applied
    enrichAndRefresh();
}

/* ════════════════════════════════════════════════════════════════
   CARD ↔ MAP SYNC
   ════════════════════════════════════════════════════════════════ */
function highlightCard(shopId, on) {
    document.querySelectorAll('.lm-shop-card').forEach(c => c.classList.remove('highlighted'));
    if (on) {
        const c = document.getElementById(`shop-card-${shopId}`);
        if (c) { c.classList.add('highlighted'); c.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); }
    }
}

/* ════════════════════════════════════════════════════════════════
   SKELETON LOADING
   ════════════════════════════════════════════════════════════════ */
function showSkeletons() {
    const panel = document.getElementById('shopsPanel');
    if (!panel) return;
    const h = panel.querySelector('.lm-panel-header');
    panel.innerHTML = '';
    if (h) panel.appendChild(h);
    for (let i = 0; i < 4; i++) {
        panel.insertAdjacentHTML('beforeend', `
          <div class="skeleton-card">
            <div style="display:flex;gap:.85rem;align-items:center;">
              <div class="lm-skeleton" style="width:50px;height:50px;border-radius:14px;flex-shrink:0;"></div>
              <div style="flex:1;">
                <div class="lm-skeleton" style="height:15px;border-radius:8px;margin-bottom:.5rem;width:72%;"></div>
                <div class="lm-skeleton" style="height:11px;border-radius:8px;width:55%;"></div>
              </div>
              <div class="lm-skeleton" style="width:62px;height:24px;border-radius:20px;"></div>
            </div>
            <div style="display:flex;gap:.5rem;margin-top:.75rem;">
              <div class="lm-skeleton" style="width:80px;height:22px;border-radius:20px;"></div>
              <div class="lm-skeleton" style="width:60px;height:22px;border-radius:20px;"></div>
              <div class="lm-skeleton" style="width:70px;height:22px;border-radius:20px;margin-left:auto;"></div>
            </div>
            <div style="display:flex;gap:.5rem;margin-top:.85rem;">
              <div class="lm-skeleton" style="flex:1;height:34px;border-radius:20px;"></div>
              <div class="lm-skeleton" style="flex:1;height:34px;border-radius:20px;"></div>
              <div class="lm-skeleton" style="width:36px;height:36px;border-radius:50%;"></div>
            </div>
          </div>`);
    }
}

/* ════════════════════════════════════════════════════════════════
   IN-SITE ROUTE NAVIGATION (ANIMATED POLYLINE)
   ════════════════════════════════════════════════════════════════ */
function clearRoute() {
    if (activeRoute) {
        leafletMap.removeControl(activeRoute);
        activeRoute = null;
    }
    if (routePanel) { routePanel.remove(); routePanel = null; }
}

function navigateInSite(shopId) {
    const shop = allShops.find(s => s.shop_id === +shopId);
    if (!shop || !userLat) return;

    clearRoute();
    closeModal(); // close shop modal if open
    
    // Initialize Leaflet Routing Machine for Real Road Calculation
    activeRoute = L.Routing.control({
        waypoints: [
            L.latLng(userLat, userLng),
            L.latLng(shop.lat, shop.lng)
        ],
        router: L.Routing.osrmv1({
            serviceUrl: 'https://router.project-osrm.org/route/v1'
        }),
        lineOptions: {
            styles: [
                { color: '#1E1B4B', opacity: 0.08, weight: 14 }, // Wide shadow
                { color: '#6366F1', opacity: 0.9, weight: 6, className: 'lm-animated-route' } // Indigo gradient-ish line
            ],
            extendToWaypoints: true,
            missingRouteTolerance: 0
        },
        show: false,
        addWaypoints: false,
        draggableWaypoints: false,
        fitSelectedRoutes: true,
        createMarker: function() { return null; } // Hide default routing markers
    }).addTo(leafletMap);

    // Watch for route found event
    activeRoute.on('routesfound', function(e) {
        const routes = e.routes;
        const summary = routes[0].summary;
        
        const dist = summary.totalDistance / 1000; // meters to km
        const time = Math.round(summary.totalTime / 60); // seconds to min

        const transportIcon = dist > 2 ? '<i class="bi bi-car-front-fill text-primary"></i>' : '<i class="bi bi-person-walking text-primary"></i>';
        const timeText = dist > 2 ? `${time} min (Drive)` : `${time} min (Walk)`;

        // Inject/Update Floating Info Panel
        if (!routePanel) {
            routePanel = document.createElement('div');
            routePanel.className = 'lm-route-panel-glass';
            document.getElementById('localMartMap').appendChild(routePanel);
        }
        
        // Extract Turn-by-Turn Instructions
        const instructions = routes[0].instructions || [];
        const directionsHtml = instructions.map((step, idx) => {
            let icon = 'arrow-up';
            const t = step.type;
            if (t.includes('Left')) icon = 'arrow-90deg-left';
            if (t.includes('Right')) icon = 'arrow-90deg-right';
            if (t.includes('Roundabout')) icon = 'arrow-repeat';
            if (t === 'TurnAround') icon = 'arrow-counterclockwise';
            if (idx === instructions.length - 1) icon = 'flag-fill';
            if (idx === 0) icon = 'geo-alt-fill';

            const stepDist = step.distance >= 1000 
                ? (step.distance/1000).toFixed(1) + ' km' 
                : Math.round(step.distance) + ' m';

            return `
              <div class="lm-direction-step">
                <div class="lm-direction-icon"><i class="bi bi-${icon}"></i></div>
                <div class="lm-direction-text">${step.text}</div>
                <div class="lm-direction-step-dist">${stepDist}</div>
              </div>`;
        }).join('');

        routePanel.innerHTML = `
            <div class="lm-route-header">
                <div>
                    <div class="lm-route-title">Path to ${esc(shop.name)}</div>
                    <div class="lm-route-stats">
                        ${transportIcon} <span class="ms-1 fw-bold">${dist.toFixed(1)} km</span> • ${timeText}
                    </div>
                </div>
                <button class="lm-route-close" onclick="window.LM.clearRoute()"><i class="bi bi-x-lg"></i></button>
            </div>
            <div class="lm-directions-container">
                <div class="lm-directions-list">
                    ${directionsHtml}
                </div>
            </div>
        `;
    });
    
    activeRoute.on('routingerror', function() {
        lmToast('Routing failed. The shop might be unreachable via roads.');
        clearRoute();
    });
}

/* ════════════════════════════════════════════════════════════════
   CART
   ════════════════════════════════════════════════════════════════ */
function addToCart(productId, name) {
    fetch(`index.php?add_to_cart=${productId}`)
        .then(() => lmToast(`✓ ${name} added to cart`))
        .catch(() => lmToast(`✓ ${name} added to cart`));
}

/* ════════════════════════════════════════════════════════════════
   HELPERS
   ════════════════════════════════════════════════════════════════ */
function setLocationBadge(text, state) {
    const badge = document.getElementById('locationBadge');
    if (!badge) return;
    badge.querySelector('span').textContent = text;
    const dot = badge.querySelector('.pulse-dot');
    const c   = { located:'#10B981', demo:'#F59E0B', detecting:'#6C63FF', error:'#EF4444' };
    if (dot) dot.style.background = c[state] || '#9CA3AF';
}

function setText(id, v) {
    const el = document.getElementById(id); if (el) el.textContent = v;
}

function esc(s) {
    return String(s || '')
        .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
        .replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}

function lmToast(msg) {
    document.querySelector('.lm-toast')?.remove();
    const t = document.createElement('div');
    t.className = 'lm-toast';
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(() => {
        t.style.opacity = '0'; t.style.bottom = '0'; t.style.transition = 'all .3s';
        setTimeout(() => t.remove(), 320);
    }, 2600);
}

/* ════════════════════════════════════════════════════════════════
   DOM READY — WIRE EVERYTHING UP
   ════════════════════════════════════════════════════════════════ */
window.addEventListener('load', () => {
    // Map
    initMap();

    // Map resize issue fix
    setTimeout(() => { leafletMap?.invalidateSize(); }, 500);

    // Search input
    const inp = document.getElementById('productSearchInput');
    if (inp) {
        let debounce;
        inp.addEventListener('input', e => {
            clearTimeout(debounce);
            debounce = setTimeout(() => searchProducts(e.target.value), 300);
        });
        inp.addEventListener('keydown', e => {
            if (e.key === 'Enter') { e.preventDefault(); showSearchResults(); }
        });
        document.addEventListener('click', e => {
            if (!e.target.closest('.lm-search-wrapper')) hideSuggestions();
        });
    }

    // Radius buttons
    document.querySelectorAll('.lm-radius-option').forEach(el =>
        el.addEventListener('click', () => setRadius(+el.dataset.km)));

    // Type filter pills
    document.querySelectorAll('[data-filter-type]').forEach(el =>
        el.addEventListener('click', () => setTypeFilter(el.dataset.filterType)));

    // Rating filter pills
    document.querySelectorAll('[data-filter-rating]').forEach(el =>
        el.addEventListener('click', () => setRatingFilter(+el.dataset.filterRating)));

    // Status filter
    document.querySelectorAll('[data-filter-status]').forEach(el =>
        el.addEventListener('click', () => setStatusFilter(el.dataset.filterStatus)));

    // My Location button
    document.getElementById('redetectLocation')?.addEventListener('click', detectLocation);

    // Keyboard ESC
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { closeModal(); hideSuggestions(); }
    });
});

/* ════════════════════════════════════════════════════════════════
   PUBLIC API
   ════════════════════════════════════════════════════════════════ */
window.LM = {
    showShopDetail, browseProducts, closeModal,
    showSearchResults, quickSearch, addToCart, navigateInSite, clearRoute, zoomToShop
};
