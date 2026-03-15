# Univault — College Project Presentation

---

# Slide 1 — Project Title

**Univault – AI Powered Hyperlocal Ecommerce Platform**

- **Project name:** Univault  
- **Developer:** Karuppasamy M  
- **Project type:** AI-powered hyperlocal ecommerce platform  

A modern web application that combines online shopping with AI assistance and map-based local store discovery.

**Image suggestion:** Modern ecommerce UI dashboard with product grid and clean navigation.

**Diagram suggestion:** None.

---

# Slide 2 — Problem Statement

**The problem**

- Users struggle to find nearby stores quickly.
- It is hard to know which local shops have the products they need.
- Comparing options across multiple stores is time-consuming.

**The solution**

- A hyperlocal ecommerce platform with map-based search.
- Users can see nearby shops, filter by category, and get directions in one place.

**Image suggestion:** Person searching for products on a map on their device.

**Diagram suggestion:** Problem (confused user) → Solution (map with stores) flow.

---

# Slide 3 — Project Overview

**What is Univault?**

Univault is a full ecommerce platform that brings together:

- **Online shopping** — Browse and buy products.
- **AI shopping assistant** — Get personalised product suggestions.
- **Local store discovery** — Find nearby shops on an interactive map.

**Key idea:** AI + Ecommerce + Local shop discovery in one platform.

**Image suggestion:** Ecommerce website interface showing products and a map section.

**Diagram suggestion:** Three overlapping circles: AI, Ecommerce, Local discovery.

---

# Slide 4 — Key Features

**Main features of Univault**

- **AI Shopping Assistant** — Ask in natural language and get product recommendations.
- **Product browsing** — Browse categories and product listings.
- **Cart system** — Add items and manage cart before checkout.
- **Wishlist** — Save favourite products for later.
- **Local Mart map** — Interactive map with nearby shops and filters.
- **Navigation directions** — Turn-by-turn directions from user location to selected shop.

**Image suggestion:** Feature icons in a grid or row (cart, wishlist, map, chatbot, etc.).

**Diagram suggestion:** Feature list or icon-based feature map.

---

# Slide 5 — AI Shopping Assistant

**How it works**

- The user types or speaks a request (e.g. “Best smartwatch under ₹3000”).
- The AI analyses the query and suggests suitable products from the catalog.
- Results can show price, ratings, and links to buy or add to cart.

**Example query:** “Best smartwatch under ₹3000”

**Benefits:** Saves time and helps users discover products that match their budget and needs.

**Image suggestion:** Chatbot UI with user message and AI reply with product cards.

**Diagram suggestion:** User → Query → AI → Product suggestions.

---

# Slide 6 — Hyperlocal Store Map

**Local Mart feature**

- **Interactive map** — Shops are shown as markers on a map (e.g. Trichy area).
- **Nearby shops list** — List of shops sorted by distance with name, category, rating, and distance.
- **Filters** — Filter by category (grocery, electronics, pharmacy, etc.), radius, and rating.
- **Shop details** — Click a shop to see details, products, and actions (e.g. View on Map, Directions).

Users can quickly see which stores are nearby and what they offer.

**Image suggestion:** Interactive map with coloured store markers and a side panel listing shops.

**Diagram suggestion:** Map with user location, radius circle, and shop markers.

---

# Slide 7 — Map Navigation System

**Route navigation**

- User selects a shop (from the list or map).
- **Generate route** — The system gets a route from the user’s location to the shop (e.g. via OSRM).
- **Route on map** — A route line is drawn on the map (e.g. purple line with glow).
- **Turn-by-turn directions** — A panel shows step-by-step instructions (e.g. “Turn left onto Main Road”, distance per step).
- **Travel modes** — Options such as driving, cycling, or walking.

**Cancel route** — User can cancel navigation; the route and directions are cleared and the map returns to the default shop view.

**Image suggestion:** Google Maps–style route with a purple path and a directions panel.

**Diagram suggestion:** User location → Route line → Shop marker, with direction steps list.

---

# Slide 8 — Technology Stack

**Technologies used**

| Layer        | Technology                          |
|-------------|--------------------------------------|
| **Frontend** | HTML, CSS, JavaScript                |
| **Maps**     | Leaflet.js, OpenStreetMap, GeoJSON   |
| **Backend**  | PHP                                  |
| **Data**     | CSV files (e.g. shops, products)     |
| **Deployment** | Vercel                            |

- **Leaflet.js** — Renders the map, markers, and routes.
- **OpenStreetMap / CartoDB** — Map tiles (e.g. Voyager style).
- **OSRM** — Routing API for directions (no API key required for basic use).
- **PHP** — Server-side logic and page rendering.

**Image suggestion:** Tech stack diagram with icons for HTML, CSS, JS, PHP, Leaflet, OSM, CSV, Vercel.

**Diagram suggestion:** Horizontal or layered stack: Frontend → Backend → Data → Deployment.

---

# Slide 9 — System Architecture

**High-level structure**

- **User** — Uses browser to access the platform (shop, ask AI, use map).
- **Frontend** — HTML pages, CSS styling, JavaScript for UI, map, and AI chat.
- **Map engine** — Leaflet + OSM tiles; GeoJSON for shops; OSRM for routes; markers and routing drawn on the client.
- **Backend** — PHP handles pages, includes, and any server-side logic.
- **Data storage** — CSV (or similar) for shops and products; no database required for the demo.

**Flow:** User → Frontend (UI + Map) → Backend (PHP) → Data (CSV); Map engine runs in the browser and calls OSRM for routes.

**Image suggestion:** Architecture diagram: User → Frontend → Backend → Data; Map engine inside Frontend.

**Diagram suggestion:** Simple blocks: [User] → [Browser / Frontend] → [PHP Backend] → [CSV]. Optional: [OSRM] as external service from Frontend.

---

# Slide 10 — Conclusion

**Summary**

- Univault is an **AI-powered hyperlocal ecommerce platform** that combines online shopping, an AI shopping assistant, and map-based local store discovery.
- **Developer:** Karuppasamy M  
- **Portfolio:** https://ks02.vercel.app  

**How Univault improves local shopping**

- One place to **browse products** and **get AI suggestions**.
- **Find nearby shops** on a map with filters and distance.
- **Get directions** to chosen stores with turn-by-turn navigation.
- Easier to **compare** local options and decide where to go.

**Image suggestion:** Smart shopping illustration (person with phone, map, and shopping bag).

**Diagram suggestion:** None, or a simple “Univault = Ecommerce + AI + Local Map” summary graphic.

---

*End of presentation — Thank you*
