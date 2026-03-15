/**
 * Local Mart — Leaflet.js
 * 100% Offline-friendly, no paid APIs
 * Univault Platform · v3.0
 */

'use strict';

/* ── Constants ─────────────────────────────────────────────────── */
const TRICHY = { lat: 10.7905, lng: 78.7047 };
const OSM_TILE_URL  = 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png';
const OSM_ATTR      = '';
const GEOJSON_URL   = 'data/50k.geojson';
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
let activeRadius  = 50;      // km
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
        attributionControl: false,
    });

    // Map tile layer
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

    // Custom zoom control logic
    document.getElementById('btnZoomIn')?.addEventListener('click', () => leafletMap.zoomIn());
    document.getElementById('btnZoomOut')?.addEventListener('click', () => leafletMap.zoomOut());

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
    if (p['addr:full']) return p['addr:full'];
    const parts = [
        p['addr:housenumber'] || p['addr:housename'],
        p['addr:street'] || p['addr:locality'],
        p['addr:city'] || p['addr:district'] || p['addr:suburb'] || p['addr:state']
    ].filter(Boolean);
    return parts.length ? parts.join(', ') : 'Trichy District Region';
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
        const uniqueNames = new Set();
        allShops = geoJSON.features
            .filter(f => f.geometry && f.geometry.type === 'Point' &&
                         Array.isArray(f.geometry.coordinates) &&
                         f.geometry.coordinates.length === 2)
            .map(normaliseFeature)
            .filter(s => {
                if (isNaN(s.lat) || isNaN(s.lng)) return false;
                
                // Skip unnamed shops for a cleaner UI
                if (s.name === 'Unnamed Shop') return false; 

                // OSM often yields nodes + ways for the same building. 
                // Group by name and roughly 110m box (toFixed(3)) to deduplicate the same store
                const latBin = s.lat.toFixed(3);
                const lngBin = s.lng.toFixed(3);
                const normalizedKey = `${s.name.trim().toLowerCase()}_${latBin}_${lngBin}`;
                
                if (uniqueNames.has(normalizedKey)) return false;
                uniqueNames.add(normalizedKey);
                return true;
            });

        console.log(`✅ Loaded ${allShops.length} unique OSM shops from export file`);
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
        pharmacy:    '<i class="bi bi-capsule"></i>',
        bakery:      '<i class="bi bi-bread-slice"></i>',
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
                ${distText}
            </div>

            <div class="lm-popup-addr-text"><i class="bi bi-geo-alt"></i> ${esc(shop.address)}</div>

            <div class="lm-popup-btns" style="margin-top:0.75rem; display:flex; gap:0.5rem;">
              <button onclick="window.LM.showShopDetail(${shop.shop_id})" class="lm-btn-popup-prof">
                <i class="bi bi-shop"></i> View shop
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

    const toShow = filtered;

    placeShopMarkers(toShow);
    renderShopCards(toShow);
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

function categoryLabel(type) {
    const labels = {
        grocery: 'Grocery Store',
        supermarket: 'Supermarket',
        vegetables: 'Vegetables',
        electronics: 'Electronics',
        pharmacy: 'Pharmacy',
        bakery: 'Bakery',
        dairy: 'Dairy',
        hardware: 'Auto Repair',
        default: type
    };
    return labels[type] || (type.charAt(0).toUpperCase() + type.slice(1).replace(/_/g, ' '));
}

