<?php
$title = 'Apply for Listing - Applicant Panel';
$activePage = 'apply';
$categories = $categories ?? [];
$swalError = '';
if (!empty($_SESSION['swal_error'])) {
    $swalError = $_SESSION['swal_error'];
    unset($_SESSION['swal_error']);
}
require_once '../app/views/partials/business-header.php';
?>

<!-- Top Bar -->
<div class="biz-topbar">
    <div>
        <div class="biz-topbar-title">Apply for Listing</div>
        <div class="biz-topbar-breadcrumb">Submit your establishment details and documents to the LGU for review</div>
    </div>
    <a href="<?= BASE_URL ?>/business" style="padding:0.45rem 1rem;border-radius:0.5rem;border:1.5px solid #d1d5db;color:#4b5563;text-decoration:none;font-size:0.88rem;font-weight:500;">
        <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
    </a>
</div>

<div class="biz-page-body">
    <div style="max-width: 820px; margin: 0 auto; background: white; border-radius: 0.75rem; box-shadow: 0 1px 4px rgba(0,0,0,0.07); overflow: hidden;">
        <div style="padding: 1.5rem 2rem; border-bottom: 1px solid #f0f0f0; background: #f8f9fb;">
            <div style="font-weight: 700; font-size: 1rem; color: var(--primary-dark);">Accreditation Application Form</div>
            <div style="font-size: 0.8rem; color: var(--text-muted);">Your application and documents will be reviewed by the LGU before your business appears on the public map. Fields marked with * are required.</div>
        </div>
        
        <form id="apply-form" action="<?= BASE_URL ?>/api/apply-submit" method="POST" enctype="multipart/form-data" style="padding: 2rem;">

            <!-- Document Details Section -->
            <div style="background:#f8f9fa; border:1px solid #e5e7eb; border-radius:0.5rem; padding:1.5rem; margin-bottom:1.5rem;">
                <h3 style="font-size:0.9rem; margin-bottom:1rem; color:var(--primary-dark); text-transform:uppercase; letter-spacing:0.05em;"><i class="fa-solid fa-file-contract"></i> Required Documents (For LGU Verification)</h3>
                <p style="font-size:0.82rem; color:var(--text-muted); margin-bottom:1rem;">Please provide your document numbers. (Note: File upload functionality will be added later).</p>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group" style="margin-bottom:0;">
                        <label for="bir_number" style="font-weight:600; font-size:0.85rem;">B.I.R. Registration Number <span style="color: red;">*</span></label>
                        <input type="text" id="bir_number" name="bir_number" class="form-control" required placeholder="e.g. 123-456-789-000">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label for="business_permit" style="font-weight:600; font-size:0.85rem;">Business Permit Number <span style="color: red;">*</span></label>
                        <input type="text" id="business_permit" name="business_permit" class="form-control" required placeholder="e.g. BP-2026-1234">
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="name">Business / Spot Name <span style="color: red;">*</span></label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="category_id">Category <span style="color: red;">*</span></label>
                <select id="category_id" name="category_id" class="form-control" required>
                    <option value="">-- Select Category --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Location (Pin Drop) <span style="color: red;">*</span></label>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.75rem;">Select a barangay, search for your location, or click on the map to pin your exact business location.</p>
                
                <!-- Barangay and Location Search Side by Side -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label for="barangay" style="font-weight:600; font-size:0.85rem;">Barangay</label>
                        <select id="barangay" name="barangay" class="form-control">
                            <option value="">-- Select Barangay --</option>
                            <?php
                            $brgys = ['Bacao', 'Basak-bawang', 'Bontong', 'Camanga', 'Candiis', 'Catituan', 'Dansulao', 'Del Pilar', 'Guilawa', 'Kigay', 'La Dicha', 'Lipacan', 'Logpond', 'Mabini', 'Malungon', 'Mulom', 'Overland', 'Palalian', 'Payag', 'Poblacion', 'Rebocon', 'San Vicente', 'Sinusayan', 'Tackling', 'Tigabon'];
                            foreach ($brgys as $b) {
                                echo "<option value=\"$b\">$b</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div style="position: relative;">
                        <label for="location-search" style="font-weight:600; font-size:0.85rem;">Search Location</label>
                        <input type="text" id="location-search" class="form-control" placeholder="Street, landmark, or address..." style="padding: 0.6rem 0.8rem; font-size: 0.88rem;">
                        <div id="autocomplete-list" style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #e2e8f0; border-top: none; border-radius: 0 0 0.5rem 0.5rem; max-height: 200px; overflow-y: auto; display: none; z-index: 10; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"></div>
                    </div>
                </div>

                <!-- Map Picker -->
                <div id="map-picker" style="height: 350px; width: 100%; border-radius: var(--radius-md); border: 1px solid #cbd5e1; margin-bottom: 0.5rem;"></div>
                <input type="hidden" id="latitude" name="latitude" required>
                <input type="hidden" id="longitude" name="longitude" required>
                <div id="latlng-display" style="font-size: 0.85rem; color: var(--text-muted);">Coordinates: None selected</div>
            </div>

            <div class="form-group">
                <label for="description">Description <span style="color: red;">*</span></label>
                <textarea id="description" name="description" class="form-control" rows="4" required placeholder="Describe your tourist spot..."></textarea>
            </div>

            <div class="form-group">
                <label for="contact_number">Contact Number</label>
                <input type="tel" id="contact_number" name="contact_number" class="form-control"
                       placeholder="09XXXXXXXXX"
                       maxlength="11"
                       pattern="[0-9]{10,11}"
                       inputmode="numeric">
                <div style="font-size:0.78rem; color:var(--text-muted); margin-top:0.3rem;">Numbers only, max 11 digits</div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="fb_link">Facebook Link</label>
                    <input type="url" id="fb_link" name="fb_link" class="form-control">
                </div>
                <div class="form-group">
                    <label for="hours">Operating Hours</label>
                    <input type="text" id="hours" name="hours" class="form-control" placeholder="e.g. 8AM - 5PM">
                </div>
                <div class="form-group">
                    <label for="price_range">Price Range</label>
                    <input type="text" id="price_range" name="price_range" class="form-control" placeholder="e.g. ₱100 - ₱500">
                </div>
            </div>

            <div class="form-group">
                <label for="spot_images">Spot Images (Max 10)</label>
                <input type="file" id="spot_images" name="spot_images[]" class="form-control" multiple accept="image/*">
                <div style="font-size:0.85rem; color:var(--text-muted); margin-top:0.3rem;">You can select up to 10 images to upload. First image will be used as the cover.</div>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 1.5rem; width: 100%;">Submit Application</button>
        </form>
    </div>
</div>

<?php if ($swalError): ?>
<script>
window.addEventListener('load', function () {
    Swal.fire({
        icon: 'error',
        title: 'Incomplete Form',
        text: <?= json_encode($swalError) ?>,
        confirmButtonColor: '#166534'
    });
});
</script>
<?php endif; ?>

<?php require_once '../app/views/partials/business-footer.php'; ?>

<script>
// ── Contact number input ──────────────────────────────────────────────────────
const contactInput = document.getElementById('contact_number');
if (contactInput) {
    contactInput.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 11);
    });
    contactInput.addEventListener('keypress', function (e) {
        if (!/[0-9]/.test(e.key)) e.preventDefault();
    });
    contactInput.addEventListener('paste', function (e) {
        e.preventDefault();
        const pasted = (e.clipboardData || window.clipboardData).getData('text');
        this.value = pasted.replace(/\D/g, '').slice(0, 11);
    });
}

