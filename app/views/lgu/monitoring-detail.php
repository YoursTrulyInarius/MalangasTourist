<?php
$title = 'Monitoring Details - LGU';
$activePage = 'monitoring';
require_once '../app/views/partials/lgu-header.php';
?>

<style>
    .monitor-detail-wrap {
        display: grid;
        gap: 1.5rem;
    }

    .detail-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #edf0ee;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.04);
        padding: 1.5rem;
    }

    .detail-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .detail-header h1 {
        margin: 0;
        color: var(--primary-dark);
        font-size: clamp(2rem, 2.4vw, 2.4rem);
        letter-spacing: -0.04em;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        padding: 0.38rem 0.8rem;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .status-normal { background: #dcfce7; color: #166534; }
    .status-warning { background: #fef3c7; color: #92400e; }
    .status-renovation { background: #dbeafe; color: #1d4ed8; }
    .status-improved { background: #e0f2fe; color: #0f766e; }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
        margin: 1.2rem 0 0;
    }

    .meta-box {
        background: #f8fafc;
        border: 1px solid #edf0ee;
        border-radius: 12px;
        padding: 0.9rem 1rem;
    }

    .meta-box .label {
        display: block;
        color: var(--text-muted);
        font-size: 0.72rem;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        margin-bottom: 0.4rem;
        font-weight: 700;
    }

    .meta-box .value {
        color: var(--text-main);
        font-weight: 600;
    }

    .info-block {
        margin-top: 1.2rem;
        border-top: 1px solid #edf0ee;
        padding-top: 1.2rem;
    }

    .info-block h3 {
        margin: 0 0 0.8rem;
        color: var(--primary-dark);
        font-size: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .note-box {
        background: #fff7f7;
        border: 1px solid #fecaca;
        border-radius: 12px;
        padding: 1rem;
        color: var(--text-main);
        line-height: 1.7;
    }

    .history-list {
        display: grid;
        gap: 0.9rem;
    }

    .history-item {
        background: #f8fafc;
        border: 1px solid #edf0ee;
        border-radius: 12px;
        padding: 0.9rem 1rem;
    }

    .history-item strong {
        color: var(--primary-dark);
    }

    .proof-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .proof-preview {
        border: 1px solid #edf0ee;
        border-radius: 12px;
        overflow: hidden;
        background: #f8fafc;
    }

    .proof-preview a {
        display: block;
        text-decoration: none;
    }

    .proof-preview img {
        display: block;
        width: 100%;
        height: 180px;
        object-fit: cover;
        transition: opacity 0.2s ease;
    }

    .proof-preview img:hover {
        opacity: 0.88;
    }
</style>

<div class="lgu-topbar">
    <div>
        <div class="lgu-topbar-title">Monitoring Details</div>
        <div class="lgu-topbar-breadcrumb">Review warning and update history for this tourist spot</div>
    </div>
</div>

<div class="lgu-page-body">
    <div class="monitor-detail-wrap">
        <div class="detail-card">
            <div class="detail-header">
                <div>
                    <h1><?= htmlspecialchars($spot['name'] ?? '') ?></h1>
                    <div style="margin-top: 0.4rem; color: var(--text-muted);">
                        <?= htmlspecialchars($spot['barangay'] ?? 'N/A') ?> · <?= htmlspecialchars($spot['category_name'] ?? 'N/A') ?>
                    </div>
                </div>
                <?php
                    $status = $spot['monitor_status'] ?? 'normal';
                    $statusClass = 'status-normal';
                    $statusLabel = 'Normal';
                    if ($status === 'warning') { $statusClass = 'status-warning'; $statusLabel = 'Warning'; }
                    elseif ($status === 'under_renovation') { $statusClass = 'status-renovation'; $statusLabel = 'Renovation'; }
                    elseif ($status === 'improved') { $statusClass = 'status-improved'; $statusLabel = 'Improved'; }
                ?>
                <span class="status-pill <?= $statusClass ?>"><?= $statusLabel ?></span>
            </div>

            <div class="detail-grid">
                <div class="meta-box">
                    <span class="label">Owner</span>
                    <span class="value"><?= htmlspecialchars($spot['owner_name'] ?? 'Unassigned') ?></span>
                </div>
                <div class="meta-box">
                    <span class="label">Email</span>
                    <span class="value"><?= htmlspecialchars($spot['owner_email'] ?? 'N/A') ?></span>
                </div>
                <div class="meta-box">
                    <span class="label">Average Rating</span>
                    <span class="value"><?= !empty($spot['avg_rating']) ? number_format((float)$spot['avg_rating'], 1) . ' / 5' : 'No ratings yet' ?></span>
                </div>
                <div class="meta-box">
                    <span class="label">Deadline</span>
                    <span class="value"><?= !empty($spot['improvement_deadline']) ? date('M d, Y', strtotime($spot['improvement_deadline'])) : '—' ?></span>
                </div>
            </div>

            <div class="info-block">
                <h3>Monitoring Note</h3>
                <div class="note-box">
                    <?= !empty($spot['monitor_note']) ? nl2br(htmlspecialchars($spot['monitor_note'])) : 'No monitoring note yet.' ?>
                </div>
            </div>

            <div class="info-block">
                <h3>Owner Update</h3>
                <div class="note-box">
                    <?= !empty($spot['renovation_note']) ? nl2br(htmlspecialchars($spot['renovation_note'])) : 'No owner update submitted yet.' ?>
                </div>

                <?php if (!empty($spot['renovation_proof_path'])): ?>
                    <?php $proofFiles = array_values(array_filter(array_map('trim', explode('|', (string)$spot['renovation_proof_path'])))); ?>
                    <?php if (!empty($proofFiles)): ?>
                        <div class="proof-grid">
                            <?php foreach ($proofFiles as $proofFile): ?>
                                <div class="proof-preview">
                                    <a href="<?= BASE_URL ?>/<?= htmlspecialchars($proofFile) ?>" target="_blank" title="Click to view full image">
                                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($proofFile) ?>" alt="Renovation proof" loading="lazy">
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <p style="font-size: 0.78rem; color: var(--text-muted); margin: 0.4rem 0 0;"><?= count($proofFiles) ?> image<?= count($proofFiles) !== 1 ? 's' : '' ?> submitted</p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="info-block">
                <h3>History</h3>
                <?php if (empty($spot['history'])): ?>
                    <div class="note-box">No monitoring history available.</div>
                <?php else: ?>
                    <div class="history-list">
                        <?php foreach ($spot['history'] as $item): ?>
                            <div class="history-item">
                                <div style="margin-bottom: 0.35rem;"><strong><?= htmlspecialchars(ucfirst($item['record_type'] ?? 'event')) ?></strong></div>
                                <div style="margin-bottom: 0.25rem; color: var(--text-muted); font-size: 0.8rem;">
                                    <?= !empty($item['created_at']) ? date('M d, Y h:i A', strtotime($item['created_at'])) : '—' ?>
                                </div>
                                <div><?= !empty($item['message']) ? nl2br(htmlspecialchars($item['message'])) : 'No description provided.' ?></div>
                                <?php if (!empty($item['deadline_at'])): ?>
                                    <div style="margin-top: 0.5rem; font-size: 0.8rem; color: var(--text-muted);">
                                        Deadline: <?= date('M d, Y', strtotime($item['deadline_at'])) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/views/partials/business-footer.php'; ?>
