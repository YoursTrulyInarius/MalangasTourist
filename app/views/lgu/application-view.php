<?php
$title = 'Review Application - LGU Admin';
$activePage = 'applications';
require_once '../app/views/partials/lgu-header.php';
?>

<!-- Top Bar -->
<div class="lgu-topbar">
    <div>
        <div class="lgu-topbar-title">Review Application</div>
        <div class="lgu-topbar-breadcrumb"><?= htmlspecialchars($application['name']) ?></div>
    </div>
    <a href="<?= BASE_URL ?>/lgu/applications" style="padding: 0.45rem 1rem; border-radius: 0.5rem; border: 1.5px solid #d1d5db; color: #4b5563; text-decoration: none; font-size: 0.88rem; font-weight: 500;">
        <i class="fa-solid fa-arrow-left"></i> Back to Applications
    </a>
</div>

<div class="lgu-page-body">
    <?php
        $statusColors = [
            'pending'        => ['bg' => '#fef3c7', 'text' => '#92400e'],
            'approved'       => ['bg' => '#dcfce7', 'text' => '#166534'],
            'rejected'       => ['bg' => '#fee2e2', 'text' => '#991b1b'],
            'needs_revision' => ['bg' => '#dbeafe', 'text' => '#1e40af'],
        ];
        $sc = $statusColors[$application['status']] ?? ['bg' => '#f3f4f6', 'text' => '#374151'];
    ?>

    <!-- Header card -->
    <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 4px rgba(0,0,0,0.07); padding: 1.5rem 2rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="font-size: 1.4rem; font-weight: 800; color: var(--primary-dark); margin-bottom: 0.3rem;"><?= htmlspecialchars($application['name']) ?></h2>
            <div style="font-size: 0.88rem; color: var(--text-muted);">
                Submitted by <strong><?= htmlspecialchars($application['owner_name']) ?></strong>
                &middot; <?= htmlspecialchars($application['owner_email']) ?>
                <?php if (!empty($application['owner_phone'])): ?>
                    &middot; <?= htmlspecialchars($application['owner_phone']) ?>
                <?php endif; ?>
            </div>
        </div>
        <span style="background: <?= $sc['bg'] ?>; color: <?= $sc['text'] ?>; padding: 0.4rem 1rem; border-radius: 999px; font-size: 0.82rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
            <?= htmlspecialchars($application['status']) ?>
        </span>
    </div>

    <!-- Two-col layout -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">

        <!-- Details -->
        <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 4px rgba(0,0,0,0.07); padding: 1.5rem 2rem;">
            <div style="font-weight: 700; font-size: 0.95rem; color: var(--primary-dark); margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid #f0f0f0;">Application Details</div>
            <table style="width: 100%; border-collapse: collapse;">
                <?php
                    $details = [
                        ['Category',       htmlspecialchars($application['category_name'])],
                        ['Barangay',       htmlspecialchars($application['barangay'] ?: 'N/A')],
                        ['Contact',        htmlspecialchars($application['contact_number'] ?: 'N/A')],
                        ['Operating Hours',htmlspecialchars($application['hours'] ?: 'N/A')],
                        ['Price Range',    htmlspecialchars($application['price_range'] ?: 'N/A')],
                    ];
                    foreach ($details as $row):
                ?>
                    <tr style="border-bottom: 1px solid #f5f5f5;">
                        <td style="padding: 0.6rem 0; font-size: 0.82rem; font-weight: 600; color: var(--text-muted); width: 40%;"><?= $row[0] ?></td>
                        <td style="padding: 0.6rem 0; font-size: 0.88rem; color: var(--primary-dark);"><?= $row[1] ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!empty($application['fb_link'])): ?>
                    <tr style="border-bottom: 1px solid #f5f5f5;">
                        <td style="padding: 0.6rem 0; font-size: 0.82rem; font-weight: 600; color: var(--text-muted);">Social Media</td>
                        <td style="padding: 0.6rem 0;"><a href="<?= htmlspecialchars($application['fb_link']) ?>" target="_blank" style="color: var(--complementary-color); font-size: 0.88rem;">View Facebook Page</a></td>
                    </tr>
                <?php endif; ?>
            </table>
            <?php if (!empty($application['description'])): ?>
                <div style="margin-top: 1.25rem;">
                    <div style="font-size: 0.82rem; font-weight: 600; color: var(--text-muted); margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">Description</div>
                    <div style="background: #f8f9fb; padding: 1rem; border-radius: 0.5rem; font-size: 0.9rem; line-height: 1.6; color: #374151;">
                        <?= nl2br(htmlspecialchars($application['description'])) ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Documents Verification -->
        <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 4px rgba(0,0,0,0.07); padding: 1.5rem 2rem; margin-bottom: 1.5rem;">
            <div style="font-weight: 700; font-size: 0.95rem; color: var(--primary-dark); margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid #f0f0f0;"><i class="fa-solid fa-file-contract"></i> Submitted Documents & Clearances</div>
            <table style="width: 100%; border-collapse: collapse;">
                <tr style="border-bottom: 1px solid #f5f5f5;">
                    <td style="padding: 0.6rem 0; font-size: 0.82rem; font-weight: 600; color: var(--text-muted); width: 40%;">B.I.R. Registration No.</td>
                    <td style="padding: 0.6rem 0; font-size: 0.88rem; color: var(--primary-dark); font-weight: 600; font-family: monospace;"><?= htmlspecialchars($application['bir_number'] ?: 'Not Provided') ?></td>
                </tr>
                <tr style="border-bottom: 1px solid #f5f5f5;">
                    <td style="padding: 0.6rem 0; font-size: 0.82rem; font-weight: 600; color: var(--text-muted);">Business Permit No.</td>
                    <td style="padding: 0.6rem 0; font-size: 0.88rem; color: var(--primary-dark); font-weight: 600; font-family: monospace;"><?= htmlspecialchars($application['business_permit'] ?: 'Not Provided') ?></td>
                </tr>
                <tr>
                    <td style="padding: 0.6rem 0; font-size: 0.82rem; font-weight: 600; color: var(--text-muted);">Other Clearances</td>
                    <td style="padding: 0.6rem 0; font-size: 0.82rem; color: #f59e0b;"><em>File upload feature coming soon</em></td>
                </tr>
            </table>
        </div>

        <!-- Map -->
        <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 4px rgba(0,0,0,0.07); padding: 1.5rem 2rem;">
            <div style="font-weight: 700; font-size: 0.95rem; color: var(--primary-dark); margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid #f0f0f0;">Location Review</div>
            <div id="review-map" style="height: 280px; width: 100%; border-radius: 0.5rem; border: 1.5px solid #e2e8f0; margin-bottom: 0.5rem;"></div>
            <div style="font-size: 0.8rem; color: var(--text-muted);">
                <i class="fa-solid fa-location-crosshairs"></i>
                <?= $application['latitude'] ?>, <?= $application['longitude'] ?>
            </div>
        </div>
    </div>

    <?php if ($pendingPhotoUpdate): ?>
        <section style="background:white;border-radius:0.75rem;box-shadow:0 1px 4px rgba(0,0,0,0.07);padding:1.5rem 2rem;margin-bottom:1.5rem;border-left:4px solid #f59e0b;">
            <div style="font-weight:800;font-size:1.05rem;color:var(--primary-dark);margin-bottom:0.4rem;">
                <i class="fa-regular fa-images" style="color:#b45309;"></i> Photo Update — Pending Review
            </div>
            <p style="font-size:0.88rem;color:#4b5563;margin-bottom:1rem;">The business submitted replacement photos. Approving these photos will replace the current live gallery; rejecting them will leave the current gallery unchanged.</p>
            <?php if (!empty($currentPhotos)): ?>
                <div style="font-size:0.82rem;font-weight:700;color:#4b5563;margin-bottom:0.5rem;">Current live photos</div>
                <div style="display:flex;gap:0.75rem;flex-wrap:wrap;margin-bottom:1rem;">
                    <?php foreach ($currentPhotos as $photo): ?>
                        <a href="<?= BASE_URL ?>/<?= htmlspecialchars($photo['file_path']) ?>" target="_blank" rel="noopener">
                            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($photo['file_path']) ?>" alt="Current live photo" style="width:120px;height:90px;object-fit:cover;border-radius:0.5rem;border:1px solid #e2e8f0;">
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div style="font-size:0.82rem;font-weight:700;color:#4b5563;margin-bottom:0.5rem;">Submitted replacement photos</div>
            <div style="display:flex;gap:0.75rem;flex-wrap:wrap;margin-bottom:1.25rem;">
                <?php foreach ($pendingPhotoUpdate['photos'] as $photo): ?>
                    <a href="<?= BASE_URL ?>/<?= htmlspecialchars($photo['file_path']) ?>" target="_blank" rel="noopener">
                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($photo['file_path']) ?>" alt="Submitted replacement photo" style="width:150px;height:110px;object-fit:cover;border-radius:0.5rem;border:1px solid #e2e8f0;">
                    </a>
                <?php endforeach; ?>
            </div>
            <form method="POST" action="<?= BASE_URL ?>/lgu/applications/<?= (int)$application['id'] ?>/photo-update">
                <label for="photo-update-reason" style="display:block;font-size:0.82rem;font-weight:700;color:#4b5563;margin-bottom:0.4rem;">Rejection reason (required when rejecting)</label>
                <textarea id="photo-update-reason" name="reason" class="form-control" rows="2" style="margin-bottom:0.9rem;" placeholder="Explain why the replacement photos are not suitable."></textarea>
                <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
                    <button class="btn btn-success" type="submit" name="action" value="approve"><i class="fa-solid fa-circle-check"></i> Approve Photo Update</button>
                    <button class="btn btn-danger" type="submit" name="action" value="reject" onclick="return confirm('Reject this photo update? The current live photos will remain unchanged.');"><i class="fa-solid fa-circle-xmark"></i> Reject Photo Update</button>
                </div>
            </form>
        </section>
    <?php endif; ?>

    <!-- Decision Panel (only if pending) -->
    <?php if ($application['status'] === 'pending'): ?>
        <div style="background: white; border-radius: 1rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0,0,0,0.06); padding: 1.75rem 2rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.6rem; font-weight: 800; font-size: 1.05rem; color: var(--primary-dark); margin-bottom: 1.25rem; padding-bottom: 0.85rem; border-bottom: 1.5px solid #f1f5f9;">
                <i class="fa-solid fa-gavel" style="color: var(--primary-color);"></i>
                <span>LGU Application Decision</span>
            </div>
            <form id="review-form" action="<?= BASE_URL ?>/api/approve-reject" method="POST">
                <input type="hidden" name="spot_id" value="<?= $application['id'] ?>">
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; font-weight: 700; font-size: 0.82rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em;">
                        <span>Comments / Rejection Reason</span>
                        <span style="font-weight: 500; font-size: 0.78rem; text-transform: none; color: var(--text-muted);">Required if rejecting · Optional for approval</span>
                    </label>
                    <textarea id="reason" name="reason" class="form-control" rows="3" placeholder="Provide feedback to the applicant or reason for rejection..." style="border: 1.5px solid #cbd5e1; border-radius: 0.75rem; padding: 0.9rem; font-size: 0.95rem; width: 100%; box-shadow: inset 0 1px 2px rgba(0,0,0,0.04);"></textarea>
                </div>
                <div style="display: flex; gap: 1.25rem; flex-wrap: wrap;">
                    <button type="submit" id="btn-approve" name="action" value="approve" class="btn btn-success" style="flex: 1; min-width: 220px; padding: 0.95rem 1.5rem; font-size: 1rem; font-weight: 700; border-radius: 0.75rem;">
                        <i class="fa-solid fa-circle-check"></i> Approve Application
                    </button>
                    <button type="submit" id="btn-reject" name="action" value="reject" class="btn btn-danger" style="flex: 1; min-width: 220px; padding: 0.95rem 1.5rem; font-size: 1rem; font-weight: 700; border-radius: 0.75rem;">
                        <i class="fa-solid fa-circle-xmark"></i> Reject Application
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- Rejection Reason (if already rejected) -->
    <?php if ($application['status'] === 'rejected' && !empty($application['rejection_reason'])): ?>
        <div style="background: #fee2e2; border-radius: 0.75rem; padding: 1.25rem 1.5rem; color: #991b1b;">
            <strong><i class="fa-solid fa-circle-xmark"></i> Rejection Reason:</strong><br>
            <div style="margin-top: 0.5rem;"><?= nl2br(htmlspecialchars($application['rejection_reason'])) ?></div>
        </div>
    <?php endif; ?>

