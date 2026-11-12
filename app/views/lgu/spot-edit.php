<?php
$title      = 'Edit Spot - LGU Admin';
$activePage = 'spots';
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
        <div class="lgu-topbar-title">Edit Spot</div>
        <div class="lgu-topbar-breadcrumb"><?= htmlspecialchars($spot['name']) ?></div>
    </div>
    <a href="<?= BASE_URL ?>/lgu/spots" style="padding:0.45rem 1rem;border-radius:0.5rem;border:1.5px solid #d1d5db;color:#4b5563;text-decoration:none;font-size:0.88rem;font-weight:500;">
        <i class="fa-solid fa-arrow-left"></i> Back to Spots
    </a>
</div>

<div class="lgu-page-body">
    <div style="max-width:820px;margin:0 auto;background:white;border-radius:0.75rem;box-shadow:0 1px 4px rgba(0,0,0,0.07);overflow:hidden;">
        <div style="padding:1.5rem 2rem;border-bottom:1px solid #f0f0f0;background:#f8f9fb;">
            <div style="font-weight:700;font-size:1rem;color:var(--primary-dark);">Spot Information</div>
            <div style="font-size:0.8rem;color:var(--text-muted);">Fields marked with * are required.</div>
        </div>

        <form action="<?= BASE_URL ?>/lgu/spots/update" method="POST" style="padding:2rem;">
            <input type="hidden" name="spot_id" value="<?= $spot['id'] ?>">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                <div>
                    <label style="display:block;margin-bottom:0.4rem;font-weight:600;font-size:0.82rem;color:var(--primary-dark);text-transform:uppercase;letter-spacing:0.05em;">Spot Name <span style="color:red;">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($spot['name']) ?>" required>
                </div>
                <div>
                    <label style="display:block;margin-bottom:0.4rem;font-weight:600;font-size:0.82rem;color:var(--primary-dark);text-transform:uppercase;letter-spacing:0.05em;">Category <span style="color:red;">*</span></label>
                    <select name="category_id" class="form-control" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $spot['category_id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-top:1.25rem;">
                <div>
                    <label style="display:block;margin-bottom:0.4rem;font-weight:600;font-size:0.82rem;color:var(--primary-dark);text-transform:uppercase;letter-spacing:0.05em;">Operational Status</label>
                    <select name="operational_status" class="form-control">
                        <?php
                            $statuses = ['open' => 'Open', 'closed' => 'Closed', 'temp_closed' => 'Temporarily Closed', 'maintenance' => 'Under Maintenance'];
                            foreach ($statuses as $val => $label):
                        ?>
                            <option value="<?= $val ?>" <?= ($spot['operational_status'] ?? 'open') === $val ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="display:block;margin-bottom:0.4rem;font-weight:600;font-size:0.82rem;color:var(--primary-dark);text-transform:uppercase;letter-spacing:0.05em;">Barangay</label>
                    <select id="barangay" name="barangay" class="form-control">
                        <option value="">-- Select Barangay --</option>
                        <?php
                        $brgys = ['Bacao', 'Basak-bawang', 'Bontong', 'Camanga', 'Candiis', 'Catituan', 'Dansulao', 'Del Pilar', 'Guilawa', 'Kigay', 'La Dicha', 'Lipacan', 'Logpond', 'Mabini', 'Malungon', 'Mulom', 'Overland', 'Palalian', 'Payag', 'Poblacion', 'Rebocon', 'San Vicente', 'Sinusayan', 'Tackling', 'Tigabon'];
                        $selected = $spot['barangay'] ?? '';
                        foreach ($brgys as $b) {
                            $sel = ($selected === $b) ? 'selected' : '';
                            echo "<option value=\"$b\" $sel>$b</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div style="margin-top:1.25rem;">
                <label style="display:block;margin-bottom:0.4rem;font-weight:600;font-size:0.82rem;color:var(--primary-dark);text-transform:uppercase;letter-spacing:0.05em;">Location (Pin Drop) <span style="color:red;">*</span></label>
                <p style="font-size:0.82rem;color:var(--text-muted);margin-bottom:0.5rem;"><i class="fa-solid fa-circle-info"></i> Click on the map to update the pin location.</p>
                <div id="map-picker" style="height:340px;width:100%;border-radius:0.5rem;border:1.5px solid #e2e8f0;margin-bottom:0.5rem;"></div>
                <input type="hidden" id="latitude"  name="latitude"  value="<?= $spot['latitude'] ?>" required>
                <input type="hidden" id="longitude" name="longitude" value="<?= $spot['longitude'] ?>" required>
                <div id="latlng-display" style="font-size:0.82rem;color:var(--text-muted);padding:0.4rem 0;">
                    Coordinates: <em><?= $spot['latitude'] ?>, <?= $spot['longitude'] ?></em>
                </div>
            </div>

            <div style="margin-top:1.25rem;">
                <label style="display:block;margin-bottom:0.4rem;font-weight:600;font-size:0.82rem;color:var(--primary-dark);text-transform:uppercase;letter-spacing:0.05em;">Description</label>
                <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($spot['description'] ?? '') ?></textarea>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-top:1.25rem;">
                <div>
                    <label style="display:block;margin-bottom:0.4rem;font-weight:600;font-size:0.82rem;color:var(--primary-dark);text-transform:uppercase;letter-spacing:0.05em;">Contact Number</label>
                    <input type="tel" id="contact_number" name="contact_number" class="form-control" value="<?= htmlspecialchars($spot['contact_number'] ?? '') ?>" placeholder="09XXXXXXXXX" maxlength="11" inputmode="numeric">
                </div>
                <div>
                    <label style="display:block;margin-bottom:0.4rem;font-weight:600;font-size:0.82rem;color:var(--primary-dark);text-transform:uppercase;letter-spacing:0.05em;">Operating Hours</label>
                    <input type="text" name="hours" class="form-control" value="<?= htmlspecialchars($spot['hours'] ?? '') ?>" placeholder="e.g. 8AM – 5PM">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-top:1.25rem;">
                <div>
                    <label style="display:block;margin-bottom:0.4rem;font-weight:600;font-size:0.82rem;color:var(--primary-dark);text-transform:uppercase;letter-spacing:0.05em;">Facebook Link</label>
                    <input type="url" name="fb_link" class="form-control" value="<?= htmlspecialchars($spot['fb_link'] ?? '') ?>" placeholder="https://facebook.com/...">
                </div>
                <div>
                    <label style="display:block;margin-bottom:0.4rem;font-weight:600;font-size:0.82rem;color:var(--primary-dark);text-transform:uppercase;letter-spacing:0.05em;">Price Range</label>
                    <input type="text" name="price_range" class="form-control" value="<?= htmlspecialchars($spot['price_range'] ?? '') ?>" placeholder="e.g. ₱100 – ₱500">
                </div>
            </div>

            <div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid #f0f0f0;display:flex;gap:1rem;">
                <button type="submit" class="btn btn-primary" style="padding:0.85rem 2.5rem;font-size:1rem;font-weight:700;">
                    <i class="fa-solid fa-floppy-disk"></i> Save Changes
                </button>
                <a href="<?= BASE_URL ?>/lgu/spots" class="btn" style="padding:0.85rem 2rem;font-size:1rem;background:#f3f4f6;color:#374151;">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../app/views/partials/lgu-footer.php'; ?>
<script src="<?= BASE_URL ?>/assets/js/map-picker.js"></script>
<script>
const contactInput = document.getElementById('contact_number');
if (contactInput) {
    contactInput.addEventListener('input', function () { this.value = this.value.replace(/\D/g, '').slice(0, 11); });
    contactInput.addEventListener('keypress', function (e) { if (!/[0-9]/.test(e.key)) e.preventDefault(); });
}

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