// ── Image limit ───────────────────────────────────────────────────────────────
const spotImagesInput = document.getElementById('spot_images');
if (spotImagesInput) {
    spotImagesInput.addEventListener('change', function() {
        if (this.files.length > 10) {
            Swal.fire({ icon: 'warning', title: 'Too Many Images', text: 'You can only upload a maximum of 10 images.', confirmButtonColor: '#166534' });
            this.value = '';
        }
    });
}

// ── Price formatting ──────────────────────────────────────────────────────────
const priceInput = document.querySelector('[name="price_range"]');
if (priceInput) {
    priceInput.addEventListener('blur', function () {
        let val = this.value.trim();
        if (!val) return;
        val = val.replace(/₱/g, '').trim();
        val = val.replace(/(\d[\d,]*\.?\d*)/g, '₱$1');
        this.value = val;
    });
}

// ── Submit confirmation ───────────────────────────────────────────────────────
const applyForm = document.getElementById('apply-form');
if (applyForm) {
    applyForm.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Submit Application?',
            text: 'Ensure all details and documents are correct before submitting.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#166534',
            cancelButtonColor: '#4b5563',
            confirmButtonText: 'Yes, submit it!'
        }).then((result) => {
            if (result.isConfirmed) {
                applyForm.submit();
            }
        });
    });
}

