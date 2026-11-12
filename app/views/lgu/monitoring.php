<?php
$title = 'Spot Monitoring - LGU';
$activePage = 'monitoring';
$monitorList = $monitorList ?? [];
require_once '../app/views/partials/lgu-header.php';
?>

<style>
    .monitor-wrap {
        display: grid;
        gap: 1.5rem;
    }

    .monitor-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 0.5rem;
    }

    .monitor-header h1 {
        margin: 0;
        color: var(--primary-dark);
        font-size: clamp(2rem, 2.5vw, 2.6rem);
        letter-spacing: -0.04em;
    }

    .monitor-header p {
        margin: 0.4rem 0 0;
        color: var(--text-muted);
        font-size: 0.96rem;
    }

    .monitor-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
    }

    .monitor-stat {
        background: white;
        border-radius: 14px;
        padding: 1.2rem 1.25rem;
        border: 1px solid #edf0ee;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.04);
    }

    .monitor-stat .label {
        display: block;
        color: var(--text-muted);
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .monitor-stat .value {
        display: block;
        margin-top: 0.55rem;
        font-size: 2rem;
        font-weight: 800;
        color: var(--primary-dark);
    }

    .monitor-table-wrap {
        background: white;
        border-radius: 16px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.04);
        border: 1px solid #edf0ee;
        overflow: hidden;
    }

    .monitor-table-wrap .table-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #edf0ee;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
    }

    .monitor-table-wrap .table-header h2 {
        margin: 0;
        font-size: 1.1rem;
        color: var(--primary-dark);
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        border-radius: 999px;
        padding: 0.32rem 0.72rem;
        font-size: 0.76rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .status-normal { background: #dcfce7; color: #166534; }
    .status-warning { background: #fef3c7; color: #92400e; }
    .status-renovation { background: #dbeafe; color: #1d4ed8; }
    .status-improved { background: #e0f2fe; color: #0f766e; }

    .table-responsive {
        overflow-x: auto;
    }

    .monitor-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 980px;
    }

    .monitor-table th,
    .monitor-table td {
        padding: 1rem 1.1rem;
        border-bottom: 1px solid #edf0ee;
        text-align: left;
        vertical-align: top;
    }

    .monitor-table thead th {
        background: #f8fafc;
        font-size: 0.72rem;
        letter-spacing: 0.08em;
        color: var(--text-muted);
        text-transform: uppercase;
    }

    .monitor-table td {
        color: var(--text-main);
        font-size: 0.92rem;
    }

    .monitor-table tr.warning-row {
        background: #fff7f7;
        box-shadow: inset 3px 0 0 #dc2626;
    }

    .monitor-table tr.warning-row td {
        background: #fff7f7;
    }

    .monitor-table tr.notification-read {
        background: white;
        box-shadow: none;
    }

    .monitor-table tr.notification-read td {
        background: white;
    }

    .warning-alert {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
        border-radius: 999px;
        padding: 0.32rem 0.72rem;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .monitor-table .spot-name {
        font-weight: 700;
        color: var(--primary-dark);
    }

    .latest-update-box {
        background: white;
        border: 1px solid #fde2e2;
        border-radius: 10px;
        padding: 0.65rem 0.75rem;
        color: var(--text-main);
        line-height: 1.5;
    }

    .latest-update-label {
        display: block;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        color: #991b1b;
        text-transform: uppercase;
        margin-bottom: 0.3rem;
    }

    .monitor-note {
        max-width: 260px;
        color: var(--text-muted);
    }

    .quick-review-grid {
        display: grid;
        gap: 0.55rem;
        min-width: 200px;
    }

    .quick-review-grid .mini-btn {
        width: 100%;
        justify-content: center;
    }

    .detail-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 2000;
        padding: 1rem;
    }

    .detail-modal-backdrop.visible {
        display: flex;
    }

    .detail-modal {
        width: min(900px, 100%);
        max-height: 85vh;
        overflow: auto;
        background: white;
        border-radius: 18px;
        box-shadow: 0 25px 60px rgba(15, 23, 42, 0.28);
        border: 1px solid #edf0ee;
    }

    .detail-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #edf0ee;
    }

    .detail-modal-header h3 {
        margin: 0;
        color: var(--primary-dark);
        font-size: 1.35rem;
    }

    .detail-modal-close {
        border: none;
        background: #f3f4f6;
        color: var(--primary-dark);
        width: 38px;
        height: 38px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 1.1rem;
    }

    .detail-modal-body {
        padding: 1.5rem;
        display: grid;
        gap: 1rem;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
    }

    .detail-box {
        background: #f8fafc;
        border: 1px solid #edf0ee;
        border-radius: 12px;
        padding: 0.9rem 1rem;
    }

    .detail-box .label {
        display: block;
        color: var(--text-muted);
        font-size: 0.72rem;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        font-weight: 700;
        margin-bottom: 0.4rem;
    }

    .detail-box .value {
        color: var(--text-main);
        font-weight: 600;
    }

    .detail-section {
        border-top: 1px solid #edf0ee;
        padding-top: 1rem;
    }

    .detail-section h4 {
        margin: 0 0 0.8rem;
        color: var(--primary-dark);
        font-size: 0.9rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .monitor-review-form {
        display: grid;
        gap: 0.9rem;
        margin-top: 0.8rem;
    }

    .monitor-review-form textarea {
        width: 100%;
        min-height: 110px;
        border: 1px solid #dfe7e1;
        border-radius: 10px;
        padding: 0.8rem 0.9rem;
        font: inherit;
        resize: vertical;
    }

    .monitor-review-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .detail-note {
        background: #fff7f7;
        border: 1px solid #fecaca;
        border-radius: 12px;
        padding: 1rem;
        color: var(--text-main);
        white-space: pre-line;
        line-height: 1.7;
    }

    .detail-history {
        display: grid;
        gap: 0.8rem;
    }

    .detail-history-item {
        background: #f8fafc;
        border: 1px solid #edf0ee;
        border-radius: 12px;
        padding: 0.9rem 1rem;
    }

    .detail-history-item strong {
        color: var(--primary-dark);
    }

    .btn-warning {
        background: #f59e0b;
        color: white;
    }

    .btn-warning:hover {
        background: #d97706;
    }

    @media (max-width: 700px) {
        .monitor-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }

    /* Proof gallery inside modal */
    .proof-grid-modal {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 0.75rem;
        margin-top: 0.5rem;
    }

    .proof-grid-modal .proof-thumb {
        border: 1px solid #edf0ee;
        border-radius: 10px;
        overflow: hidden;
        background: #f8fafc;
    }

    .proof-grid-modal .proof-thumb a {
        display: block;
        text-decoration: none;
    }

    .proof-grid-modal .proof-thumb img {
        display: block;
        width: 100%;
        height: 160px;
        object-fit: cover;
        transition: opacity 0.2s ease;
    }

    .proof-grid-modal .proof-thumb img:hover {
        opacity: 0.85;
    }
</style>

<div class="lgu-topbar">
    <div>
        <div class="lgu-topbar-title">Monitoring</div>
        <div class="lgu-topbar-breadcrumb">Tourist spot quality alerts and follow-up actions</div>
    </div>
</div>

<div class="lgu-page-body">
    <div class="monitor-wrap">
        <div class="monitor-header">
            <div>
                <h1>Spot Monitoring Dashboard</h1>
                <p>Low-rated listings are tracked here for warning and improvement follow-up.</p>
            </div>
        </div>

        <?php
            $warningCount = 0;
            $renovationCount = 0;
            $improvedCount = 0;
            foreach ($monitorList as $spot) {
                if (($spot['monitor_status'] ?? 'normal') === 'warning') $warningCount++;
                if (($spot['monitor_status'] ?? 'normal') === 'under_renovation') $renovationCount++;
                if (($spot['monitor_status'] ?? 'normal') === 'improved') $improvedCount++;
            }
        ?>

        <div class="monitor-stats">
            <div class="monitor-stat">
                <span class="label">Flagged</span>
                <span class="value"><?= $warningCount ?></span>
            </div>
            <div class="monitor-stat">
                <span class="label">Under Renovation</span>
                <span class="value"><?= $renovationCount ?></span>
            </div>
            <div class="monitor-stat">
                <span class="label">Improved</span>
                <span class="value"><?= $improvedCount ?></span>
            </div>
        </div>

        <div class="monitor-table-wrap">
            <div class="table-header">
                <h2>Alert List</h2>
            </div>

            <div class="table-responsive">
                <table class="monitor-table">
                    <thead>
                        <tr>
                            <th>Spot</th>
                            <th>Category</th>
                            <th>Owner</th>
                            <th>Rating</th>
                            <th>Status</th>
                            <th>Deadline</th>
                            <th>Note</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($monitorList)): ?>
                            <tr>
                                <td colspan="8" style="padding: 1.5rem; text-align: center; color: var(--text-muted);">
                                    No monitored tourist spots at the moment.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($monitorList as $spot): ?>
                                <?php
                                    $status = $spot['monitor_status'] ?? 'normal';
                                    $statusClass = 'status-normal';
                                    $statusLabel = 'Normal';
                                    if ($status === 'warning') { $statusClass = 'status-warning'; $statusLabel = 'Warning'; }
                                    elseif ($status === 'under_renovation') { $statusClass = 'status-renovation'; $statusLabel = 'Renovation'; }
                                    elseif ($status === 'improved') { $statusClass = 'status-improved'; $statusLabel = 'Improved'; }

                                    $hasNotification = $status === 'warning'
                                        || $status === 'under_renovation'
                                        || ($spot['monitor_submission_status'] ?? 'none') === 'pending'
                                        || !empty($spot['monitor_note'])
                                        || !empty($spot['renovation_note']);

                                    $latestOwnerUpdate = trim((string)($spot['renovation_note'] ?? ''));
                                    $latestOwnerUpdateText = $latestOwnerUpdate !== ''
                                        ? preg_replace('/\s+/', ' ', $latestOwnerUpdate)
                                        : 'No owner update submitted yet.';
                                    $latestOwnerUpdatePreview = mb_strlen($latestOwnerUpdateText) > 120 ? mb_substr($latestOwnerUpdateText, 0, 117) . '...' : $latestOwnerUpdateText;
                                ?>
                                <tr class="<?= $hasNotification ? 'warning-row' : '' ?>">
                                    <td>
                                        <div style="display: flex; flex-direction: column; gap: 0.45rem;">
                                            <div class="spot-name"><?= htmlspecialchars($spot['name'] ?? '') ?></div>
                                            <?php if ($hasNotification): ?>
                                                <div class="warning-alert">
                                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                                    Notification
                                                </div>
                                            <?php endif; ?>
                                            <div style="font-size: 0.8rem; color: var(--text-muted);">
                                                <?= htmlspecialchars($spot['barangay'] ?? '') ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($spot['category_name'] ?? $spot['category_id'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($spot['owner_name'] ?? 'Unassigned') ?></td>
                                    <td>
                                        <?= !empty($spot['avg_rating']) ? number_format((float)$spot['avg_rating'], 1) . ' / 5' : 'No ratings yet' ?>
                                        <div style="font-size: 0.75rem; color: var(--text-muted);">
                                            <?= (int)($spot['review_count'] ?? 0) ?> review(s)
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-pill <?= $statusClass ?>"><?= $statusLabel ?></span>
                                    </td>
                                    <td>
                                        <?= !empty($spot['improvement_deadline']) ? date('M d, Y', strtotime($spot['improvement_deadline'])) : '—' ?>
                                    </td>
                                    <td class="monitor-note">
                                        <div class="latest-update-box">
                                            <span class="latest-update-label">Latest update</span>
                                            <?= htmlspecialchars($latestOwnerUpdatePreview) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="quick-review-grid">
                                            <button type="button" class="btn btn-secondary view-detail-btn mini-btn"
                                                data-spot='<?= htmlspecialchars(json_encode($spot, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8') ?>'>
                                                <i class="fa-solid fa-eye"></i> View Details
                                            </button>

                                            <?php if ($status !== 'improved'): ?>
                                                <form method="post" action="<?= BASE_URL ?>/lgu/monitoring/<?= (int)($spot['id'] ?? 0) ?>/warn" class="warn-owner-form" style="display: grid; gap: 0.6rem;">
                                                    <textarea name="note" rows="2" required placeholder="Add warning note for the owner..." style="width: 100%; border: 1px solid #dfe7e1; border-radius: 10px; padding: 0.63rem 0.7rem; font: inherit; resize: vertical; min-height: 70px; background: white;"></textarea>
                                                    <button type="submit" class="btn btn-warning mini-btn">
                                                        <i class="fa-solid fa-bell"></i> Warn Owner
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span style="color: var(--text-muted); font-size: 0.8rem;">Monitoring complete</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/views/partials/business-footer.php'; ?>

<div id="monitorDetailModal" class="detail-modal-backdrop" aria-hidden="true">
    <div class="detail-modal" role="dialog" aria-modal="true" aria-labelledby="monitorDetailTitle">
        <div class="detail-modal-header">
            <h3 id="monitorDetailTitle">Monitoring Details</h3>
            <button type="button" class="detail-modal-close" aria-label="Close">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="detail-modal-body">
            <div class="detail-grid">
                <div class="detail-box">
                    <span class="label">Spot</span>
                    <span class="value" id="modalSpotName">—</span>
                </div>
                <div class="detail-box">
                    <span class="label">Category</span>
                    <span class="value" id="modalSpotCategory">—</span>
                </div>
                <div class="detail-box">
                    <span class="label">Owner</span>
                    <span class="value" id="modalSpotOwner">—</span>
                </div>
                <div class="detail-box">
                    <span class="label">Rating</span>
                    <span class="value" id="modalSpotRating">—</span>
                </div>
                <div class="detail-box">
                    <span class="label">Status</span>
                    <span class="value" id="modalSpotStatus">—</span>
                </div>
                <div class="detail-box">
                    <span class="label">Deadline</span>
                    <span class="value" id="modalSpotDeadline">—</span>
                </div>
            </div>

            <div class="detail-section">
                <h4>Monitoring Note</h4>
                <div id="modalMonitoringNote" class="detail-note">No monitoring note available.</div>
            </div>

            <div class="detail-section">
                <h4>Owner Update</h4>
                <div id="modalOwnerUpdate" class="detail-note">No owner update submitted yet.</div>
            </div>

            <div class="detail-section">
                <h4>Proof of Repair</h4>
                <div id="modalProofGallery" class="proof-grid-modal"></div>
                <p id="modalProofCount" style="font-size: 0.78rem; color: var(--text-muted); margin: 0.4rem 0 0;"></p>
            </div>

            <div class="detail-section" id="modalReviewSection" style="display: none;">
                <h4>Review Improvement Submission</h4>
                <div id="modalReviewNotice" class="detail-note">No pending improvement submission to review.</div>
                <form id="monitorReviewForm" class="monitor-review-form" method="post">
                    <input type="hidden" name="decision" id="reviewDecisionInput" value="approved">
                    <div class="field-group">
                        <label for="reviewReason" style="font-size: 0.75rem; font-weight: 700; color: var(--primary-dark); letter-spacing: 0.06em; text-transform: uppercase; margin-bottom: 0.2rem; display: block;">Reason / Feedback</label>
                        <textarea id="reviewReason" name="reason" placeholder="Add approval notes or explain what still needs improvement."></textarea>
                    </div>
                    <div class="monitor-review-actions">
                        <button type="button" class="btn btn-primary review-btn" data-decision="approved">
                            <i class="fa-solid fa-check"></i> Approve Update
                        </button>
                        <button type="button" class="btn btn-danger review-btn" data-decision="declined" style="background: #dc2626; color: white;">
                            <i class="fa-solid fa-xmark"></i> Decline Update
                        </button>
                    </div>
                </form>
            </div>

            <div class="detail-section">
                <h4>History</h4>
                <div id="modalHistory" class="detail-history"></div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('monitorDetailModal');
        const closeButton = modal.querySelector('.detail-modal-close');
        const spotName = document.getElementById('modalSpotName');
        const spotCategory = document.getElementById('modalSpotCategory');
        const spotOwner = document.getElementById('modalSpotOwner');
        const spotRating = document.getElementById('modalSpotRating');
        const spotStatus = document.getElementById('modalSpotStatus');
        const spotDeadline = document.getElementById('modalSpotDeadline');
        const monitoringNote = document.getElementById('modalMonitoringNote');
        const ownerUpdate = document.getElementById('modalOwnerUpdate');
        const proofGallery = document.getElementById('modalProofGallery');
        const historyContainer = document.getElementById('modalHistory');
        const reviewSection = document.getElementById('modalReviewSection');
        const reviewNotice = document.getElementById('modalReviewNotice');
        const reviewForm = document.getElementById('monitorReviewForm');
        const reviewReason = document.getElementById('reviewReason');
        const reviewDecisionInput = document.getElementById('reviewDecisionInput');

        function formatDate(value) {
            if (!value) return '—';
            const date = new Date(value);
            if (Number.isNaN(date.getTime())) return value;
            return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
        }

        function acknowledgeNotification(button) {
            const row = button ? button.closest('tr') : null;
            if (!row) return;

            row.classList.remove('warning-row');
            row.classList.add('notification-read');

            const alertBadge = row.querySelector('.warning-alert');
            if (alertBadge) {
                alertBadge.style.display = 'none';
            }
        }

        function openModal(spot) {
            spotName.textContent = spot.name || 'Unknown spot';
            spotCategory.textContent = spot.category_name || spot.category_id || 'N/A';
            spotOwner.textContent = spot.owner_name || 'Unassigned';
            spotRating.textContent = spot.avg_rating ? Number(spot.avg_rating).toFixed(1) + ' / 5' : 'No ratings yet';

            const rawStatus = spot.monitor_status || 'normal';
            const statusMap = {
                warning: 'Warning',
                under_renovation: 'Under Renovation',
                improved: 'Improved',
                normal: 'Normal'
            };
            spotStatus.textContent = statusMap[rawStatus] || 'Normal';
            spotDeadline.textContent = formatDate(spot.improvement_deadline);

            const monitoringText = spot.monitor_note ? spot.monitor_note : 'No monitoring note available.';
            const ownerText = spot.renovation_note ? spot.renovation_note : 'No owner update submitted yet.';
            monitoringNote.textContent = monitoringText;
            ownerUpdate.innerHTML = ownerText ? ownerText.replace(/\n/g, '<br>') : 'No owner update submitted yet.';

            const proofFiles = typeof spot.renovation_proof_path === 'string'
                ? spot.renovation_proof_path.split('|').map(function(file) { return file.trim(); }).filter(Boolean)
                : [];

            const proofCount = document.getElementById('modalProofCount');
            if (proofFiles.length) {
                proofGallery.innerHTML = proofFiles.map(function(filePath) {
                    const imgUrl = BASE_URL + '/' + filePath.replace(/^\//, '');
                    return '<div class="proof-thumb">' +
                        '<a href="' + imgUrl + '" target="_blank" title="Click to view full image">' +
                        '<img src="' + imgUrl + '" alt="Repair proof" loading="lazy">' +
                        '</a></div>';
                }).join('');
                if (proofCount) proofCount.textContent = proofFiles.length + ' image' + (proofFiles.length !== 1 ? 's' : '') + ' submitted';
            } else {
                proofGallery.innerHTML = '<div style="color: var(--text-muted); grid-column: 1/-1;">No proof images submitted.</div>';
                if (proofCount) proofCount.textContent = '';
            }

            const reviewStatus = spot.monitor_submission_status || 'none';
            if (reviewStatus === 'pending') {
                reviewSection.style.display = 'block';
                reviewNotice.textContent = 'A listing owner has submitted an improvement update for review.';
                reviewReason.value = '';
                reviewForm.action = BASE_URL + '/lgu/monitoring/' + spot.id + '/review';
            } else {
                reviewSection.style.display = 'none';
                reviewNotice.textContent = 'No pending improvement submission to review.';
                reviewForm.action = BASE_URL + '/lgu/monitoring/' + spot.id + '/review';
            }

            const history = Array.isArray(spot.history) ? spot.history : [];
            if (!history.length) {
                historyContainer.innerHTML = '<div class="detail-history-item">No monitoring history available.</div>';
            } else {
                historyContainer.innerHTML = history.map(function(item) {
                    const type = (item.record_type || 'event').toString();
                    const date = formatDate(item.created_at);
                    const text = item.message ? item.message : 'No description provided.';
                    const deadline = item.deadline_at ? '<div style="margin-top: 0.4rem; color: var(--text-muted); font-size: 0.8rem;">Deadline: ' + formatDate(item.deadline_at) + '</div>' : '';
                    return '<div class="detail-history-item"><div><strong>' + type.charAt(0).toUpperCase() + type.slice(1) + '</strong></div><div style="margin-top:0.25rem; color: var(--text-muted); font-size:0.8rem;">' + date + '</div><div style="margin-top:0.45rem;">' + text + '</div>' + deadline + '</div>';
                }).join('');
            }

            modal.classList.add('visible');
            modal.setAttribute('aria-hidden', 'false');
        }

        function closeModal() {
            modal.classList.remove('visible');
            modal.setAttribute('aria-hidden', 'true');
        }

        document.querySelectorAll('.view-detail-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                acknowledgeNotification(button);
                const spot = JSON.parse(button.getAttribute('data-spot'));
                openModal(spot);
            });
        });

        closeButton.addEventListener('click', closeModal);
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && modal.classList.contains('visible')) {
                closeModal();
            }
        });

        document.querySelectorAll('.review-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const decision = button.getAttribute('data-decision');
                reviewDecisionInput.value = decision;

                const confirmText = decision === 'approved'
                    ? 'Approve this improvement update and clear the monitoring deadline?'
                    : 'Decline this improvement update and notify the owner?';

                Swal.fire({
                    title: decision === 'approved' ? 'Approve update?' : 'Decline update?',
                    text: confirmText,
                    icon: decision === 'approved' ? 'success' : 'warning',
                    showCancelButton: true,
                    confirmButtonColor: decision === 'approved' ? '#16a34a' : '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: decision === 'approved' ? 'Yes, approve' : 'Yes, decline',
                    cancelButtonText: 'Cancel'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        reviewForm.submit();
                    }
                });
            });
        });

        document.querySelectorAll('.warn-owner-form').forEach(function(form) {
            form.addEventListener('submit', function(event) {
                event.preventDefault();

                Swal.fire({
                    title: 'Warn owner?',
                    text: 'This will send a formal warning and mark the listing for improvement follow-up.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f59e0b',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, send warning',
                    cancelButtonText: 'Cancel'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
