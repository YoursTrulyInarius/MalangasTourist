// public/assets/js/map-picker.js

function initMapPicker() {
    const mapEl = document.getElementById('map-picker');
    if (!mapEl) return;

    // ── Fix Leaflet default marker icon (broken in non-webpack environments) ──
    delete L.Icon.Default.prototype._getIconUrl;
    L.Icon.Default.mergeOptions({
        iconUrl:       'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
        iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
        shadowUrl:     'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
        iconSize:      [25, 41],
        iconAnchor:    [12, 41],
        popupAnchor:   [1, -34],
        shadowSize:    [41, 41]
    });

    const map = L.map('map-picker').setView([MAP_CENTER_LAT, MAP_CENTER_LNG], MAP_DEFAULT_ZOOM);
    
    // Street map layer
    const streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    });
    
    // Satellite layer (Esri WorldImagery + Hybrid Labels Group)
    const satelliteLayer = L.layerGroup([
        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19,
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri'
        }),
        L.tileLayer('https://services.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19,
            attribution: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ'
        })
    ]);

    // Start with street layer
    streetLayer.addTo(map);
    
    let currentLayer = 'street';

    // ── Map Type Toggle Button ───────────────────────────────────────────────
    const toggleBtn = document.createElement('button');
    toggleBtn.innerHTML = '🛰️ Satellite';
    toggleBtn.style.cssText = 'position: absolute; top: 10px; right: 10px; z-index: 999; padding: 0.5rem 0.75rem; background: white; border: 1px solid #ccc; border-radius: 4px; cursor: pointer; font-size: 0.85rem; font-weight: 600; box-shadow: 0 2px 6px rgba(0,0,0,0.2);';
    
    toggleBtn.addEventListener('click', function() {
        if (currentLayer === 'street') {
            map.removeLayer(streetLayer);
            satelliteLayer.addTo(map);
            currentLayer = 'satellite';
            toggleBtn.innerHTML = '🗺️ Street';
        } else {
            map.removeLayer(satelliteLayer);
            streetLayer.addTo(map);
            currentLayer = 'street';
            toggleBtn.innerHTML = '🛰️ Satellite';
        }
    });
    mapEl.parentElement.style.position = 'relative';
    mapEl.parentElement.appendChild(toggleBtn);

    let marker = null;
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const display  = document.getElementById('latlng-display');
    const barangaySelect = document.getElementById('barangay');
    const locationInput = document.getElementById('location-search');

    // ── Marker helpers ───────────────────────────────────────────────────────
    function setMarker(lat, lng, label = '') {
        if (marker) {
            marker.setLatLng([lat, lng]);
            if (label) {
                marker.bindPopup(label).openPopup();
            }
        } else {
            const markerOptions = { draggable: true };
            marker = L.marker([lat, lng], markerOptions).addTo(map);
            if (label) {
                marker.bindPopup(label).openPopup();
            }
            marker.on('dragend', function () {
                const p = marker.getLatLng();
                updateCoordinates(p.lat, p.lng);
            });
        }
        updateCoordinates(lat, lng);
    }

    function updateCoordinates(lat, lng) {
        const rLat = parseFloat(lat).toFixed(8);
        const rLng = parseFloat(lng).toFixed(8);
        latInput.value = rLat;
        lngInput.value = rLng;
        if (display) display.innerHTML = `Coordinates: <em>${rLat}, ${rLng}</em> <span style="font-size: 0.75rem; color: #999;">(Drag marker to adjust)</span>`;
    }

    map.on('click', function (e) {
        setMarker(e.latlng.lat, e.latlng.lng, '📍 Custom Location');
    });

    // Restore existing coords (edit mode)
    if (latInput.value && lngInput.value) {
        setMarker(parseFloat(latInput.value), parseFloat(lngInput.value));
        map.setView([parseFloat(latInput.value), parseFloat(lngInput.value)], 16);
    }

    // Barangay dropdown — no auto-pin or auto-center. User clicks the map to pin.

    // ── Confirmation dialog before pinning search results ───────────────────
    function confirmAndPinLocation(locationName, lat, lng) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Pin Location?',
                html: `<strong>${locationName}</strong><br><small style="color: #999;">Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}</small>`,
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#166534',
                cancelButtonColor: '#d1d5db',
                confirmButtonText: 'Yes, pin it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    setMarker(lat, lng, `📍 ${locationName}`);
                    map.setView([lat, lng], 17);
                    if (locationInput) {
                        locationInput.value = locationName;
                    }
                }
            });
        } else {
            // Fallback if Swal not available
            if (confirm(`Pin "${locationName}" on the map?`)) {
                setMarker(lat, lng, `📍 ${locationName}`);
                map.setView([lat, lng], 17);
                if (locationInput) {
                    locationInput.value = locationName;
                }
            }
        }
    }

    // ── Location search with autocomplete ────────────────────────────────────
    if (locationInput && barangaySelect) {
        const autocompleteList = document.getElementById('autocomplete-list');
        let autocompleteTimeout;

        locationInput.addEventListener('input', function() {
            clearTimeout(autocompleteTimeout);
            
            if (!this.value || this.value.length < 2 || !barangaySelect.value) {
                if (autocompleteList) autocompleteList.style.display = 'none';
                return;
            }

            autocompleteTimeout = setTimeout(() => {
                const location = this.value;
                const barangay = barangaySelect.value;
                const urlPrefix = (typeof BASE_URL !== 'undefined') ? BASE_URL : '';

                fetch(`${urlPrefix}/api/search-location?location=${encodeURIComponent(location)}&barangay=${encodeURIComponent(barangay)}&city=Malangas`)
                    .then(r => r.json())
                    .then(data => {
                        if (data.success && data.results && data.results.length > 0 && autocompleteList) {
                            autocompleteList.innerHTML = '';
                            data.results.forEach(result => {
                                const div = document.createElement('div');
                                div.style.cssText = 'padding: 0.75rem 1rem; cursor: pointer; border-bottom: 1px solid #f0f0f0; transition: all 0.2s;';
                                div.innerHTML = `<i class="fa-solid fa-map-pin" style="color: var(--primary-dark); margin-right: 0.5rem;"></i> <strong>${result.name}</strong>`;
                                div.onmouseover = () => div.style.backgroundColor = '#f5f5f5';
                                div.onmouseout = () => div.style.backgroundColor = 'transparent';
                                div.onclick = () => {
                                    locationInput.value = result.name;
                                    confirmAndPinLocation(result.name, result.latitude, result.longitude);
                                    autocompleteList.style.display = 'none';
                                };
                                autocompleteList.appendChild(div);
                            });
                            autocompleteList.style.display = 'block';
                        } else if (autocompleteList) {
                            autocompleteList.style.display = 'none';
                        }
                    })
                    .catch(e => {
                        console.error('Location search error:', e);
                        if (autocompleteList) autocompleteList.style.display = 'none';
                    });
            }, 300);
        });

        // Close autocomplete when clicking outside
        document.addEventListener('click', (e) => {
            if (e.target !== locationInput && autocompleteList) {
                autocompleteList.style.display = 'none';
            }
        });
    }
}

// Run immediately if DOM already ready (script loaded after </body>),
// otherwise wait for DOMContentLoaded.
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMapPicker);
} else {
    initMapPicker();
}