// ── Self-contained Map Picker ─────────────────────────────────────────────────
// Executes immediately since Leaflet and the DOM are already loaded.
function initMapPicker() {
    const mapEl = document.getElementById('map-picker');
    if (!mapEl || typeof L === 'undefined') {
        console.error("Map element or Leaflet (L) is not defined.");
        return;
    }

    // Fix Leaflet default marker icon broken paths & icon anchor positioning
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

    // Street layer
    const streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    });

    // Satellite layer
    const satelliteLayer = L.layerGroup([
        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19,
            attribution: 'Tiles &copy; Esri'
        }),
        L.tileLayer('https://services.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19
        })
    ]);

    streetLayer.addTo(map);
    let currentLayer = 'street';

    // Satellite toggle button
    const toggleBtn = document.createElement('button');
    toggleBtn.type = 'button';
    toggleBtn.innerHTML = '🛰️ Satellite';
    toggleBtn.style.cssText = 'position:absolute;top:10px;right:10px;z-index:999;padding:0.5rem 0.75rem;background:white;border:1px solid #ccc;border-radius:4px;cursor:pointer;font-size:0.85rem;font-weight:600;box-shadow:0 2px 6px rgba(0,0,0,0.2);';
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
    const latInput  = document.getElementById('latitude');
    const lngInput  = document.getElementById('longitude');
    const display   = document.getElementById('latlng-display');

    function updateCoordinates(lat, lng) {
        const rLat = parseFloat(lat).toFixed(8);
        const rLng = parseFloat(lng).toFixed(8);
        if (latInput) latInput.value = rLat;
        if (lngInput) lngInput.value = rLng;
        if (display)  display.innerHTML = 'Coordinates: <em>' + rLat + ', ' + rLng + '</em> <span style="font-size:0.75rem;color:#999;">(Drag marker to adjust)</span>';
    }

    function setMarker(lat, lng, label) {
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], { draggable: true }).addTo(map);
            marker.on('dragend', function() {
                const p = marker.getLatLng();
                updateCoordinates(p.lat, p.lng);
            });
        }
        if (label) marker.bindPopup(label).openPopup();
        updateCoordinates(lat, lng);
    }

    // Click on map to pin
    map.on('click', function(e) {
        setMarker(e.latlng.lat, e.latlng.lng, '📍 Custom Location');
    });

    // Barangay dropdown — no auto-pin or auto-center. User clicks the map to pin.
}

// Initialize immediately or on DOM content loaded / load
if (document.readyState === 'complete' || document.readyState === 'interactive') {
    initMapPicker();
} else {
    window.addEventListener('DOMContentLoaded', initMapPicker);
}
</script>

<?php require_once '../app/views/partials/business-footer.php'; ?>