</div><!-- /.lgu-page-body -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const lat = <?= $application['latitude'] ?>;
    const lng = <?= $application['longitude'] ?>;
    const map = L.map('review-map', {
        dragging: false,
        touchZoom: false,
        scrollWheelZoom: false,
        doubleClickZoom: false,
        boxZoom: false,
        keyboard: false,
        zoomControl: false,
        tap: false,
        attributionControl: true,
    }).setView([lat, lng], 16);
    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; Source: Esri'
    }).addTo(map);
    L.tileLayer('https://services.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri'
    }).addTo(map);
    L.marker([lat, lng], { interactive: false }).addTo(map)
        .bindPopup('<b><?= htmlspecialchars(addslashes($application['name'])) ?></b>')
        .openPopup();

    const reviewForm = document.getElementById('review-form');
    const approveBtn = document.getElementById('btn-approve');
    const rejectBtn = document.getElementById('btn-reject');

    function processApplication(action, reason = '') {
        // Step 1: Show loading SweetAlert popup
        Swal.fire({
            title: 'Processing Request...',
            html: 'Please wait a moment while we update the application status and send the email notification to the applicant.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const formData = new FormData();
        formData.append('spot_id', '<?= $application['id'] ?>');
        formData.append('action', action);
        formData.append('reason', reason);

        fetch(`${BASE_URL}/api/approve-reject`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Step 2: Show success SweetAlert when completed
                Swal.fire({
                    icon: 'success',
                    title: 'Email Sent!',
                    text: data.message || `Application has been successfully ${action}d and an email notification has been sent!`,
                    confirmButtonColor: '#166534'
                }).then(() => {
                    window.location.href = `${BASE_URL}/lgu/applications`;
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error || 'Failed to process application.',
                    confirmButtonColor: '#991b1b'
                });
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: 'Network Error',
                text: 'An error occurred while connecting to the server. Please try again.',
                confirmButtonColor: '#991b1b'
            });
        });
    }
    
    if (approveBtn && rejectBtn && reviewForm) {
        approveBtn.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Approve Application?',
                text: 'This will make the spot publicly visible on the map.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#166534',
                cancelButtonColor: '#4b5563',
                confirmButtonText: 'Yes, approve it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    processApplication('approve');
                }
            });
        });

        rejectBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const reason = document.getElementById('reason').value.trim();
            if (!reason) {
                Swal.fire('Error', 'Please provide a rejection reason.', 'error');
                return;
            }
            Swal.fire({
                title: 'Reject Application?',
                text: 'This application will be rejected and sent back to the business owner.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#991b1b',
                cancelButtonColor: '#4b5563',
                confirmButtonText: 'Yes, reject it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    processApplication('reject', reason);
                }
            });
        });
    }
});
</script>

<?php require_once '../app/views/partials/lgu-footer.php'; ?>
