const TRICHY_COORDS = [10.7905, 78.7047];

let map;
let allShops = [];
let markerLayer = L.layerGroup();
let userMarker;
let userLat = TRICHY_COORDS[0];
let userLng = TRICHY_COORDS[1];

document.addEventListener("DOMContentLoaded", initSystem);

function initSystem() {
    // 1. Init Map
    map = L.map("map").setView(TRICHY_COORDS, 13);

    // 2. Load OSM Tiles
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 19,
        attribution: "© OpenStreetMap contributors"
    }).addTo(map);

    markerLayer.addTo(map);

    // 3. Bind UI Events
    document.getElementById("radiusFilter").addEventListener("change", applyFilters);
    document.getElementById("typeFilter").addEventListener("change", applyFilters);
    document.getElementById("btnLocation").addEventListener("click", getUserLocation);

    // 4. Load Data
    fetchData();
}

function fetchData() {
    fetch("../data/trichy_shops.geojson")
        .then(res => res.json())
        .then(data => {
            console.log("Shops loaded:", data.features.length);
            // Parse GeoJSON to flat array
            allShops = data.features.map(f => {
                const props = f.properties;
                return {
                    id: props.shop_id || props['@id'] || Math.random(),
                    name: props.name || props['name:en'] || "Unnamed Shop",
                    type: (props.type || props.shop || props.amenity || "retail").toLowerCase(),
                    address: props.address || props['addr:street'] || "Trichy",
                    rating: props.rating || 4.0,
                    lat: f.geometry.coordinates[1],
                    lng: f.geometry.coordinates[0],
                    originalFeature: f
                };
            });

            applyFilters();
        })
        .catch(err => {
            console.error("GeoJSON load error:", err);
            document.getElementById("shopList").innerHTML = `<p style="color:red">Failed to load shop data.</p>`;
        });
}

function getUserLocation() {
    if (!navigator.geolocation) {
        alert("Geolocation is not supported by your browser");
        return;
    }

    navigator.geolocation.getCurrentPosition(
        pos => {
            userLat = pos.coords.latitude;
            userLng = pos.coords.longitude;
            map.flyTo([userLat, userLng], 14);
            
            if (userMarker) {
                map.removeLayer(userMarker);
            }
            
            // Red dot for user
            userMarker = L.circleMarker([userLat, userLng], {
                radius: 8,
                fillColor: "#4F46E5",
                color: "#fff",
                weight: 2,
                fillOpacity: 1
            }).bindPopup("You are here").addTo(map);

            applyFilters();
        },
        err => {
            console.warn("Geolocation denied or failed. Fallback to Trichy center.", err);
            userLat = TRICHY_COORDS[0];
            userLng = TRICHY_COORDS[1];
            applyFilters();
        }
    );
}

// Math logic for Distance
function getHaversineDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
              Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

function applyFilters() {
    if (!allShops.length) return;

    const maxRadius = parseFloat(document.getElementById("radiusFilter").value);
    const requiredType = document.getElementById("typeFilter").value;

    const filtered = allShops.map(shop => {
        shop.distance = getHaversineDistance(userLat, userLng, shop.lat, shop.lng);
        return shop;
    }).filter(shop => {
        if (shop.distance > maxRadius) return false;
        if (requiredType !== "all" && !shop.type.includes(requiredType)) return false;
        return true;
    });

    // Sort by distance and take top 5
    filtered.sort((a, b) => a.distance - b.distance);
    const top5 = filtered.slice(0, 5);

    renderMapMarkers(top5);
    renderSidebar(top5);
}

function renderMapMarkers(shops) {
    markerLayer.clearLayers();

    shops.forEach(shop => {
        const marker = L.marker([shop.lat, shop.lng]);
        
        marker.bindPopup(`
            <b>${shop.name}</b><br>
            Type: <span style="text-transform: capitalize;">${shop.type}</span><br>
            Address: ${shop.address}<br>
            <i>${shop.distance.toFixed(2)} km away</i>
        `);

        markerLayer.addLayer(marker);
    });
}

function renderSidebar(shops) {
    const container = document.getElementById("shopList");
    container.innerHTML = "";

    if (shops.length === 0) {
        container.innerHTML = `<p style="color:var(--text-muted)">No shops found in this area with current filters.</p>`;
        return;
    }

    shops.forEach(shop => {
        const card = document.createElement("div");
        card.className = "shop-card";
        
        card.innerHTML = `
            <h3 class="shop-name">${shop.name}</h3>
            <div class="shop-meta">
                <span class="shop-type">${shop.type}</span>
                <span>⭐ ${shop.rating}</span>
            </div>
            <p style="margin:0 0 0.5rem; font-size:0.85rem;">📍 ${shop.address}</p>
            <p style="margin:0 0 0.75rem; font-size:0.85rem; color:var(--primary); font-weight:600;">
                🛣️ ${shop.distance.toFixed(2)} km away
            </p>
            <div class="shop-actions">
                <button class="shop-btn shop-btn--primary">View Products</button>
                <a href="https://www.openstreetmap.org/directions?from=${userLat},${userLng}&to=${shop.lat},${shop.lng}" 
                   target="_blank" class="shop-btn shop-btn--outline" onclick="event.stopPropagation()">
                   Navigate
                </a>
            </div>
        `;

        card.addEventListener("click", () => {
            map.flyTo([shop.lat, shop.lng], 16);
            // Open matching marker popup (hacky but functional for basic Leaflet)
            markerLayer.eachLayer(layer => {
                const latlng = layer.getLatLng();
                if (latlng.lat === shop.lat && latlng.lng === shop.lng) {
                    layer.openPopup();
                }
            });
        });

        container.appendChild(card);
    });
}