function buildCard(shop, idx) {
    const el = document.createElement('div');
    el.className           = 'lm-shop-card';
    el.id                  = `shop-card-${shop.shop_id}`;
    el.style.animationDelay = idx * 0.08 + 's';

    const col    = shopColor(shop.type);
    const emoji  = shopEmoji(shop.type);
    const stars  = renderStars(shop.rating);
    const catLabel = categoryLabel(shop.type);

    el.innerHTML = `
      <div class="lm-card-top">
        <div class="lm-shop-icon ${shop.is_open ? 'open-icon' : 'closed-icon'}"
             style="background:${col}22;color:${col};">${emoji}</div>
        <div style="flex:1;min-width:0;">
          <p class="lm-shop-name" title="${esc(shop.name)}">${esc(shop.name)}</p>
          <p class="lm-shop-cat-label">${esc(catLabel)}</p>
        </div>
        <span class="lm-dist-pill">${shop.distance.toFixed(1)} km</span>
      </div>

      <div class="lm-card-meta">
        <span class="lm-rating">${stars} ${shop.rating.toFixed(1)}</span>
        <span class="lm-status-badge ${shop.is_open ? 'lm-open' : 'lm-closed'}">
          ${shop.is_open ? 'Open' : 'Closed'}
        </span>
      </div>

      <div class="lm-card-actions">
        <button type="button" class="lm-btn-view" onclick="event.stopPropagation(); window.LM.zoomToShop(${shop.shop_id})">
          <i class="bi bi-map-fill"></i> View on Map
        </button>
        <button type="button" class="lm-btn-browse" onclick="event.stopPropagation(); window.LM.navigateInSite(${shop.shop_id})">
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

    // Highlight the card in the list and scroll it into view
    highlightCard(shopId, true);

    // 1 & 2 & 3. Pan the map to the shop location and Zoom the map to level 15
    leafletMap.flyTo([shop.lat, shop.lng], 15, { duration: 1.5 });

    // 4 & 5. Find the shop marker, highlight it, open popup (and animate)
    const m = shopLayers[shop.shop_id];
    if (m) {
        const el = m.getElement();
        const pin = el?.querySelector('.lm-prof-icon');
        if (pin) {
            pin.classList.add('lm-bounce');
            setTimeout(() => pin.classList.remove('lm-bounce'), 1000);
        }
        if (markerCluster) {
            markerCluster.zoomToShowLayer(m, () => {
                m.openPopup();
                highlightMarker(shop.shop_id, true);
            });
        } else {
            m.openPopup();
            highlightMarker(shop.shop_id, true);
        }
    }
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
            <button onclick="window.LM.navigateInSite(${shop.shop_id})" class="lm-btn-directions" style="border:none; cursor:pointer;">
              <i class="bi bi-navigation-fill me-2"></i>Get Directions
            </button>
            <button onclick="window.LM.closeModal(); window.LM.zoomToShop(${shop.shop_id})" class="lm-btn-gmap" style="border:none; cursor:pointer;">
              <i class="bi bi-map me-2"></i>View on Map
            </button>
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
   GOOGLE MAPS-STYLE NAVIGATION SYSTEM
   ════════════════════════════════════════════════════════════════ */
let activeNavShop = null;           // currently navigating shop
let navTravelMode = 'driving';      // selected mode: driving | cycling | foot
let routeSummaryCard = null;       // floating map card (legacy, may be unused)
let routeSelectorBar = null;       // horizontal route mode selector above map
let routesByMode = null;          // { driving: { route, latlngs, distance, duration, steps }, cycling, foot }
let routeLayers = {};             // { drive: L.Polyline, bike: L.Polyline, walk: L.Polyline }
let polylineGlow = null;          // glow for selected route only

function clearRoute() {
    // Remove Leaflet Routing Control if present
    if (activeRoute) {
        try { leafletMap.removeControl(activeRoute); } catch (e) {}
        activeRoute = null;
    }
    // Remove all route polylines (Drive, Bike, Walk)
    if (polylineGlow) { leafletMap.removeLayer(polylineGlow); polylineGlow = null; }
    ['drive', 'bike', 'walk'].forEach(key => {
        if (routeLayers[key]) { leafletMap.removeLayer(routeLayers[key]); routeLayers[key] = null; }
    });
    routeLayers = {};
    routesByMode = null;

    // Remove route selector bar and floating summary card
    if (routeSelectorBar) { routeSelectorBar.remove(); routeSelectorBar = null; }
    if (routeSummaryCard) { routeSummaryCard.remove(); routeSummaryCard = null; }

    // Remove route sidebar and direction steps
    if (routePanel) { routePanel.remove(); routePanel = null; }
    activeNavShop = null;

    // De-highlight destination shop marker
    Object.values(shopLayers).forEach(m => {
        const el = m.getElement();
        const pin = el?.querySelector('.lm-prof-icon');
        if (pin) pin.classList.remove('selected');
    });

    // Reset map to default shop discovery view (city view)
    if (leafletMap) {
        leafletMap.flyTo([TRICHY.lat, TRICHY.lng], 13, { duration: 0.8 });
    }

    const area = document.getElementById('lmContentArea');
    if (area) area.classList.remove('routing-active');

    setTimeout(() => { if (leafletMap) leafletMap.invalidateSize({ animate: true }); }, 300);
}

function navigateInSite(shopId) {
    const shop = allShops.find(s => s.shop_id === +shopId);
    if (!shop) { lmToast('Shop not found.'); return; }
    if (!userLat) { lmToast('Please allow location access first.'); detectLocation(); return; }

    activeNavShop = shop;
    clearRoute();
    closeModal();

    // Highlight the destination shop marker
    const targetMarker = shopLayers[shop.shop_id];
    if (targetMarker) {
        const el = targetMarker.getElement();
        const pin = el?.querySelector('.lm-prof-icon');
        if (pin) pin.classList.add('selected');
    }

    // Set up sidebar (timeline panel)
    const area = document.getElementById('lmContentArea');
    if (area) area.classList.add('routing-active');

    routePanel = document.createElement('div');
    routePanel.className = 'lm-route-sidebar';
    routePanel.id = 'routeSidebar';
    if (area) area.insertBefore(routePanel, area.firstChild);

    routePanel.innerHTML = buildNavPanelSkeleton(shop);
    const body = document.getElementById('navBody');
    if (body) body.innerHTML = '<div class="lm-nav-loading"><div class="lm-nav-spinner"></div><span>Calculating routes…</span></div>';

    // Fetch all three routes in parallel
    fetchAllRoutes(shop).then(routes => {
        if (!routes) return;
        routesByMode = routes;
        if (!routesByMode[navTravelMode]) {
            navTravelMode = routesByMode.driving ? 'driving' : routesByMode.cycling ? 'cycling' : 'foot';
        }
        renderAllRoutes(shop, routes);
        selectRouteMode(shop, navTravelMode);
    }).catch(err => {
        console.error('Route fetch error:', err);
        if (body) body.innerHTML = '<div class="lm-nav-error"><i class="bi bi-exclamation-triangle-fill"></i><p>Could not calculate routes. Try again.</p></div>';
    });

    setTimeout(() => { if (leafletMap) leafletMap.invalidateSize({ animate: true }); }, 300);
}

function buildNavPanelSkeleton(shop) {
    const col   = shopColor(shop.type);
    const emoji = shopEmoji(shop.type);
    return `
    <div class="lm-nav-header">
        <div class="lm-nav-title-row">
            <div class="lm-nav-icon" style="background:${col}22;color:${col};">${emoji}</div>
            <div style="flex:1;min-width:0;">
                <div class="lm-nav-shop-name">${esc(shop.name)}</div>
                <div class="lm-nav-shop-type">${esc(shop.type)}</div>
            </div>
            <button class="lm-nav-close" onclick="window.LM.clearRoute()" title="Cancel route">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="lm-nav-waypoints">
            <div class="lm-nav-wp-row">
                <div class="lm-nav-wp-dot lm-wp-origin"></div>
                <span class="lm-nav-wp-label">Your Location</span>
            </div>
            <div class="lm-nav-wp-line"></div>
            <div class="lm-nav-wp-row">
                <div class="lm-nav-wp-dot lm-wp-dest" style="background:${col};"></div>
                <span class="lm-nav-wp-label" style="font-weight:600;">${esc(shop.name)}</span>
            </div>
        </div>
    </div>
    <div class="lm-nav-body" id="navBody"></div>`;
}

/** Average speeds (km/h) for route duration calculation. */
const ROUTE_SPEED_KMH = { driving: 40, cycling: 25, foot: 5 };

/**
 * Compute travel duration in seconds from route distance using speed profile.
 * time (h) = distance (km) / speed (km/h)  →  duration (s) = (distance_m / 1000 / speed_kmh) * 3600
 */
function durationFromDistance(distanceMeters, mode) {
    if (!distanceMeters || !ROUTE_SPEED_KMH[mode]) return 0;
    const km = distanceMeters / 1000;
    const hours = km / ROUTE_SPEED_KMH[mode];
    return Math.round(hours * 3600);
}

function formatDuration(seconds) {
    const mins = Math.round(seconds / 60);
    if (mins < 60) return mins + ' min';
    const h = Math.floor(mins / 60);
    const m = mins % 60;
    return m ? `${h}h ${m}m` : `${h}h`;
}

/** Fetch Drive, Bike, Walk routes in parallel from OSRM. */
async function fetchAllRoutes(shop) {
    const coords = `${userLng},${userLat};${shop.lng},${shop.lat}`;
    const base = 'https://router.project-osrm.org/route/v1';
    const opts = '?overview=full&geometries=geojson&steps=true';
    const urls = {
        driving: `${base}/driving/${coords}${opts}`,
        cycling: `${base}/cycling/${coords}${opts}`,
        foot:    `${base}/foot/${coords}${opts}`
    };
    const results = await Promise.allSettled([
        fetch(urls.driving).then(r => r.ok ? r.json() : Promise.reject(new Error('driving'))),
        fetch(urls.cycling).then(r => r.ok ? r.json() : Promise.reject(new Error('cycling'))),
        fetch(urls.foot).then(r => r.ok ? r.json() : Promise.reject(new Error('foot')))
    ]);
    const driving = results[0].status === 'fulfilled' && results[0].value?.routes?.[0] ? results[0].value.routes[0] : null;
    const cycling = results[1].status === 'fulfilled' && results[1].value?.routes?.[0] ? results[1].value.routes[0] : null;
    const foot    = results[2].status === 'fulfilled' && results[2].value?.routes?.[0] ? results[2].value.routes[0] : null;
    if (!driving && !cycling && !foot) return null;
    return {
        driving: driving ? { route: driving, latlngs: driving.geometry.coordinates.map(c => [c[1], c[0]]), distance: driving.distance, duration: driving.duration, steps: driving.legs?.flatMap(l => l.steps) || [] } : null,
        cycling: cycling ? { route: cycling, latlngs: cycling.geometry.coordinates.map(c => [c[1], c[0]]), distance: cycling.distance, duration: cycling.duration, steps: cycling.legs?.flatMap(l => l.steps) || [] } : null,
        foot:    foot    ? { route: foot,    latlngs: foot.geometry.coordinates.map(c => [c[1], c[0]]), distance: foot.distance, duration: foot.duration, steps: foot.legs?.flatMap(l => l.steps) || [] } : null
    };
}

/** Draw all three routes on the map and create the horizontal route selector bar. */
function renderAllRoutes(shop, routes) {
    if (!leafletMap || !routes) return;
    // Remove any existing route layers
    if (polylineGlow) { leafletMap.removeLayer(polylineGlow); polylineGlow = null; }
    ['drive', 'bike', 'walk'].forEach(key => { if (routeLayers[key]) { leafletMap.removeLayer(routeLayers[key]); routeLayers[key] = null; } });
    routeLayers = {};

    const ROUTE_STYLES = {
        driving: { key: 'drive', color: '#2563EB', weight: 5, opacity: 0.5, dashArray: null, className: 'lm-route-drive' },
        cycling: { key: 'bike', color: '#7c3aed', weight: 5, opacity: 0.5, dashArray: '15, 10', className: 'lm-route-bike' },
        foot:    { key: 'walk', color: '#059669', weight: 5, opacity: 0.5, dashArray: '5, 15', className: 'lm-route-walk' }
    };

    const allLatlngs = [];
    ['driving', 'cycling', 'foot'].forEach(mode => {
        const data = routes[mode];
        if (!data) return;
        const style = ROUTE_STYLES[mode];
        const layer = L.polyline(data.latlngs, {
            color: style.color,
            weight: style.weight,
            opacity: style.opacity,
            dashArray: style.dashArray || undefined,
            lineCap: 'round',
            lineJoin: 'round',
            className: style.className
        }).addTo(leafletMap);
        routeLayers[style.key] = layer;
        allLatlngs.push(...data.latlngs);
    });

    if (allLatlngs.length) {
        leafletMap.fitBounds(L.latLngBounds(allLatlngs), { padding: [80, 80], animate: true });
    }

    // Route selector bar above the map
    const mapCont = document.querySelector('.lm-map-container');
    if (routeSelectorBar) routeSelectorBar.remove();
    routeSelectorBar = document.createElement('div');
    routeSelectorBar.className = 'lm-route-selector-bar';
    routeSelectorBar.id = 'routeSelectorBar';
    const driveData = routes.driving;
    const bikeData = routes.cycling;
    const walkData = routes.foot;
    // Duration from speed profile: time = distance / speed (Drive 40, Bike 25, Walk 5 km/h)
    const driveTime = driveData ? formatDuration(durationFromDistance(driveData.distance, 'driving')) : '—';
    const bikeTime = bikeData ? formatDuration(durationFromDistance(bikeData.distance, 'cycling')) : '—';
    const walkTime = walkData ? formatDuration(durationFromDistance(walkData.distance, 'foot')) : '—';
    const driveDist = driveData ? (driveData.distance / 1000).toFixed(1) : '—';
    const bikeDist = bikeData ? (bikeData.distance / 1000).toFixed(1) : '—';
    const walkDist = walkData ? (walkData.distance / 1000).toFixed(1) : '—';
    routeSelectorBar.innerHTML = `
        <button type="button" class="lm-route-option ${navTravelMode === 'driving' ? 'active' : ''}" data-mode="driving" ${!driveData ? 'disabled' : ''}>
            <span class="lm-route-option-icon">🚗</span>
            <span class="lm-route-option-label">Drive</span>
            <span class="lm-route-option-meta">${driveTime} – ${driveDist} km</span>
        </button>
        <button type="button" class="lm-route-option ${navTravelMode === 'cycling' ? 'active' : ''}" data-mode="cycling" ${!bikeData ? 'disabled' : ''}>
            <span class="lm-route-option-icon">🚴</span>
            <span class="lm-route-option-label">Bike</span>
            <span class="lm-route-option-meta">${bikeTime} – ${bikeDist} km</span>
        </button>
        <button type="button" class="lm-route-option ${navTravelMode === 'foot' ? 'active' : ''}" data-mode="foot" ${!walkData ? 'disabled' : ''}>
            <span class="lm-route-option-icon">🚶</span>
            <span class="lm-route-option-label">Walk</span>
            <span class="lm-route-option-meta">${walkTime} – ${walkDist} km</span>
        </button>
        <button type="button" class="lm-route-cancel" onclick="window.LM.clearRoute()" title="Cancel route">
            <i class="bi bi-x-lg"></i> Cancel
        </button>`;
    if (mapCont) mapCont.insertBefore(routeSelectorBar, mapCont.firstChild);

    routeSelectorBar.querySelectorAll('.lm-route-option').forEach(btn => {
        if (btn.disabled) return;
        btn.addEventListener('click', () => {
            const mode = btn.dataset.mode;
            navTravelMode = mode;
            selectRouteMode(shop, mode);
        });
    });
}

/** Highlight selected route, fade others; zoom to route; update timeline panel. */
function selectRouteMode(shop, mode) {
    if (!routesByMode || !leafletMap) return;
    const data = routesByMode[mode];
    if (!data) return;

    // Update selector bar active state
    routeSelectorBar?.querySelectorAll('.lm-route-option').forEach(b => {
        b.classList.toggle('active', b.dataset.mode === mode);
    });

    // Route line styles: selected = thicker + full opacity, others = faded
    const ROUTE_KEYS = { driving: 'drive', cycling: 'bike', foot: 'walk' };
    const key = ROUTE_KEYS[mode];
    ['drive', 'bike', 'walk'].forEach(k => {
        const layer = routeLayers[k];
        if (!layer) return;
        const isSelected = k === key;
        const opts = layer.options;
        layer.setStyle({
            weight: isSelected ? 7 : 4,
            opacity: isSelected ? 1 : 0.35
        });
    });

    // Glow behind selected route (add first so selected line can be brought to front)
    if (polylineGlow) { leafletMap.removeLayer(polylineGlow); polylineGlow = null; }
    polylineGlow = L.polyline(data.latlngs, {
        color: key === 'drive' ? '#2563EB' : key === 'bike' ? '#7c3aed' : '#059669',
        weight: 20,
        opacity: 0.2,
        lineCap: 'round',
        lineJoin: 'round',
        className: 'lm-route-glow'
    }).addTo(leafletMap);
    if (routeLayers[key]) routeLayers[key].bringToFront();

    leafletMap.fitBounds(L.latLngBounds(data.latlngs), { padding: [60, 60], animate: true, duration: 0.5 });

    // Timeline panel: distance, time (from speed profile), turn-by-turn steps
    const body = document.getElementById('navBody');
    if (!body) return;
    const distKm = (data.distance / 1000).toFixed(1);
    const durationSeconds = durationFromDistance(data.distance, mode);
    const timeStr = formatDuration(durationSeconds);
    const steps = data.steps || [];
    const stepsHtml = steps.map((step, idx) => {
        const isFirst = idx === 0;
        const isLast = idx === steps.length - 1;
        const manType = step.maneuver?.type || 'depart';
        const modif   = step.maneuver?.modifier || '';
        let icon = 'arrow-up';
        if (isFirst) icon = 'geo-alt-fill';
        else if (isLast) icon = 'flag-fill';
        else if (manType === 'turn' && modif.includes('left')) icon = 'arrow-90deg-left';
        else if (manType === 'turn' && modif.includes('right')) icon = 'arrow-90deg-right';
        else if (manType === 'roundabout' || manType === 'rotary') icon = 'arrow-repeat';
        else if (manType === 'uturn') icon = 'arrow-counterclockwise';
        else if (manType === 'merge') icon = 'sign-merge-right';
        const distTxt = step.distance >= 1000 ? (step.distance / 1000).toFixed(1) + ' km' : Math.round(step.distance) + ' m';
        const road = step.name || '';
        const instruction = isFirst ? 'Head towards your destination' : isLast ? 'Arrive at destination' : (manType === 'turn' ? `Turn ${modif}${road ? ' onto ' + road : ''}` : `${manType.charAt(0).toUpperCase() + manType.slice(1)}${road ? ' on ' + road : ''}`);
        const stepLat = step.maneuver?.location?.[1];
        const stepLng = step.maneuver?.location?.[0];
        const clickAttr = (stepLat != null && stepLng != null) ? `onclick="window.LM.zoomToStep(${stepLat},${stepLng})"` : '';
        return `<div class="lm-route-step ${isFirst ? 'first-step' : ''} ${isLast ? 'last-step' : ''}" ${clickAttr}>
            <div class="lm-step-icon-wrap ${isFirst ? 'start-icon' : isLast ? 'end-icon' : ''}"><i class="bi bi-${icon}"></i></div>
            <div class="lm-step-body"><div class="lm-step-text">${esc(instruction)}</div>${road && !isFirst && !isLast ? `<div class="lm-step-road">${esc(road)}</div>` : ''}</div>
            <div class="lm-step-dist">${distTxt}</div>
        </div>`;
    }).join('');

    body.innerHTML = `
        <div class="lm-nav-route-summary">
            <div class="lm-nav-route-stat"><strong>Distance:</strong> ${distKm} km</div>
            <div class="lm-nav-route-stat"><strong>Time:</strong> ${timeStr}</div>
        </div>
        <div class="lm-nav-section-label">Steps</div>
        <div class="lm-route-steps-container">${stepsHtml}</div>`;
}

function zoomToStep(lat, lng) {
    if (!leafletMap) return;
    leafletMap.flyTo([lat, lng], 17, { duration: 1.2 });
    // Pulse a temporary marker at step location
    const pulse = L.circleMarker([lat, lng], {
        radius: 10, color: '#a855f7', fillColor: '#a855f7',
        fillOpacity: 0.5, weight: 3
    }).addTo(leafletMap);
    setTimeout(() => leafletMap.removeLayer(pulse), 3000);
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
    showSearchResults, quickSearch, addToCart,
    navigateInSite, clearRoute, zoomToShop, zoomToStep
};
