<?php
$title = 'Add Official Spot - LGU Admin';
$activePage = 'spot-create';
$swalError = '';
if (!empty($_SESSION['swal_error'])) {
    $swalError = $_SESSION['swal_error'];
    unset($_SESSION['swal_error']);
}
require_once '../app/views/partials/lgu-header.php';
?>

<!-- Top Bar -->
<div class="lgu-topbar">
    <div>
        <div class="lgu-topbar-title">Add Official Tourist Spot</div>
        <div class="lgu-topbar-breadcrumb">Create a new LGU-verified spot on the map</div>
    </div>
    <a href="<?= BASE_URL ?>/lgu/spots" style="padding: 0.45rem 1rem; border-radius: 0.5rem; border: 1.5px solid #d1d5db; color: #4b5563; text-decoration: none; font-size: 0.88rem; font-weight: 500;">
        <i class="fa-solid fa-arrow-left"></i> Back to Spots
    </a>
</div>

<div class="lgu-page-body">
    <div style="max-width: 820px; margin: 0 auto; background: white; border-radius: 0.75rem; box-shadow: 0 1px 4px rgba(0,0,0,0.07); overflow: hidden;">
        <div style="padding: 1.5rem 2rem; border-bottom: 1px solid #f0f0f0; background: #f8f9fb;">
            <div style="font-weight: 700; font-size: 1rem; color: var(--primary-dark);">Spot Information</div>
            <div style="font-size: 0.8rem; color: var(--text-muted);">All fields marked with * are required.</div>
        </div>

        <form action="<?= BASE_URL ?>/lgu/spots/store" method="POST" enctype="multipart/form-data" style="padding: 2rem;">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="display:block; margin-bottom:0.4rem; font-weight:600; font-size:0.82rem; color:var(--primary-dark); text-transform:uppercase; letter-spacing:0.05em;">Spot Name <span style="color:red;">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" required placeholder="e.g. Igat Island">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="display:block; margin-bottom:0.4rem; font-weight:600; font-size:0.82rem; color:var(--primary-dark); text-transform:uppercase; letter-spacing:0.05em;">Category <span style="color:red;">*</span></label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        <option value="">-- Select Category --</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div style="margin-top: 1.25rem;">
                <label style="display:block; margin-bottom:0.4rem; font-weight:600; font-size:0.82rem; color:var(--primary-dark); text-transform:uppercase; letter-spacing:0.05em;">Location (Pin Drop) <span style="color:red;">*</span></label>
                <p style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 0.75rem;"><i class="fa-solid fa-circle-info"></i> Select a barangay, search for your location, or click on the map to pin your exact spot.</p>
                
                <!-- Barangay and Location Search Side by Side -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display:block; margin-bottom:0.4rem; font-weight:600; font-size:0.82rem; color:var(--primary-dark); text-transform:uppercase; letter-spacing:0.05em;">Barangay</label>
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
                        <label style="display:block; margin-bottom:0.4rem; font-weight:600; font-size:0.82rem; color:var(--primary-dark); text-transform:uppercase; letter-spacing:0.05em;">Search Location</label>
                        <input type="text" id="location-search" class="form-control" placeholder="Street, landmark, or address..." style="padding: 0.6rem 0.8rem; font-size: 0.88rem;">
                        <div id="autocomplete-list" style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #e2e8f0; border-top: none; border-radius: 0 0 0.5rem 0.5rem; max-height: 200px; overflow-y: auto; display: none; z-index: 10; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"></div>
                    </div>
                </div>

                <!-- Map Picker -->
                <div id="map-picker" style="height: 380px; width: 100%; border-radius: 0.5rem; border: 1.5px solid #e2e8f0; margin-bottom: 0.5rem;"></div>
                <input type="hidden" id="latitude" name="latitude" required>
                <input type="hidden" id="longitude" name="longitude" required>
                <div id="latlng-display" style="font-size: 0.82rem; color: var(--text-muted); padding: 0.4rem 0;">Coordinates: <em>None selected yet</em></div>
            </div>

            <div style="margin-top: 1.25rem;">
                <label style="display:block; margin-bottom:0.4rem; font-weight:600; font-size:0.82rem; color:var(--primary-dark); text-transform:uppercase; letter-spacing:0.05em;">Description</label>
                <textarea id="description" name="description" class="form-control" rows="4" placeholder="Describe the tourist spot..."></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-top: 1.25rem;">
                <div>
                    <label style="display:block; margin-bottom:0.4rem; font-weight:600; font-size:0.82rem; color:var(--primary-dark); text-transform:uppercase; letter-spacing:0.05em;">Contact Number</label>
                    <input type="tel" id="contact_number" name="contact_number" class="form-control"
                           placeholder="09XXXXXXXXX"
                           maxlength="11"
                           pattern="[0-9]{10,11}"
                           inputmode="numeric">
                    <div style="font-size:0.78rem; color:var(--text-muted); margin-top:0.3rem;">Numbers only, max 11 digits</div>
                </div>
                <div>
                    <label style="display:block; margin-bottom:0.4rem; font-weight:600; font-size:0.82rem; color:var(--primary-dark); text-transform:uppercase; letter-spacing:0.05em;">Operating Hours</label>
                    <input type="text" id="hours" name="hours" class="form-control" placeholder="e.g. 8AM – 5PM">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-top: 1.25rem;">
                <div>
                    <label style="display:block; margin-bottom:0.4rem; font-weight:600; font-size:0.82rem; color:var(--primary-dark); text-transform:uppercase; letter-spacing:0.05em;">Facebook Link</label>
                    <input type="url" id="fb_link" name="fb_link" class="form-control" placeholder="https://facebook.com/...">
                </div>
                <div>
                    <label style="display:block; margin-bottom:0.4rem; font-weight:600; font-size:0.82rem; color:var(--primary-dark); text-transform:uppercase; letter-spacing:0.05em;">Price Range</label>
                    <input type="text" id="price_range" name="price_range" class="form-control" placeholder="e.g. ₱100 – ₱500">
                </div>
            </div>

            <div style="margin-top: 1.25rem;">
                <label style="display:block; margin-bottom:0.4rem; font-weight:600; font-size:0.82rem; color:var(--primary-dark); text-transform:uppercase; letter-spacing:0.05em;">Spot Images (Max 10)</label>
                <input type="file" id="spot_images" name="spot_images[]" class="form-control" multiple accept="image/*">
                <div style="font-size:0.78rem; color:var(--text-muted); margin-top:0.3rem;">You can select up to 10 images. The first image will be used as the cover photo.</div>
            </div>

            <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #f0f0f0;">
                <button type="submit" class="btn btn-primary" style="padding: 0.85rem 2.5rem; font-size: 1rem; font-weight: 700;">
                    <i class="fa-solid fa-map-pin"></i> Save Official Spot
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../app/views/partials/lgu-footer.php'; ?>
<script src="<?= BASE_URL ?>/assets/js/map-picker.js"></script>
<script>
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

const spotImagesInput = document.getElementById('spot_images');
if (spotImagesInput) {
    spotImagesInput.addEventListener('change', function(e) {
        if (this.files.length > 10) {
            Swal.fire({ icon: 'warning', title: 'Too Many Images', text: 'You can only upload a maximum of 10 images.', confirmButtonColor: '#166534' });
            this.value = '';
        }
    });
}

const priceInput = document.querySelector('[name="price_range"]');
if (priceInput) {
    priceInput.addEventListener('blur', function () {
        let val = this.value.trim();
        if (!val) return;
        // Add ₱ to any number that doesn't already have it
        val = val.replace(/₱/g, '').trim();
        val = val.replace(/(\d[\d,]*\.?\d*)/g, '₱$1');
        this.value = val;
    });
}
</script>

<?php if ($swalError): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        icon: 'error',
        title: 'Incomplete Form',
        text: <?= json_encode($swalError) ?>,
        confirmButtonColor: '#166534'
    });
});
</script>
<?php endif; ?>
