<?php require_once '../app/views/partials/header.php'; ?>

<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/map-public.css">

<section class="tourist-map" aria-label="Explore Malangas map">
    <div id="public-map" role="application" aria-label="Interactive map of Malangas"></div>

    <aside class="map-panel" aria-label="Map tools">
        <div class="map-panel-heading">
            <div>
                <span class="map-eyebrow">MALANGAS, ZAMBOANGA SIBUGAY</span>
                <h1>Explore the area</h1>
            </div>
            <button type="button" id="locate-me" class="map-icon-button" title="Use my current location" aria-label="Use my current location">
                <i class="fa-solid fa-location-crosshairs" aria-hidden="true"></i>
            </button>
        </div>

        <div class="map-search-fields">
            <label class="map-field-label" for="search-spot">Find a tourist spot</label>
            <input type="search" id="search-spot" class="form-control" placeholder="Search approved spots">
            <label class="map-field-label" for="filter-category">Category</label>
            <select id="filter-category" class="form-control">
            <option value="">All Categories</option>
            <option value="Beach/Island">Beach/Island</option>
            <option value="Waterfall">Waterfall</option>
            <option value="Resort">Resort</option>
            <option value="Restaurant">Restaurant</option>
            <option value="Boat/Tour Operator">Boat/Tour Operator</option>
            </select>
        </div>

        <div class="map-route-tools">
            <label class="map-field-label" for="destination-spot">Destination</label>
            <select id="destination-spot" class="form-control" disabled>
                <option value="">Choose a tourist spot</option>
            </select>
            <div class="map-travel-modes" role="group" aria-label="Travel mode">
                <button type="button" class="mode-btn active" data-mode="car" title="Drive (Car / Motorcycle)"><i class="fa-solid fa-car-side" aria-hidden="true"></i> Drive</button>
                <button type="button" class="mode-btn" data-mode="bike" title="Bicycle"><i class="fa-solid fa-bicycle" aria-hidden="true"></i> Bike</button>
                <button type="button" class="mode-btn" data-mode="foot" title="Walk"><i class="fa-solid fa-person-walking" aria-hidden="true"></i> Walk</button>
            </div>
            <button type="button" id="show-route" class="map-primary-button" disabled>
                <i class="fa-solid fa-route" aria-hidden="true"></i> Show route
            </button>
            <button type="button" id="clear-route" class="map-text-button" hidden>Clear route</button>
            <p id="map-status" class="map-status" role="status" aria-live="polite">Allow location access to plan a route from where you are.</p>
        </div>

        <div class="map-nearby-tools">
            <div class="map-section-heading">
                <div>
                    <span class="map-eyebrow">AROUND YOU</span>
                    <h2>Nearby places</h2>
                </div>
                <button type="button" id="find-nearby" class="map-icon-button map-nearby-button" title="Find nearby places" aria-label="Find nearby places">
                    <i class="fa-solid fa-magnifying-glass-location" aria-hidden="true"></i>
                </button>
            </div>
            <div class="map-nearby-filters" aria-label="Nearby place types">
                <label><input type="checkbox" name="nearby-type" value="toilets" checked><span class="map-nearby-code" style="--map-nearby-color:#2172b8;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 10h16l-1.5 5.5a5 5 0 0 1-4.8 3.5h-3.4a5 5 0 0 1-4.8-3.5L4 10Z"/><path d="M7 10V5a2 2 0 0 1 2-2h4M9 19v2m6-2v2m-7 0h8"/></svg></span> Restrooms</label>
                <label><input type="checkbox" name="nearby-type" value="food" checked><span class="map-nearby-code" style="--map-nearby-color:#d66b28;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 3v6m-2-6v3a2 2 0 0 0 4 0V3m-2 6v12M15 3c-2 2-3 4-3 7h4V3m-2 7v11"/></svg></span> Food</label>
                <label><input type="checkbox" name="nearby-type" value="stay" checked><span class="map-nearby-code" style="--map-nearby-color:#176354;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 19V6m0 9h18v4m0-7v7M3 12h5V8a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2m5 7v-5h11a3 3 0 0 1 3 3"/></svg></span> Stays</label>
                <label><input type="checkbox" name="nearby-type" value="tourism" checked><span class="map-nearby-code" style="--map-nearby-color:#9a6b14;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h18M4 18h16M5 9h14M3 7l9-4 9 4M7 9v9m5-9v9m5-9v9"/></svg></span> Attractions</label>
                <label><input type="checkbox" name="nearby-type" value="other" checked><span class="map-nearby-code" style="--map-nearby-color:#6957a5;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 10h18l-1.5-6h-15L3 10Zm2 0v10h14V10M9 20v-6h6v6M3 10a3 3 0 0 0 6 0 3 3 0 0 0 6 0 3 3 0 0 0 6 0"/></svg></span> Other services</label>
            </div>
            <p id="nearby-status" class="map-status">Search around your location or the visible map area.</p>
            <ul id="nearby-results" class="nearby-results" aria-label="Nearby places results"></ul>
        </div>
        <p class="map-attribution-note">Map and nearby places by OpenStreetMap contributors. Directions are approximate.</p>
    </aside>
</section>

<script src="<?= BASE_URL ?>/assets/js/map-public.js"></script>

<?php require_once '../app/views/partials/spot-modal.php'; ?>

<?php require_once '../app/views/partials/footer.php'; ?>
