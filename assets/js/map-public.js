document.addEventListener('DOMContentLoaded', function () {
    const map = L.map('public-map', { zoomControl: false }).setView(
        [MAP_CENTER_LAT, MAP_CENTER_LNG],
        MAP_DEFAULT_ZOOM
    );
    L.control.zoom({ position: 'topright' }).addTo(map);
    const mapResizeObserver = new ResizeObserver(() => {
        requestAnimationFrame(() => map.invalidateSize({ pan: false }));
    });
    mapResizeObserver.observe(document.getElementById('public-map'));
    window.addEventListener('resize', () => map.invalidateSize({ pan: false }));

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap contributors</a>'
    }).addTo(map);

    const spotsLayer = L.layerGroup().addTo(map);
    const nearbyLayer = L.layerGroup().addTo(map);
    const routeLayer = L.layerGroup().addTo(map);
    const searchInput = document.getElementById('search-spot');
    const categorySelect = document.getElementById('filter-category');
    const destinationSelect = document.getElementById('destination-spot');
    const routeButton = document.getElementById('show-route');
    const clearRouteButton = document.getElementById('clear-route');
    const locationButton = document.getElementById('locate-me');
    const nearbyButton = document.getElementById('find-nearby');
    const mapStatus = document.getElementById('map-status');
    const nearbyStatus = document.getElementById('nearby-status');
    const nearbyResults = document.getElementById('nearby-results');

    let allFeatures = [];
    let nearbyFeatures = [];
    let currentLocation = null;
    let userMarker = null;
    let selectedDestination = null;

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (character) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[character];
        });
    }

    function renderSpots(features) {
        spotsLayer.clearLayers();
        features.forEach(function (feature) {
            const coordinates = feature.geometry.coordinates;
            const properties = feature.properties;
            const marker = L.marker([coordinates[1], coordinates[0]]).addTo(spotsLayer);
            const rating = Number(properties.avg_rating) > 0
                ? `<br><span class="map-popup-rating"><i class="fa-solid fa-star"></i> ${escapeHtml(properties.avg_rating)} (${escapeHtml(properties.review_count)})</span>`
                : '';

            marker.bindPopup(`
                <strong>${escapeHtml(properties.name)}</strong><br>
                ${escapeHtml(properties.category)}${rating}
                <br><span class="map-popup-status">${escapeHtml(properties.op_status_label)}</span>
                <br><button type="button" class="map-popup-route" data-destination="${escapeHtml(properties.id)}">Set as destination</button>
                <br><a href="#" onclick="openSpotModal(${Number(properties.id)}); return false;">View details</a>
            `);
            marker.on('popupopen', function (event) {
                const button = event.popup.getElement().querySelector('.map-popup-route');
                if (button) {
                    button.addEventListener('click', function () {
                        setDestination(feature);
                        event.popup.remove();
                    });
                }
            });
        });
    }

    function setDestination(feature) {
        selectedDestination = feature;
        const destinationId = String(feature.properties.id);
        if (!Array.from(destinationSelect.options).some(option => option.value === destinationId)) {
            destinationSelect.add(new Option(feature.properties.name, destinationId));
        }
        destinationSelect.value = destinationId;
        routeButton.disabled = !currentLocation;
        mapStatus.textContent = currentLocation
            ? `Ready to route to ${feature.properties.name}.`
            : 'Use your location first to get directions to this spot.';
    }

    function filterSpots() {
        const query = searchInput.value.trim().toLocaleLowerCase();
        const category = categorySelect.value;
        const filtered = allFeatures.filter(function (feature) {
            const properties = feature.properties;
            return properties.name.toLocaleLowerCase().includes(query)
                && (!category || properties.category === category);
        });
        renderSpots(filtered);
    }

    function updateDestinationOptions() {
        destinationSelect.replaceChildren(new Option('Choose a tourist spot', ''));
        allFeatures.forEach(function (feature) {
            destinationSelect.add(new Option(feature.properties.name, feature.properties.id));
        });
        destinationSelect.disabled = allFeatures.length === 0;
    }

    function locateUser() {
        if (!navigator.geolocation) {
            mapStatus.textContent = 'Location is not available in this browser.';
            return;
        }

        locationButton.disabled = true;
        mapStatus.textContent = 'Finding your location...';
        navigator.geolocation.getCurrentPosition(function (position) {
            currentLocation = L.latLng(position.coords.latitude, position.coords.longitude);
            if (!userMarker) {
                userMarker = L.circleMarker(currentLocation, {
                    radius: 9,
                    color: '#ffffff',
                    weight: 3,
                    fillColor: '#1976d2',
                    fillOpacity: 1
                }).addTo(map).bindPopup('Your current location');
            } else {
                userMarker.setLatLng(currentLocation);
            }

            map.setView(currentLocation, 15);
            routeButton.disabled = !selectedDestination;
            mapStatus.textContent = selectedDestination
                ? `Location found. Ready to route to ${selectedDestination.properties.name}.`
                : 'Location found. Choose a destination to plan your route.';
            locationButton.disabled = false;
        }, function (error) {
            const messages = {
                1: 'Location access was denied. Allow location access in your browser to use directions.',
                2: 'Your location could not be determined. Try again outdoors or check your device settings.',
                3: 'Finding your location timed out. Please try again.'
            };
            mapStatus.textContent = messages[error.code] || 'Could not get your location. Please try again.';
            locationButton.disabled = false;
        }, { enableHighAccuracy: true, timeout: 15000, maximumAge: 30000 });
    }

    let currentTravelMode = 'car';
    const modeButtons = document.querySelectorAll('.mode-btn');
    modeButtons.forEach(button => {
        button.addEventListener('click', function () {
            modeButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            currentTravelMode = this.dataset.mode || 'car';
            if (currentLocation && selectedDestination && !clearRouteButton.hidden) {
                showRoute();
            }
        });
    });

    async function showRoute() {
        if (!currentLocation || !selectedDestination) return;

        routeButton.disabled = true;
        mapStatus.textContent = `Finding best ${currentTravelMode === 'car' ? 'driving' : (currentTravelMode === 'bike' ? 'cycling' : 'walking')} route...`;
        routeLayer.clearLayers();
        const [longitude, latitude] = selectedDestination.geometry.coordinates;
        const origin = `${currentLocation.lng},${currentLocation.lat}`;
        const destination = `${longitude},${latitude}`;

        const serviceMap = {
            car: 'https://routing.openstreetmap.de/routed-car/route/v1/driving',
            bike: 'https://routing.openstreetmap.de/routed-bike/route/v1/driving',
            foot: 'https://routing.openstreetmap.de/routed-foot/route/v1/driving'
        };
        const colorMap = {
            car: '#176354',
            bike: '#d66b28',
            foot: '#2172b8'
        };

        const primaryUrl = `${serviceMap[currentTravelMode] || serviceMap.car}/${origin};${destination}?overview=full&geometries=geojson`;
        const fallbackUrl = `https://router.project-osrm.org/route/v1/driving/${origin};${destination}?overview=full&geometries=geojson`;

        try {
            let response = await fetch(primaryUrl).catch(() => null);
            if (!response || !response.ok) {
                response = await fetch(fallbackUrl);
            }
            if (!response.ok) throw new Error('The directions service is temporarily unavailable.');
            const data = await response.json();
            if (data.code !== 'Ok' || !data.routes || !data.routes.length) {
                throw new Error('No navigable route was found for this destination.');
            }

            const route = data.routes[0];
            const routeColor = colorMap[currentTravelMode] || '#176354';
            const line = L.geoJSON(route.geometry, {
                style: { color: routeColor, weight: 6, opacity: 0.9, lineCap: 'round', lineJoin: 'round' }
            }).addTo(routeLayer);
            map.fitBounds(line.getBounds(), { paddingTopLeft: [360, 40], paddingBottomRight: [40, 40] });
            const distance = route.distance >= 1000
                ? `${(route.distance / 1000).toFixed(1)} km`
                : `${Math.round(route.distance)} m`;
            const minutes = Math.max(1, Math.round(route.duration / 60));
            const hours = Math.floor(minutes / 60);
            const timeLabel = hours > 0 ? `${hours} hr ${minutes % 60} min` : `${minutes} min`;
            const modeLabel = currentTravelMode === 'car' ? 'by road' : (currentTravelMode === 'bike' ? 'by bicycle' : 'on foot');
            mapStatus.textContent = `${distance} (${modeLabel}), approx. ${timeLabel} to ${selectedDestination.properties.name}.`;
            clearRouteButton.hidden = false;
        } catch (error) {
            mapStatus.textContent = error.message || 'Could not load directions. Please try again.';
        } finally {
            routeButton.disabled = false;
        }
    }

    function clearRoute() {
        routeLayer.clearLayers();
        clearRouteButton.hidden = true;
        mapStatus.textContent = selectedDestination
            ? `Destination selected: ${selectedDestination.properties.name}.`
            : 'Choose a destination to plan a route.';
    }

    function nearbyQueries(latitude, longitude, selectedTypes) {
        const filters = [];
        if (selectedTypes.includes('toilets')) filters.push('["amenity"="toilets"]');
        if (selectedTypes.includes('food')) filters.push('["amenity"~"restaurant|cafe|fast_food|food_court|bar|pub"]');
        if (selectedTypes.includes('stay')) filters.push('["tourism"~"hotel|guest_house|hostel|motel|camp_site"]');
        if (selectedTypes.includes('tourism')) {
            filters.push('["tourism"~"attraction|museum|viewpoint|information|theme_park"]');
            filters.push('["historic"]');
        }
        if (selectedTypes.includes('other')) {
            filters.push('["amenity"~"fuel|pharmacy|hospital|clinic|bank|atm|bus_station|marketplace|post_office|place_of_worship|police|fire_station"]');
            filters.push('["shop"]');
        }
        return `[out:json][timeout:20];(${filters.map(filter => `nwr(around:15000,${latitude},${longitude})${filter};`).join('')});out center tags 40;`;
    }

    function nearbyCategory(tags) {
        if (tags.amenity === 'toilets') return 'Restroom';
        if (['restaurant', 'cafe', 'fast_food', 'food_court', 'bar', 'pub'].includes(tags.amenity)) return 'Food & drink';
        if (['hotel', 'guest_house', 'hostel', 'motel', 'camp_site'].includes(tags.tourism)) return 'Place to stay';
        if (tags.tourism || tags.historic) return 'Attraction';
        return 'Other service';
    }

    function nearbyMarkerStyle(category) {
        return {
            'Restroom': { color: '#2172b8', svg: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 10h16l-1.5 5.5a5 5 0 0 1-4.8 3.5h-3.4a5 5 0 0 1-4.8-3.5L4 10Z"/><path d="M7 10V5a2 2 0 0 1 2-2h4M9 19v2m6-2v2m-7 0h8"/></svg>' },
            'Food & drink': { color: '#d66b28', svg: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 3v6m-2-6v3a2 2 0 0 0 4 0V3m-2 6v12M15 3c-2 2-3 4-3 7h4V3m-2 7v11"/></svg>' },
            'Place to stay': { color: '#176354', svg: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 19V6m0 9h18v4m0-7v7M3 12h5V8a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2m5 7v-5h11a3 3 0 0 1 3 3"/></svg>' },
            'Attraction': { color: '#9a6b14', svg: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h18M4 18h16M5 9h14M3 7l9-4 9 4M7 9v9m5-9v9m5-9v9"/></svg>' },
            'Tourist Spot': { color: '#123821', svg: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>' },
            'Other service': { color: '#6957a5', svg: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 10h18l-1.5-6h-15L3 10Zm2 0v10h14V10M9 20v-6h6v6M3 10a3 3 0 0 0 6 0 3 3 0 0 0 6 0 3 3 0 0 0 6 0"/></svg>' }
        }[category] || { color: '#176354', svg: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s7-6.2 7-12a7 7 0 1 0-14 0c0 5.8 7 12 7 12Z"/><circle cx="12" cy="9" r="2.5"/></svg>' };
    }

    async function requestNearbyData(query) {
        let lastError;
        for (let attempt = 0; attempt < 3; attempt++) {
            const controller = new AbortController();
            const timeout = setTimeout(() => controller.abort(), 20000);
            try {
                const response = await fetch('https://overpass-api.de/api/interpreter', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
                    body: `data=${encodeURIComponent(query)}`,
                    signal: controller.signal
                });
                if (!response.ok) throw new Error(`Nearby search returned ${response.status}.`);
                const data = await response.json();
                if (data.remark && !Array.isArray(data.elements)) throw new Error(data.remark);
                return data;
            } catch (error) {
                lastError = error;
                if (attempt === 2) break;
                nearbyStatus.textContent = `Nearby search is busy. Retrying (${attempt + 2} of 3)...`;
                await new Promise(resolve => setTimeout(resolve, 700 * (attempt + 1)));
            } finally {
                clearTimeout(timeout);
            }
        }
        throw new Error(lastError?.name === 'AbortError'
            ? 'Nearby search timed out. Please try again shortly.'
            : 'Nearby place data is temporarily unavailable. Please try again shortly.');
    }

    async function findNearby() {
        const checkedTypes = Array.from(document.querySelectorAll('input[name="nearby-type"]:checked'))
            .map(input => input.value);
        if (!checkedTypes.length) {
            nearbyStatus.textContent = 'Choose at least one type of place to search for.';
            return;
        }

        const anchor = currentLocation || (selectedDestination
            ? L.latLng(selectedDestination.geometry.coordinates[1], selectedDestination.geometry.coordinates[0])
            : map.getCenter());
        nearbyButton.disabled = true;
        nearbyStatus.textContent = 'Searching nearby places & tourist spots...';
        nearbyResults.replaceChildren();
        nearbyLayer.clearLayers();

        try {
            let localPlaces = [];
            let osmPlaces = [];

            // 1. Fetch local directory places
            try {
                const localResp = await fetch(`api/get-nearby-places?lat=${encodeURIComponent(anchor.lat)}&lng=${encodeURIComponent(anchor.lng)}`);
                if (localResp.ok) {
                    const localData = await localResp.json();
                    if (localData.success && Array.isArray(localData.places)) {
                        const typeToCategory = {
                            restroom: 'Restroom',
                            food: 'Food & drink',
                            stay: 'Place to stay',
                            attraction: 'Attraction',
                            other: 'Other service'
                        };
                        localPlaces = localData.places.map(p => ({
                            id: p.id,
                            lat: Number(p.lat),
                            lng: Number(p.lng),
                            name: p.name,
                            category: typeToCategory[p.category] || 'Other service',
                            typeCode: p.category
                        })).filter(p => checkedTypes.includes(p.typeCode));
                    }
                }
            } catch (localErr) {
                console.warn('Local places fetch error:', localErr);
            }

            // 2. Fetch OSM places if available
            try {
                const query = nearbyQueries(anchor.lat, anchor.lng, checkedTypes);
                const data = await requestNearbyData(query);
                osmPlaces = (data.elements || []).map(function (element) {
                    const tags = element.tags || {};
                    const lat = element.lat ?? element.center?.lat;
                    const lng = element.lon ?? element.center?.lon;
                    const name = tags.name || tags['name:en'] || tags.amenity || tags.tourism || tags.historic;
                    if (!Number.isFinite(lat) || !Number.isFinite(lng) || !name) return null;
                    return { id: `${element.type}-${element.id}`, lat, lng, name, category: nearbyCategory(tags) };
                }).filter(Boolean);
            } catch (osmErr) {
                console.warn('OSM POI search skipped or failed:', osmErr);
            }

            // Include registered tourist spots from MTMS within radius
            const registeredSpots = allFeatures.map(function (feature) {
                const coords = feature.geometry.coordinates;
                const dist = anchor.distanceTo([coords[1], coords[0]]);
                if (dist > 15000) return null; // within 15 km
                return {
                    id: `spot-${feature.properties.id}`,
                    lat: coords[1],
                    lng: coords[0],
                    name: feature.properties.name,
                    category: 'Tourist Spot',
                    isRegisteredSpot: true,
                    spotId: feature.properties.id,
                    feature: feature
                };
            }).filter(Boolean);

            const allPlaces = localPlaces.concat(osmPlaces).concat(registeredSpots);
            const uniquePlaces = Array.from(new Map(allPlaces.map(place => [place.id, place])).values());
            uniquePlaces.sort((first, second) => anchor.distanceTo([first.lat, first.lng]) - anchor.distanceTo([second.lat, second.lng]));

            if (!uniquePlaces.length) {
                nearbyStatus.textContent = 'No mapped places found in this area. Try another place type.';
                return;
            }

            uniquePlaces.forEach(function (place) {
                const distance = anchor.distanceTo([place.lat, place.lng]);
                const distanceLabel = distance >= 1000 ? `${(distance / 1000).toFixed(1)} km` : `${Math.round(distance)} m`;
                const markerStyle = nearbyMarkerStyle(place.category);
                const markerIcon = document.createElement('span');
                markerIcon.className = 'map-nearby-pin';
                markerIcon.style.setProperty('--map-nearby-color', markerStyle.color);
                markerIcon.innerHTML = markerStyle.svg;
                const marker = L.marker([place.lat, place.lng], {
                    icon: L.divIcon({
                        className: 'map-nearby-pin-wrap',
                        html: markerIcon,
                        iconSize: [34, 34],
                        iconAnchor: [17, 17]
                    })
                }).addTo(nearbyLayer);

                const nearbyFeature = place.feature || {
                    type: 'Feature',
                    geometry: { type: 'Point', coordinates: [place.lng, place.lat] },
                    properties: { id: `nearby-${place.id}`, name: place.name, category: place.category }
                };
                nearbyFeatures.push(nearbyFeature);

                const viewDetailsBtn = place.isRegisteredSpot
                    ? `<br><a href="#" onclick="openSpotModal(${Number(place.spotId)}); return false;">View Spot Details</a>`
                    : '';

                marker.bindPopup(`<strong>${escapeHtml(place.name)}</strong><br>${escapeHtml(place.category)} &middot; ${distanceLabel}<br><button type="button" class="map-popup-route" data-destination="${escapeHtml(nearbyFeature.properties.id)}">Set as destination</button>${viewDetailsBtn}`);
                marker.on('popupopen', function (event) {
                    const button = event.popup.getElement().querySelector('.map-popup-route');
                    if (button) {
                        button.addEventListener('click', function () {
                            setDestination(nearbyFeature);
                            event.popup.remove();
                        });
                    }
                });

                const item = document.createElement('li');
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'nearby-result';
                button.innerHTML = `<span><strong>${escapeHtml(place.name)}</strong><small>${escapeHtml(place.category)}</small></span><span class="nearby-distance">${distanceLabel}</span>`;
                button.addEventListener('click', function () {
                    map.setView([place.lat, place.lng], 16);
                    marker.openPopup();
                });
                item.appendChild(button);
                const routeToPlace = document.createElement('button');
                routeToPlace.type = 'button';
                routeToPlace.className = 'nearby-route-button';
                routeToPlace.title = `Set ${place.name} as destination`;
                routeToPlace.setAttribute('aria-label', `Set ${place.name} as destination`);
                routeToPlace.innerHTML = '<i class="fa-solid fa-route" aria-hidden="true"></i>';
                routeToPlace.addEventListener('click', function () {
                    setDestination(nearbyFeature);
                });
                item.appendChild(routeToPlace);
                nearbyResults.appendChild(item);
            });
            nearbyStatus.textContent = `${uniquePlaces.length} nearby places & spots found, sorted by distance.`;
        } catch (error) {
            nearbyStatus.textContent = error.message || 'Could not load nearby places. Please try again.';
        } finally {
            nearbyButton.disabled = false;
        }
    }

    fetch(`${BASE_URL}/api/get-spots`)
        .then(function (response) {
            if (!response.ok) throw new Error('Tourist spots could not be loaded.');
            return response.json();
        })
        .then(function (data) {
            if (data.type !== 'FeatureCollection' || !Array.isArray(data.features)) {
                throw new Error('The tourist spots response was not valid.');
            }
            allFeatures = data.features;
            renderSpots(allFeatures);
            updateDestinationOptions();

            // Check URL search parameters (e.g., /map?spot=5 or /map?destination=5)
            const urlParams = new URLSearchParams(window.location.search);
            const targetSpotId = urlParams.get('spot') || urlParams.get('destination') || urlParams.get('id');

            if (targetSpotId) {
                const targetFeature = allFeatures.find(f => String(f.properties.id) === String(targetSpotId));
                if (targetFeature) {
                    setDestination(targetFeature);
                    const coords = targetFeature.geometry.coordinates;
                    map.setView([coords[1], coords[0]], 15);
                    // Locate user automatically to enable immediate route planning
                    locateUser();
                    // Also trigger nearby places around this spot
                    setTimeout(() => findNearby(), 600);
                    return;
                }
            }

            if (allFeatures.length) {
                const spotBounds = L.featureGroup(allFeatures.map(function (feature) {
                    const coordinates = feature.geometry.coordinates;
                    return L.marker([coordinates[1], coordinates[0]]);
                })).getBounds();
                if (spotBounds.isValid()) map.fitBounds(spotBounds, { padding: [40, 40], maxZoom: 14 });
            }
        })
        .catch(function (error) {
            mapStatus.textContent = error.message || 'Tourist spots could not be loaded.';
        });

    searchInput.addEventListener('input', filterSpots);
    categorySelect.addEventListener('change', filterSpots);
    destinationSelect.addEventListener('change', function () {
        const feature = allFeatures.concat(nearbyFeatures)
            .find(item => String(item.properties.id) === destinationSelect.value);
        if (feature) setDestination(feature);
        else {
            selectedDestination = null;
            routeButton.disabled = true;
            mapStatus.textContent = 'Choose a destination to plan a route.';
        }
    });
    locationButton.addEventListener('click', locateUser);
    routeButton.addEventListener('click', showRoute);
    clearRouteButton.addEventListener('click', clearRoute);
    nearbyButton.addEventListener('click', findNearby);
});
