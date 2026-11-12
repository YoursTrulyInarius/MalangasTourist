<?php
$title = 'Manage Listing - Business Panel';
$activePage = 'dashboard';
require_once '../app/views/partials/business-header.php';
?>
<style>
.input-label { display:block; margin-bottom:0.4rem; font-weight:600; font-size:0.85rem; color:var(--primary-dark); text-transform:uppercase; letter-spacing:0.05em; }
.op-status-badge { display:inline-block; padding:0.25rem 0.75rem; border-radius:999px; font-size:0.8rem; font-weight:700; }
</style>

<!-- Top Bar -->
<div class="biz-topbar">
    <div>
        <div class="biz-topbar-title">Manage Listing</div>
        <div class="biz-topbar-breadcrumb"><?= htmlspecialchars($spot['name']) ?></div>
    </div>
    <a href="<?= BASE_URL ?>/business" style="padding:0.45rem 1rem;border-radius:0.5rem;border:1.5px solid #d1d5db;color:#4b5563;text-decoration:none;font-size:0.88rem;font-weight:500;">
        <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
    </a>
</div>

<div class="biz-page-body">

    <?php if ($spot['status'] !== 'approved'): ?>
        <div class="alert alert-error" style="margin-bottom:1.5rem;">
            <i class="fa-solid fa-triangle-exclamation"></i>
            This listing is <strong><?= htmlspecialchars($spot['status']) ?></strong>. You can only update the operational status of <strong>approved</strong> listings. Other changes will be submitted for re-review.
        </div>
    <?php endif; ?>

    <div class="biz-form-panel" style="max-width:820px;margin:0 auto;background:white;border-radius:0.75rem;box-shadow:0 2px 8px rgba(0,0,0,0.08);overflow:hidden;">
        <div class="biz-form-panel-header" style="padding:1.5rem 2rem;border-bottom:1px solid #f0f0f0;background:#f8f9fb;">
            <div style="font-weight:700;font-size:1rem;color:var(--primary-dark);">Update Information</div>
            <div style="font-size:0.8rem;color:var(--text-muted);">Changing the map location pin will reset your listing to <strong>pending</strong> review.</div>
        </div>

        <form class="biz-form-content" action="<?= BASE_URL ?>/business/edit/save" method="POST" enctype="multipart/form-data" style="padding:2rem;">
            <input type="hidden" name="spot_id" value="<?= $spot['id'] ?>">

            <?php if ($pendingPhotoUpdate): ?>
                <div style="background:#fffbeb;border:1px solid #fcd34d;border-radius:0.5rem;padding:1rem;margin-bottom:1.5rem;color:#92400e;">
                    <strong><i class="fa-solid fa-hourglass-half"></i> Photo update pending LGU approval</strong>
                    <div style="font-size:0.85rem;margin-top:0.3rem;">Your replacement photos have been submitted. The current photos remain visible until the LGU reviews and approves this update.</div>
                </div>
            <?php endif; ?>

            <!-- Operational Status - highlighted at top -->
            <div style="background:#f0fdf4;border:1.5px solid #bbf7d0;border-radius:0.5rem;padding:1.25rem;margin-bottom:1.5rem;">
                <label class="input-label" style="color:#166534;"><i class="fa-solid fa-toggle-on"></i> Operational Status</label>
                <?php
                    $monitorStatus = $spot['monitor_status'] ?? 'normal';
                    $selectedStatus = ($spot['operational_status'] ?? 'open');
                    if ($monitorStatus === 'under_renovation') {
                        $selectedStatus = 'maintenance';
                    }
                ?>
                <select name="operational_status" class="form-control" style="max-width:350px;" <?= $monitorStatus === 'under_renovation' ? 'disabled' : '' ?>>
                    <?php
                        $statuses = ['open' => '🟢 Open', 'closed' => '🔴 Closed', 'temp_closed' => '🟡 Temporarily Closed', 'maintenance' => '🔵 Under Renovation'];
                        foreach ($statuses as $val => $label):
                            $isDisabled = ($monitorStatus === 'under_renovation' && $val === 'open');
                    ?>
                        <option value="<?= $val ?>" <?= $selectedStatus === $val ? 'selected' : '' ?> <?= $isDisabled ? 'disabled' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($monitorStatus === 'under_renovation'): ?>
                    <input type="hidden" name="operational_status" value="maintenance">
                    <div style="font-size:0.78rem;color:#166534;margin-top:0.5rem;"><i class="fa-solid fa-circle-info"></i> This listing is currently under renovation. It will stay in this state until the LGU approves the repair update and reopens it.</div>
                <?php else: ?>
                    <div style="font-size:0.78rem;color:#166534;margin-top:0.4rem;">This updates instantly for visitors without requiring LGU re-approval.</div>
                <?php endif; ?>
            </div>

            <div style="margin-bottom:1.25rem;">
                <label class="input-label">Description</label>
                <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($spot['description'] ?? '') ?></textarea>
            </div>

            <div class="biz-edit-grid" style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1.25rem;margin-bottom:1.25rem;">
                <div>
                    <label class="input-label">Barangay</label>
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
                <div>
                    <label class="input-label">Contact Number</label>
                    <input type="tel" id="contact_number" name="contact_number" class="form-control" value="<?= htmlspecialchars($spot['contact_number'] ?? '') ?>" placeholder="09XXXXXXXXX" maxlength="11" inputmode="numeric">
                </div>
            </div>

            <div class="biz-edit-grid" style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1.25rem;margin-bottom:1.25rem;">
                <div>
                    <label class="input-label">Operating Hours</label>
                    <input type="text" name="hours" class="form-control" value="<?= htmlspecialchars($spot['hours'] ?? '') ?>" placeholder="e.g. 8AM – 5PM">
                </div>
                <div>
                    <label class="input-label">Price Range</label>
                    <input type="text" name="price_range" class="form-control" value="<?= htmlspecialchars($spot['price_range'] ?? '') ?>" placeholder="e.g. ₱100 – ₱500">
                </div>
            </div>

            <div style="margin-bottom:1.25rem;">
                <label class="input-label">Facebook Link</label>
                <input type="url" name="fb_link" class="form-control" value="<?= htmlspecialchars($spot['fb_link'] ?? '') ?>" placeholder="https://facebook.com/...">
            </div>

            <?php if ($spot['source'] === 'business' && $spot['status'] === 'approved'): ?>
                <div style="margin-bottom:1.5rem;padding:1.25rem;border:1px solid #e2e8f0;border-radius:0.5rem;">
                    <label class="input-label"><i class="fa-regular fa-images"></i> Listing Photos</label>
                    <?php if (!empty($photos)): ?>
                        <div style="display:flex;gap:0.6rem;flex-wrap:wrap;margin:0.75rem 0;">
                            <?php foreach ($photos as $photo): ?>
                                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($photo['file_path']) ?>" alt="Current listing photo" style="width:86px;height:70px;object-fit:cover;border-radius:0.4rem;border:1px solid #e2e8f0;">
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p style="font-size:0.82rem;color:var(--text-muted);margin:0.5rem 0;">No current photos.</p>
                    <?php endif; ?>
                    <label for="spot_images" class="input-label" style="margin-top:1rem;">Submit Replacement Photos (up to 10)</label>
                    <input type="file" id="spot_images" name="spot_images[]" class="form-control" multiple accept="image/jpeg,image/png,image/webp" <?= $pendingPhotoUpdate ? 'disabled' : '' ?>>
                    <div style="font-size:0.82rem;color:var(--text-muted);margin-top:0.4rem;">Submitting new photos will replace the current gallery only after LGU approval. Until then, your existing photos remain live.</div>
                </div>
            <?php endif; ?>

            <div style="margin-bottom:1.25rem;">
                <label class="input-label">Location Pin — <span style="color:#ef4444;font-size:0.78rem;">Moving the pin requires LGU re-approval</span></label>
                <p style="font-size:0.82rem;color:var(--text-muted);margin-bottom:0.5rem;"><i class="fa-solid fa-circle-info"></i> Click on the map to update your establishment's pin.</p>
                <div id="map-picker" style="height:320px;width:100%;border-radius:0.5rem;border:1.5px solid #e2e8f0;margin-bottom:0.5rem;"></div>
                <input type="hidden" id="latitude"  name="latitude"  value="<?= $spot['latitude'] ?>" required>
                <input type="hidden" id="longitude" name="longitude" value="<?= $spot['longitude'] ?>" required>
                <div id="latlng-display" style="font-size:0.82rem;color:var(--text-muted);">
                    Current: <em><?= $spot['latitude'] ?>, <?= $spot['longitude'] ?></em>
                </div>
            </div>

            <div class="biz-form-actions" style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid #f0f0f0;display:flex;gap:1rem;">
                <button type="submit" class="btn btn-primary" style="padding:0.85rem 2.5rem;font-size:1rem;font-weight:700;">
                    <i class="fa-solid fa-floppy-disk"></i> Save Changes
                </button>
                <a href="<?= BASE_URL ?>/business" class="btn" style="padding:0.85rem 2rem;font-size:1rem;background:#f3f4f6;color:#374151;">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script src="<?= BASE_URL ?>/assets/js/map-picker.js"></script>
<script>
const contactInput = document.getElementById('contact_number');
if (contactInput) {
    contactInput.addEventListener('input', function () { this.value = this.value.replace(/\D/g, '').slice(0, 11); });
    contactInput.addEventListener('keypress', function (e) { if (!/[0-9]/.test(e.key)) e.preventDefault(); });
}

const spotImagesInput = document.getElementById('spot_images');
if (spotImagesInput) {
    spotImagesInput.addEventListener('change', function () {
        if (this.files.length > 10) {
            window.alert('You can submit up to 10 replacement photos.');
            this.value = '';
        }
    });
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

<?php require_once '../app/views/partials/business-footer.php'; ?>
